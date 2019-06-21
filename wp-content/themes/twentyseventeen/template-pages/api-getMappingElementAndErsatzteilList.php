<?php
/* Template Name: Idealhit Api GetMappingElementAndErsatzteilList */
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
    $db_table_name = "ihmapping_elements_ersatzteile";

    $action_name = 'GetMappingElementAndErsatzteilList';
    $isSuccess = false;
    $msg = '';
    $data = array();


    /**
     * 参数说明
     * $type
     * 1: Element EAN 是一个，Ersatzteil EAN 是多个
     * 2: Ersatzteil EAN 是一个，Element EAN 是多个
     * 3: Ersatzteil EAN 是多个，Element EAN 是多个
     */
    $type = -1;
    $ean = '';

    if(isset($_POST["type"]) && isset($_POST["ean"])){

        $type = intval($_POST["type"]);
        $ean = $_POST["ean"];

        $column_ean_name = 'ean_';
        switch ($type){
            case 1:
                /**
                 * Element EAN 是一个，Ersatzteil EAN 是多个
                 */
                $column_ean_name .= 'elm';
                $sql = "SELECT `meta_id`, `ean_elm`, `ean_est` FROM `" . $db_table_name . "` WHERE `" . $column_ean_name . "` = %s;";
                $data = $wpdb->get_results($wpdb->prepare($sql, $ean));
                break;
            case 2:
                /**
                 * Ersatzteil EAN 是一个，Element EAN 是多个
                 */
                $column_ean_name .= 'est';
                $sql = "SELECT `meta_id`, `ean_elm`, `ean_est` FROM `" . $db_table_name . "` WHERE `" . $column_ean_name . "` = %s;";
                $data = $wpdb->get_results($wpdb->prepare($sql, $ean));
                break;
            case 3:
                /**
                 * Ersatzteil EAN 是多个，Element EAN 是多个
                 */
                $column_ean_name .= '';
                $sql = "SELECT `meta_id`, `ean_elm`, `ean_est` FROM `" . $db_table_name . "` WHERE 1=1;";
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