<?php
/* Template Name: Idealhit Api GetMappingProductAndElementAndErsatzteilList */
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
    $db_table_name_1 = "ihmapping_products_elements";
    $db_table_name_2 = "ihmapping_elements_ersatzteile";

    $action_name = 'GetMappingProductAndElementAndErsatzteilList';
    $isSuccess = false;
    $msg = '';
    $data = array();

    if(isset($_POST['eans'])){

        $eansArray = explode(",", $_POST['eans']);

        $sql_condition = ' (';
        $counter = 0;
        foreach ($eansArray AS $ean){
            if(strlen($ean) === 13){
                if($counter > 0){
                    $sql_condition .= ",";
                }
                $sql_condition .= "'" . str_replace("42512429","42507553",$ean) . "'";
                $counter++;
            }
        }
        $sql_condition .= ')';

        if($counter > 0){

            $sql = "SELECT proe.`ean_pro`, proe.`ean_elm`, eest.`ean_est` FROM `" . $db_table_name_1 . "` AS proe LEFT JOIN `" . $db_table_name_2 . "` AS eest ON proe.`ean_elm` = eest.`ean_elm` WHERE proe.`ean_pro` in " . $sql_condition;
            $data = $wpdb->get_results($sql);

            $isSuccess = true;
            $msg = COUNT($data) . ' Datensätze wurden erfolgreich geladen.';

        }else{
            $isSuccess = false;
            $msg = 'Leider Position nicht gefunden!';
        }

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