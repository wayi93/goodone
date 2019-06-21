<?php
/* Template Name: Idealhit Api GetPosQuantityWantInOrder */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/3/19
 * Time: 17:07
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

    $action_name = 'GetPosQuantityWantInOrder';
    $isSuccess = false;
    $msg = '';
    $data = array();

    $db_table_orders = "ihattach_orders";
    $db_table_positions = "ihattach_positions";

    global $wpdb;

    $query = "SELECT `ean`, `title`, `quantity_want` FROM `ihattach_positions` WHERE `order_id` = " . $_POST["id_db"];
    $db_res_s = $wpdb->get_results($query);

    if(COUNT($db_res_s) < 1){
        $isSuccess = false;
        $msg = "Keine Bestellung gefunden.";
    }else{
        $isSuccess = true;
        foreach ($db_res_s as $row){
            if($row->ean != "7777777777777" && $row->ean != "8888888888888" && $row->ean != "9999999999999" && strlen($row->ean) == 13){
                $pos = array(
                    "ean" => $row->ean,
                    "title" => $row->title,
                    "quantity_want" => $row->quantity_want
                );
                array_push($data, $pos);
            }
        }
        $msg = "Bestellungen in GoodOne System wurden erfolgreich geladen.";
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