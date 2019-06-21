<?php
/* Template Name: Idealhit Api SendOrderToE2 */
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
if(!is_user_logged_in()){
    //重定向浏览器
    header("Location: /do-login/");
    //确保重定向后，后续代码不会被执行
    exit;
}else{

    $action_name = 'SendOrderToE2';
    $isSuccess = false;
    $msg = '';
    $data = array();

    if(isset($_POST["order_details"])){

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
        $orderDetails = json_decode(rawurldecode($_POST["order_details"]));
        $orderDetails->mediator = $helper->getMediate();

        /**
         * 预测订单ID
         */
        $order_meta_id_predict = 0;
        $order_id_predict = '';
        global $wpdb;
        $query = "SELECT max(meta_id) as max_id FROM `ihattach_orders`";
        $max_order_id_db = $wpdb->get_results($query);
        foreach ($max_order_id_db as $row){
            $order_meta_id_predict = intval($row->max_id) + 1;
            $order_id_predict = intval($row->max_id) + 3000000 + 1;
        }
        $orderDetails->referenceId = $order_id_predict . '.' . $current_user->user_login;


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
        $requestData = array(
            'mediator' => $orderDetails->mediator,
            'referenceId' => $orderDetails->referenceId,
            'customerCompany' => $orderDetails->customerCompany,
            'customerSurname' => $orderDetails->customerSurname,
            'customerFirstname' => $orderDetails->customerFirstname,
            'customerStreet' => $orderDetails->customerStreet,
            'customerPostcode' => $orderDetails->customerPostcode,
            'customerCity' => $orderDetails->customerCity,
            'customerCountry' => $orderDetails->customerCountry,
            'customerMail' => $orderDetails->customerMail,
            'customerTelephone' => $orderDetails->customerTelephone,
            'customerShippingCompany' => $orderDetails->customerShippingCompany,
            'customerShippingSurname' => $orderDetails->customerShippingSurname,
            'customerShippingFirstname' => $orderDetails->customerShippingFirstname,
            'customerShippingStreet' => $orderDetails->customerShippingStreet,
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
            'billNr' => $orderDetails->invoiceNr,
            'customerShippingTelephone' => $orderDetails->customerShippingTelephone,
            'invoiceComment' => $orderDetails->memo_big_account,
            'afterbuyAccount' => $orderDetails->afterbuyAccount
        );

        $postFields = $requestData;

        $res_data = $helper->requestHttpApi($url, $postFields, $CURLOPT_HTTPHEADER_LIST);

        $data = $helper->xmlToJson($res_data);

        /**
         * 预测好订单ID,就可以写入日志了
         */
        $get_order_id_in_AB_1 = explode('posted a new order (', json_encode($data));
        $get_order_id_in_AB_2 = explode(')', $get_order_id_in_AB_1[1]);
        $order_id_in_AB = $get_order_id_in_AB_2[0];
        if($orderDetails->deal_with === 'ersatzteil'){
            $helper->setOperationHistory($order_meta_id_predict, 'Die Ersatzteilbestellung wurde erfolgreich an Aftrebuy gesendet. Bestellung-Nr. in Aftrebuy lautet: <a href="https://farm02.afterbuy.de/afterbuy/auktionsliste.aspx?art=edit&id=' . $order_id_in_AB . '" target="_blank">' . $order_id_in_AB . '</a>.', 4, 88888888);
        }else{
            $helper->setOperationHistory($order_meta_id_predict, 'Die Bestellung wurde erfolgreich an Aftrebuy gesendet. Bestellung-Nr. in Aftrebuy lautet: <a href="https://farm02.afterbuy.de/afterbuy/auktionsliste.aspx?art=edit&id=' . $order_id_in_AB . '" target="_blank">' . $order_id_in_AB . '</a>.', 0, 88888888);
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
        'data' => $data,
        'test-posotions' => $soldItems
    );

    echo json_encode($results);


}