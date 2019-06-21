<?php
/* Template Name: Idealhit Document Page */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/6/25
 * Time: 14:50
 */
include_once ( GET_STYLESHEET_DIRECTORY() . '/Util/Helper.php');
use SoGood\Support\Util\Helper;
$helper = new Helper();

/**
 * 得到当前用户信息, 并且根据不同的用户组跳转不同的页面
 */
global $current_user;
get_current_user();
$userGroup = $current_user->roles[0];

$url = home_url(add_query_arg(array()));
$url_list = explode("/document/", $url);
$id = substr($url_list[1], 0, 7);
$id_db = intval($id) - 3000000;
$doc_typ = substr($url_list[1], 7, 1);
$doc_typ_title = '';
$doc_typ_title_safe = '';
$docNo = $id;

$db_table_orders = "ihattach_orders";
$db_table_positions = "ihattach_positions";
$db_table_document_nrs = "ihattach_document_nrs";


/**
 * 判断，如果没有登陆，那么直接跳转到 Login 页面
 */
if(!is_user_logged_in()){header("Location: /do-login.php");exit;}

/**
 * 判断 doc_typ 不是 0 1 2 3 就跳出
 */
switch ($doc_typ){
    case "0":
        $doc_typ_title = 'Angebot';
        $doc_typ_title_safe = 'Angebot';
        break;
    case "1":
        $doc_typ_title = 'Auftragsbestätigung';
        $doc_typ_title_safe = 'Auftragsbestaetigung';
        break;
    case "2":
        $doc_typ_title = 'Lieferschein';
        $doc_typ_title_safe = 'Lieferschein';
        break;
    case "3":
        $doc_typ_title = 'Rechnung';
        $doc_typ_title_safe = 'Rechnung';

        /**
         * 如果已经生成了AfterbuyID，那么就使用Afterbuy的账单号
         * 如果没有AfterbuyID，就从老孙的API取账单号
         */
        //$docNo = $id_db + 18070;

        break;
    default:
        header("Location: /404/");
        exit;
}


