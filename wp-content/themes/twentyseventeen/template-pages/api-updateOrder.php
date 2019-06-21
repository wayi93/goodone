<?php
/* Template Name: Idealhit Api UpdateOrder */
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

    $action_name = 'UpdateOrder';
    $isSuccess = false;
    $msg = '';
    $data = array();

    if(isset($_POST["order_new_data"])){

        $helper = new Helper();

        global $wpdb;
        global $current_user;
        get_current_user();

        $db_table_orders = "ihattach_orders";
        $db_table_positions = "ihattach_positions";

        /**
         * 需要修改的数据
         */
        $orderNewData = json_decode(rawurldecode($_POST["order_new_data"]));

        /**
         * 通用数据
         */
        $update_at = time() + ( $_settings_data['server-info']['gmt_offset'] * 3600 );
        $update_by = $current_user->ID;
        $orderNewData->update_at = $update_at;
        $orderNewData->update_by = $update_by;

        $sql_order = "UPDATE `" . $db_table_orders . "` SET ";
        $prepareArr = array();
        foreach ($orderNewData as $k => $v){
            if($k != 'meta_id'){
                $sql_order = $sql_order . ' `' . $k . '` = ' . $helper->getZhanWeiFuByFieldName($k) . ',';
                array_push($prepareArr, $v);
            }
        }
        $sql_order = substr($sql_order, 0, -1);
        $sql_order = $sql_order . " WHERE `meta_id` = " . $helper->getZhanWeiFuByFieldName("meta_id");
        array_push($prepareArr, $orderNewData->meta_id);
        $wpdb->query($wpdb->prepare($sql_order, $prepareArr));


        $isSuccess = true;
        $msg = 'Die Bestellung wurde erfolgreich aktualisiert.';


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