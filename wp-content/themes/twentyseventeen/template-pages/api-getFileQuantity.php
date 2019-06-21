<?php
/* Template Name: Idealhit Api GetFileQuantity */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/4/25
 * Time: 16:24
 */
include_once ( GET_STYLESHEET_DIRECTORY() . '/Util/Helper.php');
use SoGood\Support\Util\Helper;

/**
 * 判断，如果没有登陆，那么直接跳转到 Login 页面
 */
if(!is_user_logged_in()){
    //重定向浏览器
    header("Location: /do-login/");
    //确保重定向后，后续代码不会被执行
    exit;
}else{

    $action_name = 'GetFileQuantity';
    $isSuccess = false;
    $msg = '';
    $data = array();

    if(isset($_POST["orderId"])){
        $helper = new Helper();
        $orderId = $_POST["orderId"];
        $data["fileQuantity"] = $helper->getFileQuantity($orderId);
        $isSuccess = true;
        $msg = '';
    }else{
        $isSuccess = false;
        $msg = '';
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