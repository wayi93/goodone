<?php
/* Template Name: Idealhit Api UpdateErsatzteilReason */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/4/25
 * Time: 16:24
 */

/**
 * Endpoint         /api/updateersatzteilreason/
 *
 * 参数说明
 * @param act       0: load all records
 *                  1: insert a new one
 *                  2: update a reason
 *                  3: 下单页面读reason数据
 * @param id        : record id
 * @param reason    : reason
 * @param type      : ersatzteil 或 gutschrift
 *
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


    global $wpdb;
    $db_table_name = "ihattach_ersatzteil_reasons";

    $action_name = 'UpdateReasonForErsatzteil&Gutschrift';
    $isSuccess = false;
    $msg = '';
    $data = array();

    $isNoParameterError = false;

    if(isset($_POST["act"]) && isset($_POST["type"])){

        $action = intval($_POST["act"]);
        $type = $_POST["type"];
        $id = 0;
        $reason = '';

        /**
         * $action 0 3 都是读取全部数据
         */
        if($action === 3){
            $action = 0;
        }

        /**
         * 判断参数是否齐全
         */
        if($action === 1){
            if(isset($_POST["reason"])){
                $reason = $_POST["reason"];
                $isNoParameterError = false;
            }else{
                $isNoParameterError = true;
            }
        }else if($action === 2){
            if(isset($_POST["id"]) && isset($_POST["reason"])){
                $id = $_POST["id"];
                $reason = $_POST["reason"];
                $isNoParameterError = false;
            }else{
                $isNoParameterError = true;
            }
        }


        /**
         * 执行数据库操作
         */
        switch ($action){
            case 0:
                $sql = 'SELECT * FROM `' . $db_table_name . '` WHERE `type` = "' . $type . '"';
                $data = $wpdb->get_results($sql);
                $isSuccess = true;
                $msg = 'Alle Gründe wurden erforgreich geladen.';
                break;
            case 1:
                $sql = 'INSERT INTO `' . $db_table_name . '` (`reason`, `type`) VALUES (%s, %s)';
                $wpdb->query($wpdb->prepare($sql, $reason, $type));
                $isSuccess = true;
                $msg = 'Der nene Grund wurde erforgreich speichert.';
                break;
            case 2:
                $sql = 'UPDATE `ihattach_ersatzteil_reasons` SET `reason` = %s WHERE `meta_id` = %d';
                $wpdb->query($wpdb->prepare($sql, $reason, $id));
                $isSuccess = true;
                $msg = 'Der Grund wurde erforgreich geändert.';
                break;
            default:
                //
        }


    }else{
        $isNoParameterError = true;
    }



    if($isNoParameterError){
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