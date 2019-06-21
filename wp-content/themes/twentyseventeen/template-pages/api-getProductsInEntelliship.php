<?php
/* Template Name: Idealhit Api GetProductsInEntelliship */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/3/19
 * Time: 17:07
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
}else {

    // parameters
    $url = $_settings_data["urls"]["Api_Url_Get_Products"];
    $authorization = base64_encode($_settings_data["server-info"]["Entelliship_UserID"] . ":" . $_settings_data["server-info"]["Entelliship_Password"]);
    $postFields = array();
    $CURLOPT_HTTPHEADER_LIST = array(
        'Authorization: Basic ' . $authorization,
        'Accept: application/xml'
    );

    $helper = new Helper();
    $products = $helper->requestHttpApi($url, $postFields, $CURLOPT_HTTPHEADER_LIST);

    $json = $helper->xmlToJson($products);

    echo $json;

}