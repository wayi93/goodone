<?php
/* Template Name: Idealhit Api GetAfterbuyOrderById */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/3/19
 * Time: 17:07
 */
/*
ini_set('display_errors',1);            //错误信息
ini_set('display_startup_errors',1);    //php启动错误信息
error_reporting(-1);                    //打印出所有的 错误信息
*/
$root_path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
require_once ( $root_path . '/wp-content/themes/twentyseventeen/Util/AfterbuyApi.php');
use SoGood\Support\Util\AfterbuyApi;

/**
 * 参数
 * $abKonto
 * $abOrderId
 */

/**
 * 判断，如果没有登陆，那么直接跳转到 Login 页面
 */
if(!is_user_logged_in()){
    //重定向浏览器
    header("Location: /do-login/");
    //确保重定向后，后续代码不会被执行
    exit;
}else{

    $action_name = 'GetAfterbuyOrderById';
    $isSuccess = false;
    $msg = '';
    $data = array();

    if(isset($_POST["abKonto"]) && isset($_POST["abOrderId"]))
    {

        /**
         * URL Parameters
         */
        $abKonto = $_POST["abKonto"];
        $abOrderId = $_POST["abOrderId"];

        /**
         * Config Parameters
         */
        $url = $_settings_data["urls"]["Api_Url_Afterbuy"];
        $partnerId = $_settings_data["afterbuy-konto"][$abKonto."_partnerId"];
        $partnerPw = $_settings_data["afterbuy-konto"][$abKonto."_partnerPw"];
        $userId = $_settings_data["afterbuy-konto"][$abKonto."_userId"];
        $userPw = $_settings_data["afterbuy-konto"][$abKonto."_userPw"];

        $api = new AfterbuyApi();
        $api->setParams($url, $partnerId, $partnerPw, $userId, $userPw);

        $detaillevel = 0;
        $filterVals = array(
            array(
                "FilterName" => "OrderID",
                "FilterValues" => $abOrderId
            )
        );

        $result = $api->getSoldItems($detaillevel, $filterVals);

        if($result->CallStatus == "Success"){
            $success = 1;
            $output = array();
            $o_id = '';

            $soldItems = $result->Result->Orders->Order;

            $soldItemsNr = COUNT($soldItems);
            if($soldItemsNr < 1){
                $msg = "Keine Bestellung gefunden.";
            }else if($soldItemsNr > 1){
                $msg = COUNT($soldItems) . " Bestellungen wurden geladen.";
            }else{
                $msg = "1 Bestellung wurden geladen.";
            }

            foreach($soldItems as $sdTms){

                $outputItm = array();

                // Kunden Info
                $outputItm["BillingAddress"] = array(
                    // BillingAddress
                    'Company' => (string)$sdTms->BuyerInfo->BillingAddress->Company,
                    'FirstName' => (string)$sdTms->BuyerInfo->BillingAddress->FirstName,
                    'LastName' => (string)$sdTms->BuyerInfo->BillingAddress->LastName,
                    'Street' => (string)$sdTms->BuyerInfo->BillingAddress->Street,
                    'Street2' => (string)$sdTms->BuyerInfo->BillingAddress->Street2,
                    'PostalCode' => (string)$sdTms->BuyerInfo->BillingAddress->PostalCode,
                    'City' => (string)$sdTms->BuyerInfo->BillingAddress->City,
                    'CountryISO' => (string)$sdTms->BuyerInfo->BillingAddress->CountryISO,
                    'Phone' => (string)$sdTms->BuyerInfo->BillingAddress->Phone,
                    // eMail
                    'Mail' => (string)$sdTms->BuyerInfo->BillingAddress->Mail
                );

                $outputItm["IsBAddrSAddrNotSame"] = property_exists($sdTms->BuyerInfo, 'ShippingAddress');
                if($outputItm["IsBAddrSAddrNotSame"]){
                    // DeliveryAddress
                    $outputItm["ShippingAddress"] = array(
                        'Company' => (string)$sdTms->BuyerInfo->ShippingAddress->Company,
                        'FirstName' => (string)$sdTms->BuyerInfo->ShippingAddress->FirstName,
                        'LastName' => (string)$sdTms->BuyerInfo->ShippingAddress->LastName,
                        'Street' => (string)$sdTms->BuyerInfo->ShippingAddress->Street,
                        'Street2' => (string)$sdTms->BuyerInfo->ShippingAddress->Street2,
                        'PostalCode' => (string)$sdTms->BuyerInfo->ShippingAddress->PostalCode,
                        'City' => (string)$sdTms->BuyerInfo->ShippingAddress->City,
                        'CountryISO' => (string)$sdTms->BuyerInfo->ShippingAddress->CountryISO,
                        'Phone' => (string)$sdTms->BuyerInfo->ShippingAddress->Phone
                    );
                }else{
                    $outputItm["ShippingAddress"] = $outputItm["BillingAddress"];
                }


                $o_id = (string)$sdTms->OrderID;
                $outputItm["OrderID"] = (string)$sdTms->OrderID;
                $outputItm["OrderDate"] = (string)$sdTms->OrderDate;
                $outputItm["InvoiceNumber"] = (string)$sdTms->InvoiceNumber;
                $outputItm["AlreadyPaid"] = (string)$sdTms->PaymentInfo->AlreadyPaid;
                $outputItm["FullAmount"] = (string)$sdTms->PaymentInfo->FullAmount;

                // 整理所有的EAN 开始
                $sItms = $sdTms->SoldItems->SoldItem;
                $outputItm["EANs"] = "";
                foreach ($sItms as $sIs){
                    $outputItm["EANs"] .= $sIs->ShopProductDetails->Anr;
                    $outputItm["EANs"] .= ",";
                }
                //$outputItm["EANs"] = substr($outputItm["EANs"],0,strlen($outputItm["EANs"])-1);
                // 整理所有的EAN 结束

                array_push($output, $outputItm);

            }

            $data = $output;

            if($o_id === $abOrderId){
                $isSuccess = true;
            }else{
                $msg = "Keine Bestellung gefunden.";
                $data = array();
                $isSuccess = false;
            }



        }else{

            $msg = "Keine Bestellung gefunden.";
            $isSuccess = false;

        }

    }else{
        $isSuccess = false;
        $msg = 'Parameters not found!';
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