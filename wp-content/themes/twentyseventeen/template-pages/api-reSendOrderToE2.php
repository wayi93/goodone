<?php
/* Template Name: Idealhit Api ReSendOrderToE2 */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/4/25
 * Time: 16:24
 */
include_once ( GET_STYLESHEET_DIRECTORY() . '/Util/Helper.php');
use SoGood\Support\Util\Helper;

global $current_user;
get_current_user();

/**
 * 判断，如果没有登陆，那么直接跳转到 Login 页面
 */
$auth_res = wp_authenticate($_POST["api_login"], $_POST["api_password"]);
if(!is_user_logged_in() && $auth_res->data->user_login != $_POST["api_login"]){
    //重定向浏览器
    header("Location: /do-login/");
    //确保重定向后，后续代码不会被执行
    exit;
}else{

    $action_name = 'ReSendOrderToE2';
    $isSuccess = false;
    $msg = '';
    $data = array();

    $db_table_orders = "ihattach_orders";
    $db_table_positions = "ihattach_positions";
    $db_table_document_nrs = "ihattach_document_nrs";

    if(isset($_POST["id_db"])){

        $id_db = $_POST["id_db"];
        $id = intval($id_db) + 3000000;

        $helper = new Helper();

        $isSuccess = true;
        $msg = "Bestellung wurde erfolgreich an E2 gesendet.";

        // parameters
        $url = $_settings_data["urls"]["Api_Url_Post_Order"];
        $authorization = base64_encode($_settings_data["server-info"]["Entelliship_UserID"] . ":" . $_settings_data["server-info"]["Entelliship_Password"]);
        $CURLOPT_HTTPHEADER_LIST = array(
            'Authorization: Basic ' . $authorization,
            'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
            'Accept: application/xml'
        );


        global $wpdb;
        /**
         * 订单数据
         */
        $query = "SELECT * FROM `" . $db_table_orders . "` WHERE `meta_id` = %d";
        $order_main_infos_db = $wpdb->get_results($wpdb->prepare($query, $id_db));
        $order_main_infos = $order_main_infos_db[0];

        $creator_info = get_userdata($order_main_infos->create_by);

        $orderDetails = array(
            //"mediator" => $helper->getMediate(),
            "mediator" => $order_main_infos->mediate,
            //"referenceId" => $current_user->user_login,
            "referenceId" => strval($id) . '.' . $creator_info->user_login,
            "customerCompany" => $order_main_infos->customer_company,
            "customerSurname" => $order_main_infos->customer_lastName,
            "customerFirstname" => $order_main_infos->customer_firstName,
            "customerStreet" => $order_main_infos->customer_street,
            "customerStreet1" => $order_main_infos->customer_street1,
            "customerPostcode" => $order_main_infos->customer_postalCode,
            "customerCity" => $order_main_infos->customer_city,
            "customerCountry" => $order_main_infos->customer_country,
            "customerMail" => $order_main_infos->goodone_customer_mail,
            "customerTelephone" => $order_main_infos->customer_phone,
            "customerShippingCompany" => $order_main_infos->customer_shipping_company,
            "customerShippingSurname" => $order_main_infos->customer_shipping_lastName,
            "customerShippingFirstname" => $order_main_infos->customer_shipping_firstName,
            "customerShippingStreet" => $order_main_infos->customer_shipping_street,
            "customerShippingStreet1" => $order_main_infos->customer_shipping_street1,
            "customerShippingPostcode" => $order_main_infos->customer_shipping_postalCode,
            "customerShippingCity" => $order_main_infos->customer_shipping_city,
            "customerShippingCountry" => $order_main_infos->customer_shipping_country,
            "paymentMethod" => $order_main_infos->payment_method,
            "paidSum" => $order_main_infos->paidSum,
            "shippingMethod" => $order_main_infos->shipping_method,
            "soldItems" => array(
                "items" => array()
            ),
            "memo" => $order_main_infos->memo,
            'customerShippingTelephone' => $order_main_infos->customer_shipping_phone,
            'invoiceComment' => $order_main_infos->memo_big_account
        );

        /**
         * 读取账单号码
         */
        $billNr = "";
        $query_billNr = "SELECT * FROM `" . $db_table_document_nrs . "` WHERE `order_id` = %d AND `type` = 'Rechnung'";
        $billNr_db = $wpdb->get_results($wpdb->prepare($query_billNr, $id_db));
        if(COUNT($billNr_db) > 0){
            for($i=0; $i<COUNT($billNr_db); ++$i){
                $billNr = $billNr_db[$i]->number;
            }
        }

        /**
         * 读取数据库load订单Positions数据
         */
        $query_pos = "SELECT * FROM `" . $db_table_positions . "` WHERE `order_id` = %d";
        $order_poss_db = $wpdb->get_results($wpdb->prepare($query_pos, $id_db));

        $newPaidSum = 0;

        $items = array();
        if(COUNT($order_poss_db) > 0){
            for($i=0; $i<COUNT($order_poss_db); ++$i){

                $item = array(
                    "ean" => $order_poss_db[$i]->ean,
                    "qInCart" => $order_poss_db[$i]->quantity_want,
                    "price" => $order_poss_db[$i]->price,
                    "title" => $order_poss_db[$i]->title,
                    "tax" => $order_poss_db[$i]->tax
                );
                array_push($items, $item);

                $newPaidSum = floatval($newPaidSum) + floatval($order_poss_db[$i]->price);

            }
        }
        $orderDetails["soldItems"]["items"] = $items;

        $newPaidSumBrutto = $newPaidSum * (1 + ($order_main_infos->tax / 100));
        $orderDetails["paidSum"] = number_format(floatval($newPaidSumBrutto), 2, ".", "");

        $orderDetails = json_decode(strval(json_encode($orderDetails)));

        /**
         * 按照老孙的API要求
         * 整理数据的格式
         */
        $soldItems = '{"items":[';
        $soldItemsGoodOne = $orderDetails->soldItems->items;
        for($i=0; $i<COUNT($soldItemsGoodOne); ++$i){
            $sItm = $soldItemsGoodOne[$i];
            if($i > 0){
                $soldItems .= ',';
            }
            $bruttoPrice = floatval($sItm->price) * (floatval($sItm->tax) / 100 + 1);
            $bruttoPrice = number_format($bruttoPrice,2,".","");
            $soldItems = $soldItems . '{"ean":"' . $sItm->ean . '","quantity":"' . $sItm->qInCart . '","price":"' . $bruttoPrice . '","comment":"' . $sItm->title . '","tax":"' . $sItm->tax . '"}';
        }
        $soldItems .= ']}';
        $customerStreet = $orderDetails->customerStreet;
        if(strlen($orderDetails->customerStreet1) > 0){
            $customerStreet = $customerStreet . " (" . $orderDetails->customerStreet1 . ")";
        }
        $customerShippingStreet = $orderDetails->customerShippingStreet;
        if(strlen($orderDetails->customerShippingStreet1) > 0){
            $customerShippingStreet = $customerShippingStreet . " (" . $orderDetails->customerShippingStreet1 . ")";
        }
        $requestData = array(
            'mediator' => $orderDetails->mediator,
            'referenceId' => $orderDetails->referenceId,
            'customerCompany' => $orderDetails->customerCompany,
            'customerSurname' => $orderDetails->customerSurname,
            'customerFirstname' => $orderDetails->customerFirstname,
            'customerStreet' => $customerStreet,
            'customerPostcode' => $orderDetails->customerPostcode,
            'customerCity' => $orderDetails->customerCity,
            'customerCountry' => $orderDetails->customerCountry,
            'customerMail' => $orderDetails->customerMail,
            'customerTelephone' => $orderDetails->customerTelephone,
            'customerShippingCompany' => $orderDetails->customerShippingCompany,
            'customerShippingSurname' => $orderDetails->customerShippingSurname,
            'customerShippingFirstname' => $orderDetails->customerShippingFirstname,
            'customerShippingStreet' => $customerShippingStreet,
            'customerShippingPostcode' => $orderDetails->customerShippingPostcode,
            'customerShippingCity' => $orderDetails->customerShippingCity,
            'customerShippingCountry' => $orderDetails->customerShippingCountry,
            'paymentMethod' => $orderDetails->paymentMethod,
            'paymentDetails' => '',
            'paidSum' => $orderDetails->paidSum,
            'shippingMethod' => $orderDetails->shippingMethod,
            'shippingDatails' => '',
            'soldItems' => $soldItems,
            'comment' => $orderDetails->memo,
            'billNr' => $billNr,
            'customerShippingTelephone' => $orderDetails->customerShippingTelephone,
            'invoiceComment' => $orderDetails->invoiceComment,
            'afterbuyAccount' => $orderDetails->afterbuyAccount
        );

        $postFields = $requestData;

        $res_data = $helper->requestHttpApi($url, $postFields, $CURLOPT_HTTPHEADER_LIST);

        $data = $helper->xmlToJson($res_data);

        /**
         * 可以写入日志了
         */
        $get_order_id_in_AB_1 = explode('posted a new order (', json_encode($data));
        $get_order_id_in_AB_2 = explode(')', $get_order_id_in_AB_1[1]);
        $order_id_in_AB = $get_order_id_in_AB_2[0];
        if($orderDetails->deal_with === 'ersatzteil'){
            $helper->setOperationHistory($id_db, 'Die Ersatzteilbestellung wurde erfolgreich an Aftrebuy gesendet. Bestellung-Nr. in Aftrebuy lautet: <a href="https://farm02.afterbuy.de/afterbuy/auktionsliste.aspx?art=edit&id=' . $order_id_in_AB . '" target="_blank">' . $order_id_in_AB . '</a>.', 4, 88888888);
        }else {
            $helper->setOperationHistory($id_db, 'Die Bestellung wurde erfolgreich an Aftrebuy gesendet. Bestellung-Nr. in Aftrebuy lautet: <a href="https://farm02.afterbuy.de/afterbuy/auktionsliste.aspx?art=edit&id=' . $order_id_in_AB . '" target="_blank">' . $order_id_in_AB . '</a>.', 0, 88888888);
        }

    }else{

        $isSuccess = false;
        $msg = 'Missing parameters!';

    }


    $results = array(
        'action' => $action_name,
        'isSuccess' => $isSuccess,
        'msg' => $msg,
        'data_quantity' => COUNT($data),
        'data' => $data
    );

    echo json_encode($results);


}