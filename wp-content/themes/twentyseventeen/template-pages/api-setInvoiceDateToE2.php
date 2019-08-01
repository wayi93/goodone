<?php
/* Template Name: Idealhit Api SetInvoiceDateToE2 */
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

    $action_name = 'SetInvoiceDateToE2';
    $isSuccess = false;
    $msg = '';
    $data = array();

    if(isset($_POST["aboid"]) && isset($_POST["gooid"]) && isset($_POST['abaccount'])){

        /**
         * abaccount:
         * 1 Sogood
         * 2 Mai & Mai
         */
        $isSogood = 'false';
        if(intval($_POST['abaccount']) === 1){
            $isSogood = 'true';
        }

        $helper = new Helper();

        $isSuccess = true;
        $msg = "Rechnungsdatum wurde erfolgreich an E2 gesendet.";

        // parameters
        $url = $_settings_data["urls"]["Api_Url_SetInvoiceDate"] . "?isSogood=" . $isSogood . "&orderId=" . $_POST["aboid"];
        $authorization = base64_encode($_settings_data["server-info"]["Entelliship_UserID"] . ":" . $_settings_data["server-info"]["Entelliship_Password"]);
        $CURLOPT_HTTPHEADER_LIST = array(
            'Authorization: Basic ' . $authorization,
            'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
            'Accept: application/xml'
        );

        $postFields = null;

        $res_data = $helper->requestHttpApi($url, $postFields, $CURLOPT_HTTPHEADER_LIST);

        $data = $helper->xmlToJson($res_data);

        /**
         * 预测好订单ID,就可以写入日志了
         */
        $get_order_id_in_AB_1 = explode('invoice date ', json_encode($data));
        $order_id_in_AB = substr(trim($get_order_id_in_AB_1[1]), 0, 9);
        $helper->setOperationHistory($_POST["gooid"], 'Das Rechnungsdatum wurde erfolgreich in Aftrebuy gesetzt. Bestellung-Nr. in Aftrebuy lautet: <a href="https://farm02.afterbuy.de/afterbuy/auktionsliste.aspx?art=edit&id=' . $order_id_in_AB . '" target="_blank">' . $order_id_in_AB . '</a>.', 0, 88888888);


    }else{

        $isSuccess = false;
        $msg = 'Missing parameters!';

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