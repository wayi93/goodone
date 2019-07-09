<?php
/* Template Name: Idealhit Api Afterbuy UpdateOrder */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/3/19
 * Time: 17:07
 * https://goodone.maimai24.de/api/afterbuy-updateorder/
 */
$twentyseventeenDir = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'twentyseventeen';
require_once ( $twentyseventeenDir . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR . 'AfterbuyOrderManager.php' );
//require_once ( $twentyseventeenDir . DIRECTORY_SEPARATOR . 'Util' . DIRECTORY_SEPARATOR . 'Helper.php');
use SoGood\Support\Model\AfterbuyOrderManager;
//use SoGood\Support\Util\Helper;

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

    $action_name = 'UpdateAfterbuyOrder';
    $isSuccess = false;
    $msg = '';
    $data = array();

    //if(isset($_POST["order_details"]))
    if(true)
    {

        $isSuccess = true;

        /*

        $orderDetails = json_decode(rawurldecode($_POST["order_details"]));

        $afterbuyOrderManager = new AfterbuyOrderManager($orderDetails->afterbuyAccount);

        $afterbuyOrderManager->setCustomerCompany($orderDetails->customerCompany);
        $afterbuyOrderManager->setCustomerSurname($orderDetails->customerSurname);
        $afterbuyOrderManager->setCustomerFirstname($orderDetails->customerFirstname);
        $afterbuyOrderManager->setCustomerStreet($orderDetails->customerStreet);
        $afterbuyOrderManager->setCustomerPostcode($orderDetails->customerPostcode);
        $afterbuyOrderManager->setCustomerCity($orderDetails->customerCity);
        $afterbuyOrderManager->setCustomerCountry($orderDetails->customerCountry);
        $afterbuyOrderManager->setCustomerCountryName($orderDetails->customerCountryName);
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

        */

        $afterbuyOrderManager = new AfterbuyOrderManager('sogood');
        $afterbuyOrderManager->setIsAddressSame(true);
        $afterbuyOrderManager->setCustomerCompany('Invoice Sogood');
        $afterbuyOrderManager->setCustomerSurname('Invoice Wang');
        $afterbuyOrderManager->setCustomerFirstname('Invoice Ying');
        $afterbuyOrderManager->setCustomerStreet('Invoice Karl-Benz-Straße 1');
        $afterbuyOrderManager->setCustomerPostcode('Invoice 63128');
        $afterbuyOrderManager->setCustomerCity('Invoice Dietzenbach');
        $afterbuyOrderManager->setCustomerCountry('DE');
        $afterbuyOrderManager->setCustomerCountryName('Deutschland');
        $afterbuyOrderManager->setCustomerTelephone('Invoice 017645613006');
        $afterbuyOrderManager->setCustomerShippingCompany('Shipping Sogood');
        $afterbuyOrderManager->setCustomerShippingSurname('Shipping Wang');
        $afterbuyOrderManager->setCustomerShippingFirstname('Shipping Ying');
        $afterbuyOrderManager->setCustomerShippingStreet('Shipping Karl-Benz-Straße 1');
        $afterbuyOrderManager->setCustomerShippingPostcode('Shipping 63128');
        $afterbuyOrderManager->setCustomerShippingCity('Shipping Dietzenbach');
        $afterbuyOrderManager->setCustomerShippingCountry('DE');
        $afterbuyOrderManager->setCustomerShippingCountryName('Deutschland');
        $afterbuyOrderManager->setCustomerShippingTelephone('Shipping 017645613006');








        $response = $afterbuyOrderManager->update('1205117339', 1);


        var_dump($response);


        if($response['success'] == 1){

            $isSuccess = true;
            $msg = $response['data']['AID'];

        }else{

            $isSuccess = false;
            $msg = $response['errorlist']['error'];

        }

        // http://www.ying.com/api/afterbuy-createorder/
        $data['api-response'] = $response;


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