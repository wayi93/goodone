<?php
/* Template Name: Idealhit Api SetOperationHistory */
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

    $action_name = 'SetOperationHistory';
    $isSuccess = false;
    $msg = '';
    $data = array();

    if(isset($_POST["order_id"]) && isset($_POST["message"]) && isset($_POST["doc_type"]) && isset($_POST["user_id"])){

        /**
         * $docType : 0 order 1 quote
         */
        $orderId = $_POST["order_id"];
        $message = str_replace('###SHUANGYINHAO###', '"', $_POST["message"]);
        $docType = $_POST["doc_type"];
        $userId = $_POST["user_id"];

        $helper = new Helper();

        global $wpdb;
        global $current_user;
        get_current_user();

        $db_table_operation_history = "ihattach_operation_history";

        /**
         * 通用数据
         */
        $create_at = time() + ( $_settings_data['server-info']['gmt_offset'] * 3600 );
        $create_by = $userId;
        if(intval($userId) == 0){
            $create_by = $current_user->ID;
        }

        $sql_log = "INSERT INTO `" . $db_table_operation_history . "` (`create_at`, `create_by`, `order_id`, `message`, `doc_type`) values (%f, %d, %d, %s, %s)";
        $wpdb->query($wpdb->prepare($sql_log, $create_at, $create_by, $orderId, $message, $docType));

        $isSuccess = true;
        $msg = "The operation history was saved.";
        $data = array(
            "create_at" => date("Y-m-d H:i:s", $create_at)
        );

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