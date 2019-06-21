<?php
/* Template Name: Idealhit Api GetDocumentNumberFromEntelliship.php */
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
if(!is_user_logged_in() || !isset($_POST["doctyp"])){

    echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?><result><name>failure no parameters</name><httpStatusCode></httpStatusCode></result>';

}else {

    $param_id = $_POST["doctyp"];

    // parameters
    $url = $_settings_data["urls"]["Api_Url_Get_Entelliship_DOCNr"];
    $authorization = base64_encode($_settings_data["server-info"]["Entelliship_UserID"] . ":" . $_settings_data["server-info"]["Entelliship_Password"]);
    $postFields = array(
        "id" => $param_id
    );
    $CURLOPT_HTTPHEADER_LIST = array(
        'Authorization: Basic ' . $authorization,
        'Accept: application/xml',
        //'Content-Type: application/json'
    );

    $helper = new Helper();
    $result = $helper->requestHttpApi($url, $postFields, $CURLOPT_HTTPHEADER_LIST);

    $json = $helper->xmlToJson($result);

    echo $json;

}