if(strlen($url_list[1]) > 10){

    header("Location: /404/");
    exit;

}else{

    /**
     * 读取数据库load订单主表数据
     */
    global $wpdb;

    // 主表信息查询
    $query = "SELECT * FROM `" . $db_table_orders . "` WHERE `meta_id` = %d";
    $order_main_infos_db = $wpdb->get_results($wpdb->prepare($query, $id_db));

    if(COUNT($order_main_infos_db) < 1){

        header("Location: /404/");exit;

    }else {

        // 从数据库中拿订单主表数据
        $order_main_infos = $order_main_infos_db[0];
        $order_tax = intval($order_main_infos->tax);
        $order_paidSum = floatval($order_main_infos->paidSum);
        $order_paidTax = number_format(($order_paidSum * $order_tax / (100 + $order_tax)),2,".","");
        $order_goodone_meta_id = $order_main_infos->meta_id;
        $order_id_ab = $order_main_infos->order_id_ab;
        $order_goodone_id = strval(intval($order_goodone_meta_id) + 3000000);
        $order_verifi_code = substr(strval($order_main_infos->create_at), 8, 2);
        $paymentMethod = $order_main_infos->payment_method;
        $paymentDetails = '';
        if($paymentMethod == 'Paypal'){
            $paymentDetails = 'https://www.sogood.de/api/do-paypal.php?token=' . $order_goodone_id . $order_verifi_code;
        }
        $docDate = date("d.m.Y", $order_main_infos->create_at);
        $dealWith = $order_main_infos->deal_with;
        $afterbuyAccount = $order_main_infos->afterbuy_account;
        if($afterbuyAccount === ''){
            $afterbuyAccount = 'maimai';
        }
        $is_sogood = true;
        if($afterbuyAccount === 'maimai'){
            $is_sogood = false;
        }


        /**
         * 写入历史操作记录
         */
        if($dealWith === 'ersatzteil'){
            switch ($doc_typ){
                case "0":
                    $helper->setOperationHistory($id_db, 'Das PDF-Angebot wurde erstellt.', 4, 0);
                    break;
                case "1":
                    $helper->setOperationHistory($id_db, 'Die PDF-Auftragsbestätigung wurde erstellt.', 4, 0);
                    break;
                case "2":
                    $helper->setOperationHistory($id_db, 'Der PDF-Lieferschein wurde erstellt.', 4, 0);
                    break;
                case "3":
                    $helper->setOperationHistory($id_db, 'Die PDF-Rechnung wurde erstellt.', 4, 0);
                    break;
                default:
                    //
            }
        }else{
            switch ($doc_typ){
                case "0":
                    $helper->setOperationHistory($id_db, 'Das PDF-Angebot wurde erstellt.', 1, 0);
                    break;
                case "1":
                    $helper->setOperationHistory($id_db, 'Die PDF-Auftragsbestätigung wurde erstellt.', 0, 0);
                    break;
                case "2":
                    $helper->setOperationHistory($id_db, 'Der PDF-Lieferschein wurde erstellt.', 0, 0);
                    break;
                case "3":
                    $helper->setOperationHistory($id_db, 'Die PDF-Rechnung wurde erstellt.', 0, 0);
                    break;
                default:
                    //
            }
        }


        /**
         * 查询并更新 账单ID
         * ----------------
         * 如果账单表里面没有Rechnungsnr，那么直接显示 N/A
         */
        if($doc_typ == 3){

            $query_invoice_nr = "SELECT * FROM `" . $db_table_document_nrs . "` WHERE `order_id` = %d AND `type` = 'Rechnung'";
            $order_invoice_nr_db = $wpdb->get_results($wpdb->prepare($query_invoice_nr, $id_db));

            if(COUNT($order_invoice_nr_db) < 1){
                $docNo = "N/A";
                // $docDate
            }else{

                // 从数据库中拿订单主表数据
                $order_invoice_nr = $order_invoice_nr_db[0];
                $docNo = $order_invoice_nr->number;

                if(intval($order_invoice_nr->update_by) < 1){
                    $new_update_at = time() + ( $_settings_data['server-info']['gmt_offset'] * 3600 );

                    // 更新时间写入数据库  $current_user->ID
                    $query_update_invoice_table = "UPDATE `" . $db_table_document_nrs . "` SET `update_at` = %f, `update_by` = %d WHERE order_id = %d AND `type` = 'Rechnung'";
                    $wpdb->query($wpdb->prepare($query_update_invoice_table, $new_update_at, $current_user->ID, $order_goodone_meta_id));

                    $docDate = date("d.m.Y", $new_update_at);
                }else{
                    $docDate = date("d.m.Y", $order_invoice_nr->update_at);
                }

            }

        }

		$customerShippingTelephone = $order_main_infos->customer_shipping_phone;
		if(strlen($customerShippingTelephone) < 1){
			$customerShippingTelephone = 'Keine Tel.-Nr. hinterlegt.';
		}

        // parameters
        $url_pdf = $_settings_data["urls"]["Api_Url_PDF"];
        $authorization = base64_encode($_settings_data["server-info"]["Entelliship_UserID"] . ":" . $_settings_data["server-info"]["Entelliship_Password"]);
        $CURLOPT_HTTPHEADER_LIST = array(
            'Authorization: Basic ' . $authorization,
            'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
        );
        $requestData = array(
            'type' => $doc_typ,
            'is_sogood' => $is_sogood,
            'order_id_ab' => $order_id_ab,
            'platform_id' => $order_goodone_id,
            'order_date' => date("d.m.Y H:i:s", $order_main_infos->create_at),
            'tax' => $order_tax,
            'paidTax' => $order_paidTax,
            'mediator' => $helper->getMediate(),
            'referenceId' => $id . '.' . $current_user->user_login,
            'customerCompany' => $order_main_infos->customer_company,
            'customerSurname' => $order_main_infos->customer_lastName,
            'customerFirstname' => $order_main_infos->customer_firstName,
            'customerStreet' => $order_main_infos->customer_street,
            'customerPostcode' => $order_main_infos->customer_postalCode,
            'customerCity' => $order_main_infos->customer_city,
            'customerCountry' => $order_main_infos->customer_country,
            'customerMail' => $order_main_infos->goodone_customer_mail,
            'customerTelephone' => $order_main_infos->customer_phone,
            'customerShippingCompany' => $order_main_infos->customer_shipping_company,
            'customerShippingSurname' => $order_main_infos->customer_shipping_lastName,
            'customerShippingFirstname' => $order_main_infos->customer_shipping_firstName,
            'customerShippingStreet' => $order_main_infos->customer_shipping_street,
            'customerShippingPostcode' => $order_main_infos->customer_shipping_postalCode,
            'customerShippingCity' => $order_main_infos->customer_shipping_city,
            'customerShippingCountry' => $order_main_infos->customer_shipping_country,
            'paymentMethod' => $paymentMethod,
            'paymentDetails' => $paymentDetails,
            'paidSum' => $order_main_infos->paidSum,
            'shippingMethod' => $order_main_infos->shipping_method,
            'shippingDatails' => '',
            'soldItems' => '',
            'docNo' => $docNo,
            'docDate' => $docDate,

            // memo_big_account und comment here mean the Rechnungsvermerk
            'comment' => str_replace("<br>", "<br/>", $order_main_infos->memo_big_account),
            // memo in Afterbuy
            'memo_ab' => str_replace("<br>", "<br/>", $order_main_infos->memo),

            'shippingDate' => $order_main_infos->expected_delivery_date,
            //'comment_customer' => $order_main_infos->memo_big_account,
            'show_customer_name_in_doc' => $order_main_infos->show_customer_name_in_doc,
            'vat_nr' => $order_main_infos->vat_nr,
            'arbeitsort' => $helper->getWorkplaceByUserId($order_main_infos->create_by),
			'customerShippingTelephone' => $customerShippingTelephone,
            'afterbuy_account' => $afterbuyAccount,
            'deal_with' => $order_main_infos->deal_with
        );

        if($requestData["order_id_ab"] != "N/A" || strlen($requestData["order_id_ab"]) > 7){
            $requestData["platform_id"] = $requestData["order_id_ab"];
        }

        /**
         * 读取Position数据
         */
        $query_pos = "SELECT * FROM `" . $db_table_positions . "` WHERE `order_id` = %d";
        $order_poss_db = $wpdb->get_results($wpdb->prepare($query_pos, $id_db));

        /**
         * 整理Position数据
         */
        $soldItems = '{"items":[';
        $soldItemsGoodOne = $order_poss_db;
        for($i=0; $i<COUNT($soldItemsGoodOne); ++$i){
            $sItm = $soldItemsGoodOne[$i];
            if($i > 0){
                $soldItems .= ',';
            }
            $bruttoPrice = floatval($sItm->price) * (floatval($sItm->tax) / 100 + 1);
            $bruttoPrice = number_format($bruttoPrice,2,".","");
            $soldItems = $soldItems . '{"ean":"' . $sItm->ean . '","quantity":"' . $sItm->quantity_want . '","price":"' . $bruttoPrice . '","comment":"' . $sItm->title . '","tax":"' . $sItm->tax . '"}';
        }
        $soldItems .= ']}';
        $requestData['soldItems'] = $soldItems;


        //echo "<h1>" . $doc_typ_title . "</h1>";
        //var_dump($requestData);
        //die('test');


        // 呼唤API
        $res_data = $helper->requestHttpApi($url_pdf, $requestData, $CURLOPT_HTTPHEADER_LIST);


        // 打开PDF文件流
        $pdf_save_path = __DIR__ . "/../../../uploads/document/";
        $pdf_file_name = $doc_typ_title_safe . "-" . $id . ".pdf";
        $pdfFile = fopen($pdf_save_path . "/" . $pdf_file_name, "w") or die("Unable to open file!");
        fwrite($pdfFile, $res_data);
        fclose($pdfFile);

        //var_dump($requestData);
        // 打开PDF
        header("Location: /wp-content/uploads/document/" . $pdf_file_name);
        //确保重定向后，后续代码不会被执行
        exit;



    }
}