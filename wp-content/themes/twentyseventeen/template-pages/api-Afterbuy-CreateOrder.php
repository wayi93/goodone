<?php
/* Template Name: Idealhit Api Afterbuy CreateOrder */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/3/19
 * Time: 17:07
 */
$root_path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
require_once ( $root_path . '/wp-content/themes/twentyseventeen/Model/AfterbuyOrderManager.php' );
use SoGood\Support\Model\AfterbuyOrderManager;
include_once ( GET_STYLESHEET_DIRECTORY() . '/Util/Helper.php');
use SoGood\Support\Util\Helper;

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

    $action_name = 'CreateAfterbuyOrder';
    $isSuccess = false;
    $msg = '';
    $data = array();

    if(isset($_POST["order_details"]))
    {

        $isSuccess = true;

        $helper = new Helper();

        $orderDetails = json_decode($helper->deFilterParamDangerousChars(rawurldecode($_POST["order_details"])));
        $orderDetails->mediator = $helper->getMediate();

        $afterbuyOrderManager = new AfterbuyOrderManager($orderDetails->afterbuyAccount);

        $afterbuyOrderManager->setEKundenNr($orderDetails->afterbuy_customer_id);

        $afterbuyOrderManager->setMediator($orderDetails->mediator);
        $afterbuyOrderManager->setReferenceId($orderDetails->referenceId);

        $afterbuyOrderManager->setCustomerCompany($orderDetails->customerCompany);
        $afterbuyOrderManager->setCustomerSurname($orderDetails->customerSurname);
        $afterbuyOrderManager->setCustomerFirstname($orderDetails->customerFirstname);
        $afterbuyOrderManager->setCustomerStreet($orderDetails->customerStreet);
        $afterbuyOrderManager->setCustomerPostcode($orderDetails->customerPostcode);
        $afterbuyOrderManager->setCustomerCity($orderDetails->customerCity);
        $afterbuyOrderManager->setCustomerCountry($orderDetails->customerCountry);
        $afterbuyOrderManager->setCustomerCountryName($orderDetails->customerCountryName);
        $afterbuyOrderManager->setCustomerMail($orderDetails->customerMail);
        $afterbuyOrderManager->setCustomerTelephone($orderDetails->customerTelephone);
        $afterbuyOrderManager->setCustomerShippingCompany($orderDetails->customerShippingCompany);
        $afterbuyOrderManager->setCustomerShippingSurname($orderDetails->customerShippingSurname);
        $afterbuyOrderManager->setCustomerShippingFirstname($orderDetails->customerShippingFirstname);
        $afterbuyOrderManager->setCustomerShippingStreet($orderDetails->customerShippingStreet);
        $afterbuyOrderManager->setCustomerShippingPostcode($orderDetails->customerShippingPostcode);
        $afterbuyOrderManager->setCustomerShippingCity($orderDetails->customerShippingCity);
        $afterbuyOrderManager->setCustomerShippingCountry($orderDetails->customerShippingCountry);
        $afterbuyOrderManager->setCustomerShippingCountryName($orderDetails->customerShippingCountryName);
        $afterbuyOrderManager->setCustomerShippingTelephone($orderDetails->customerShippingTelephone);

        if(
            $orderDetails->customerCompany === $orderDetails->customerShippingCompany &&
            $orderDetails->customerSurname === $orderDetails->customerShippingSurname &&
            $orderDetails->customerFirstname === $orderDetails->customerShippingFirstname &&
            $orderDetails->customerStreet === $orderDetails->customerShippingStreet &&
            $orderDetails->customerPostcode === $orderDetails->customerShippingPostcode &&
            $orderDetails->customerCity === $orderDetails->customerShippingCity &&
            $orderDetails->customerCountryName === $orderDetails->customerShippingCountryName &&
            $orderDetails->customerTelephone === $orderDetails->customerShippingTelephone
        )
        {
            $afterbuyOrderManager->setIsAddressSame(true);
        }else{
            $afterbuyOrderManager->setIsAddressSame(false);
        }

        $afterbuyOrderManager->setPaymentMethod($orderDetails->paymentMethod);
        $afterbuyOrderManager->setShippingMethod($orderDetails->shippingMethod);
        $afterbuyOrderManager->setSoldItems($orderDetails->soldItems);
        $afterbuyOrderManager->setComment($orderDetails->memo);
        $afterbuyOrderManager->setBillNr($orderDetails->invoiceNr);
        $afterbuyOrderManager->setInvoiceComment($orderDetails->memo_big_account);

        $createResponse = $afterbuyOrderManager->create();

        if($createResponse['success'] == 1){

            $isSuccess = true;
            $msg = $createResponse['data']['AID'];

        }else{

            $isSuccess = false;
            $msg = $createResponse['errorlist']['error'];

        }

        // http://www.ying.com/api/afterbuy-createorder/
        $data['api-response'] = $createResponse;


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