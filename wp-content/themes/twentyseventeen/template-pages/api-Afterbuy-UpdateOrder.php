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
 * $type        0: update invoice info | 1: update customer address | 2: update PaymentMethod
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


    if(isset($_POST["new_order_details"]))
    {

        $orderDetails = json_decode(rawurldecode($_POST["new_order_details"]));

        $type = intval($orderDetails->type);
        $response = array();

        switch ($type)
        {
            case 0:
                //
                break;
            case 1:
                //
                break;
            case 2:
                $afterbuyOrderManager = new AfterbuyOrderManager($orderDetails->afterbuy_account);
                $afterbuyOrderManager->setPaymentMethod($orderDetails->payment_method);
                $response = $afterbuyOrderManager->update($orderDetails->order_id_ab_original, $type);
                break;
            default:
                //
        }

        if($response['isSuccess']){

            $isSuccess = true;
            $msg = $response['data']['AID'];

        }else{

            $isSuccess = false;
            $msg = $response['errorlist']['error'];

        }

        // http://www.ying.com/api/afterbuy-updateorder/
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