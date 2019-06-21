<?php
/* Template Name: Idealhit Api GetDeliveryNotePrintTimes */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/10/12
 * Time: 12:45
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

    $action_name = 'GetDeliveryNotePrintTimes';
    $isSuccess = false;
    $msg = '';
    $data = array();

    if(isset($_POST["oid"])){

        global $wpdb;
        $helper = new Helper();

        $order_id_goodone = $_POST["oid"];
        $deliveryNotePrintTimes = 0;


        $sql = "SELECT COUNT(*) AS DeliveryNotePrintTimes FROM `ihattach_operation_history` WHERE `order_id` = " . $order_id_goodone . " AND `message` LIKE '%Der PDF-Lieferschein%'";
        $resultsInDB = $wpdb->get_results($sql);

        if(COUNT($resultsInDB) > 0){
            foreach ($resultsInDB as $itm){
                $deliveryNotePrintTimes = intval($itm->DeliveryNotePrintTimes);
            }
        }



        $isSuccess = true;
        $msg = '';
        $data["DeliveryNotePrintTimes"] = $deliveryNotePrintTimes;

    }else{
        $isSuccess = false;
        $msg = 'There is no parameter.';
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
