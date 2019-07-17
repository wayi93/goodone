<?php
/* Template Name: Idealhit Api UpdateOrderStatus */
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

    $action_name = 'UpdateOrderStatus';
    $isSuccess = false;
    $msg = '';
    $data = array();

    if(isset($_GET["afterbuy_order_id"]) && isset($_GET["username"]) && isset($_GET["password"])){

        /**
         * 参数
         */
        $order_id_ab = $_GET["afterbuy_order_id"];

        /**
         * 验证身份
         */
        if($_settings_data["server-info"]["goodone_api_username"] === $_GET["username"] &&
            $_settings_data["server-info"]["goodone_api_password"] === $_GET["password"])
        {

            $helper = new Helper();

            global $wpdb;

            $db_table_orders = "ihattach_orders";
            $db_table_positions = "ihattach_positions";

            /**
             * 查询订单
             */
            $sql = "SELECT `meta_id`, `deal_with` FROM `" . $db_table_orders . "` WHERE order_id_ab = " . $helper->getZhanWeiFuByFieldName("order_id_ab");
            $wpdb->query($wpdb->prepare($sql, $order_id_ab));

            if($wpdb->num_rows === 1){

                /**
                 * Update 订单
                 */

                // 订单
                $lastResult = $wpdb->last_result[0];
                $meta_id = $lastResult->meta_id;
                $dealWith = $lastResult->deal_with;

                // 需要修改的数据
                $orderNewData = array(
                    'meta_id' => $meta_id,
                    'status' => 'Versendet',
                );

                // 通用数据
                $update_at = time() + ( $_settings_data['server-info']['gmt_offset'] * 3600 );
                $update_by = 22;
                $orderNewData['update_at'] = $update_at;
                $orderNewData['update_by'] = $update_by;

                $sql_order = "UPDATE `" . $db_table_orders . "` SET ";
                $prepareArr = array();
                foreach ($orderNewData as $k => $v){
                    if($k != 'order_id_ab'){
                        $sql_order = $sql_order . ' `' . $k . '` = ' . $helper->getZhanWeiFuByFieldName($k) . ',';
                        array_push($prepareArr, $v);
                    }
                }
                $sql_order = substr($sql_order, 0, -1);
                $sql_order = $sql_order . " WHERE `meta_id` = " . $helper->getZhanWeiFuByFieldName("meta_id");
                array_push($prepareArr, $orderNewData['meta_id']);
                $wpdb->query($wpdb->prepare($sql_order, $prepareArr));

                if($wpdb->dbh->affected_rows === 1){

                    if($dealWith === 'order'){
                        $helper->setOperationHistory($meta_id, 'Status wurde nach Lieferschein-Scannen geändert.', 0, 88888888);
                    }
                    if($dealWith === 'ersatzteil'){
                        $helper->setOperationHistory($meta_id, 'Status wurde nach Lieferschein-Scannen geändert.', 4, 88888888);
                    }

                    $isSuccess = true;
                    $msg = 'Die Bestellung #' . (3000000 + intval($meta_id)) . ' wurde erfolgreich aktualisiert.';

                }else{

                    $isSuccess = false;
                    $msg = 'Die Bestellung kann nicht aktualisiert werden. Ein Datenbankfehler ist in GoodOne aufgetreten.';

                }

            }else if($wpdb->num_rows < 1){

                $isSuccess = false;
                $msg = 'Die Bestellung kann nicht aktualisiert werden. Keine Bestellung mit Afterbuy-Order-ID [' . $order_id_ab . '] wurde gefunden.';

            }else{

                $isSuccess = false;
                $msg = 'Die Bestellung kann nicht aktualisiert werden. ' . $wpdb->num_rows . ' Bestellungen mit Afterbuy-Order-ID [' . $order_id_ab . '] wurden gefunden.';

            }

        }else{

            $isSuccess = false;
            $msg = 'Api Authorization failed!';

        }

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