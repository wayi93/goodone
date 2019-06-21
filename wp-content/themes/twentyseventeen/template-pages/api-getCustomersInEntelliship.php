<?php
/* Template Name: Idealhit Api GetCustomersInEntelliship */
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

    $param_id = "0";
    $param_surname = "";
    $param_firstname = "";
    $param_mail = "";
    $param_phone = "";
    $param_postcode = "";

    // 获得参数
    if(isset($_POST["id"]) && $_POST["id"] != ""){
        $param_id = $_POST["id"];
    }
    if(isset($_POST["surname"])){
        $param_surname = $_POST["surname"];
    }
    if(isset($_POST["firstname"])){
        $param_firstname = $_POST["firstname"];
    }
    if(isset($_POST["mail"])){
        $param_mail = $_POST["mail"];
    }
    if(isset($_POST["phone"])){
        $param_phone = $_POST["phone"];
    }
    if(isset($_POST["postcode"])){
        $param_postcode = $_POST["postcode"];
    }

    // parameters
    $url = $_settings_data["urls"]["Api_Url_Get_Customers"];
    $authorization = base64_encode($_settings_data["server-info"]["Entelliship_UserID"] . ":" . $_settings_data["server-info"]["Entelliship_Password"]);
    $postFields = array();
    if($param_id == "0"){
        $postFields = array(
            "surname" => $param_surname,
            "firstname" => $param_firstname,
            "mail" => $param_mail,
            "phone" => $param_phone,
            "postcode" => $param_postcode
        );
    }else{
        $postFields = array(
            "id" => $param_id
        );
    }
    $CURLOPT_HTTPHEADER_LIST = array(
        'Authorization: Basic ' . $authorization,
        //'Accept: application/xml',
        'Content-Type: application/json'
    );

    $helper = new Helper();
    $customers = $helper->requestHttpApi_Json($url, $postFields, $CURLOPT_HTTPHEADER_LIST);

    $json = $helper->xmlToJson($customers);

    echo $json;

}