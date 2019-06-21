<?php
/* Template Name: Idealhit Api SetPrintFileLog */
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

    $action_name = 'SetPrintFileLog';
    $isSuccess = false;
    $msg = '';
    $data = array();

    /**
     * Parameters
     * file_name = maimai.2018-10-22.235901.pdf
     * file_type = pdf
     * file_category = Lieferschein
     * already_printed = 1
     */
    if(isset($_POST["file_name"]) && isset($_POST["file_type"]) && isset($_POST["file_category"]) && isset($_POST["already_printed"])){

        $helper = new Helper();

        global $wpdb;
        global $current_user;
        get_current_user();

        $db_table_file_print_logs = "ihattach_file_print_logs";

        $name = $_POST["file_name"];
        $type = $_POST["file_type"];
        $category = $_POST["file_category"];
        $already_printed = $_POST["already_printed"];

        $print_count = 1;
        $memo = '';

        /**
         * 通用数据
         */
        //$create_at = current_time('mysql', 1);
        $create_at = time() + ( $_settings_data['server-info']['gmt_offset'] * 3600 );
        $update_at = $create_at;
        $create_by = $current_user->ID;
        $update_by = 0;


        /**
         * 查询是否有这个文件的log
         */
        $sql_check = 'SELECT `meta_id` FROM `' . $db_table_file_print_logs . '` WHERE `name` = %s limit 1;';
        $check_results_db = $wpdb->get_results($wpdb->prepare($sql_check, $name));
        if(COUNT($check_results_db) > 0){

            /**
             * 更新表
             */
            $meta_id_exist = $check_results_db[0]->meta_id;
            $sql_update = 'UPDATE `' . $db_table_file_print_logs . '` SET `update_at`=%f, `update_by`=%d, `name`=%s, `type`=%s, `category`=%s, `already_printed`=%d WHERE `meta_id` = %d';
            $wpdb->query($wpdb->prepare($sql_update, $update_at, $create_by, $name, $type, $category, $already_printed, $meta_id_exist));

        }else{

            /**
             * 插入表
             */
            $sql_create = 'INSERT INTO `' . $db_table_file_print_logs . '` (`create_at`, `update_at`, `create_by`, `update_by`, `name`, `type`, `category`, `already_printed`, `print_count`, `memo`) VALUES (%f,%f,%d,%d,%s,%s,%s,%d,%d,%s)';
            $wpdb->query($wpdb->prepare($sql_create, $create_at, $update_at, $create_by, $update_by, $name, $type, $category, $already_printed, $print_count, $memo));

        }


        $isSuccess = true;
        $msg = "SetPrintFileLog wurde erfolgreich in Datenbank gespeichert.";

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