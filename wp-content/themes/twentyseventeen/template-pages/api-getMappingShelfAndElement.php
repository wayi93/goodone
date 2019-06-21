<?php
/* Template Name: Idealhit Api GetMappingRegalAndElementList */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/4/25
 * Time: 16:24
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
    $db_table_name = "ihmapping_shelf_elements";

    $action_name = 'GetMappingShelfAndElementList';
    $isSuccess = false;
    $msg = '';
    $data = array();


    /**
     * 参数说明
     * $type
     * 1: Regal 是一个，Element EAN 是多个
     * 2: Element EAN 是一个，Regal 是多个
     * 3: [load all records] Regal 是多个，Regal 是多个
     */
    $type = -1;
    $ean = '';

    if(isset($_POST["type"]) && isset($_POST["shelfid"])){

        $type = intval($_POST["type"]);
        $shelfid = $_POST["shelfid"];

        switch ($type){
            case 1:
                /**
                 * Regal 是一个，Element EAN 是多个
                 */
                $column_ean_name = 'shelf_id';
                $sql = "SELECT `meta_id`, `shelf_id`, `ean_elm` FROM `" . $db_table_name . "` WHERE `" . $column_ean_name . "` = %s;";
                $data = $wpdb->get_results($wpdb->prepare($sql, $shelfid));
                break;
            case 2:
                /**
                 * Element EAN 是一个，Regal 是多个
                 */
                break;
            case 3:
                /**
                 * [load all records] Regal 是多个，Regal 是多个
                 */
                $sql = "SELECT `meta_id`, `shelf_id`, `ean_elm` FROM `" . $db_table_name . "` WHERE 1=1;";
                $data = $wpdb->get_results($sql);
                break;
            default:
                //
        }

        $isSuccess = true;
        $msg = COUNT($data) . ' Datensätze wurden erfolgreich geladen.';


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