<?php
/* Template Name: Idealhit Api PaypalPaymentNotify */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/6/20
 * Time: 11:17
 */

include_once ( GET_STYLESHEET_DIRECTORY() . '/Util/Helper.php');
use SoGood\Support\Util\Helper;
$helper = new Helper();

global $current_user;
get_current_user();

$action_name = 'paypalPaymentNotify';
$isSuccess = false;
$msg = '';
$data = array();

$authorization = base64_encode($_settings_data["server-info"]["Entelliship_UserID"] . ":" . $_settings_data["server-info"]["Entelliship_Password"]);
$CURLOPT_HTTPHEADER_LIST = array(
    'Authorization: Basic ' . $authorization,
    'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
    'Accept: application/xml'
);

if(isset($_POST["payment_status"]) && isset($_POST["custom"])){

    $status = $_POST['payment_status'];
    $custom = $_POST['custom'];

    global $wpdb;

    $token = $helper->encrypt($custom, 'D', 'WaYi93');
    $t_arr = explode("#=#", $token);

    $api_password = $t_arr[0];
    $id_db = $t_arr[1];
    $api_login = $t_arr[2];

    if ($status == "Completed") {

        // 付费成功，在此情况下发货是安全的。
        if(COUNT($t_arr) > 2){

            $helper->setOperationHistory($id_db, 'Die Bestellung wurde erfolgreich bezahlt.', 0, 99999999);

            $auth_res = wp_authenticate($api_login, $api_password);

            if($auth_res->data->user_login == $api_login){

                // 根据 $id_db 查询数据库，看是否有 Afterbuy ID
                $sql_01 = "SELECT `order_id_ab`,  `deal_with`, `status`, `subtract_from_inventory` FROM `ihattach_orders` WHERE `meta_id` = %d";
                $order_id_ab_arr = $wpdb->get_results($wpdb->prepare($sql_01, $id_db));
                $order_id_ab = $order_id_ab_arr[0]->order_id_ab;
                $subtract_from_inventory = $order_id_ab_arr[0]->subtract_from_inventory;

                if(strlen($order_id_ab) > 3){

                    // Afterbuy 订单已经存在数据库中
                    $isSuccess = false;
                    $msg = 'The order was already sent to Afterbuy.';
                    $order_id_in_AB = $order_id_ab;
                    $helper->setOperationHistory($id_db, 'Die Bestellung wurde erfolgreich an Aftrebuy gesendet. Bestellung-Nr. in Aftrebuy lautet: <a href="https://farm02.afterbuy.de/afterbuy/auktionsliste.aspx?art=edit&id=' . $order_id_in_AB . '" target="_blank">' . $order_id_in_AB . '</a>.', 0, 88888888);

                }else{

                    // 判断是不是订单
                    $deal_with = $order_id_ab_arr[0]->deal_with;

                    if($deal_with == 'order'){

                        // 查库存
                        $status = $order_id_ab_arr[0]->status;

                        /**
                         * 更新数据库的Update时间，准备参数
                         */
                        $orderNewData = array(
                            "update_at" => time() + ( $_settings_data['server-info']['gmt_offset'] * 3600 ),
                            "update_by" => 99999999
                        );

                        $oldStatus_arr = explode(", ", $status);
                        for($i = 0; $i < COUNT($oldStatus_arr); ++$i){
                            if($helper->checkContainStr($oldStatus_arr[$i], "Unbezahlt")){
                                //unset($oldStatus_arr[$i]);
                                array_splice($oldStatus_arr,$i, 1);
                            }
                        }
                        if(COUNT($oldStatus_arr) < 1){
                            array_push($oldStatus_arr, "Versandvorbereitung");
                        }else{
                            if($helper->isValueInArray("Bezahlt", $oldStatus_arr)){
                                //
                            }else{
                                array_push($oldStatus_arr, "Bezahlt");
                            }
                        }
                        $newStatus = "";
                        for($j = 0; $j < COUNT($oldStatus_arr); ++$j){
                            if($j == 0){
                                $newStatus .= $oldStatus_arr[$j];
                            }else{
                                $newStatus = $newStatus . ", " . $oldStatus_arr[$j];
                            }
                        }
                        $orderNewData["status"] = $newStatus;
                        if($orderNewData["status"] == ""){
                            $orderNewData["status"] = "Versandvorbereitung";
                        }

                        if($helper->checkContainStr($status, "Nicht auf Lager")){

                            // 库存不足，虽然客户已经付款，但是仍需等待
                            $isSuccess = false;
                            $msg = 'The order has been paid, but the products in order are out of stock, so the customer should wait.';

                        }else{

                            // 终于可以发出到 Afterbuy
                            $isSuccess = true;

                            /**
                             * 判断是否需要走德国E2库存
                             */
                            if($subtract_from_inventory == "NO"){

                                $orderNewData["order_id_ab"] = "N/A";

                                $msg = 'Bestellung wurde erfolgreich angelegt. Lagerbestand in E2 wurde nicht geändert.';

                            }else{

                                // parameters
                                $url = home_url() . '/api/resendordertoe2';
                                $postFields = array(
                                    'id_db' => $id_db
                                );
                                $res_data = $helper->requestHttpApi($url, $postFields, $CURLOPT_HTTPHEADER_LIST);
                                $order_id_ab = $helper->getTextInKuoHao($res_data);
                                $data = array(
                                    "order_id_ab" => $order_id_ab
                                );
                                $orderNewData["order_id_ab"] = $order_id_ab;

                                $msg = 'Bestellung wurde erfolgreich an E2 gesendet.';

                            }

                        }

                        /**
                         * 操作数据库，修改订单的状态
                         */
                        //array_push($data, $orderNewData);
                        $sql_uo = "UPDATE `ihattach_orders` SET ";
                        if(COUNT($orderNewData) > 0){
                            $prepareArr = array();
                            foreach ($orderNewData as $k => $v){
                                $sql_uo = $sql_uo . ' `' . $k . '` = ' . $helper->getZhanWeiFuByFieldName($k) . ',';
                                array_push($prepareArr, $v);
                            }
                        }
                        $sql_uo = substr($sql_uo, 0, -1);
                        $sql_uo = $sql_uo . " WHERE `meta_id` = " . $helper->getZhanWeiFuByFieldName("meta_id");
                        array_push($prepareArr, $id_db);
                        $wpdb->query($wpdb->prepare($sql_uo, $prepareArr));


                    }else{

                        // Only oder can be sent to Afterbuy
                        $isSuccess = false;
                        $msg = 'This is not an order.';

                    }

                }

            }else{

                // API 账户登陆失败
                $isSuccess = false;
                $msg = 'No authorization to access the service [' . $action_name . '].';

            }

        }else{

            // token 格式出错， 通过 #=# 分解后，至少要有三个值
            $isSuccess = false;
            $msg = 'The token format is incorrect.';

        }

    } else if ($status == "Pending") {

        // 款项在途，目前Paypal有可能出现状态为Pending，实际上已经支付成功的情况。
        $isSuccess = false;
        $msg = 'Das Geld wird noch nicht auf dem Sogood-Paypal-Konto gebucht. [pending_reason: ' . $_POST['pending_reason'] . ']';

        // 根据 $id_db 查询数据库，看是否有 Afterbuy ID
        $sql_01 = "SELECT `order_id_ab`,  `deal_with`, `status` FROM `ihattach_orders` WHERE `meta_id` = %d";
        $order_id_ab_arr = $wpdb->get_results($wpdb->prepare($sql_01, $id_db));
        $order_id_ab = $order_id_ab_arr[0]->order_id_ab;
        $deal_with = $order_id_ab_arr[0]->deal_with;
        $status = $order_id_ab_arr[0]->status;

        if(strlen($order_id_ab) > 3){

            // Afterbuy 订单已经存在数据库中
            $isSuccess = false;
            $msg = 'The order was already sent to Afterbuy.';

        }else if($deal_with != "order"){

            // Only oder can be sent to Afterbuy
            $isSuccess = false;
            $msg = 'This is not an order. ';

        }else{

            if($status == ""){
                $status = "Paypal Pending";
            }else{
                if($helper->checkContainStr($status, "Paypal Pending")){
                    //
                }else{
                    $status .= ", Paypal Pending";
                }
            }

            /**
             * 更新数据库的Update时间，准备参数
             */
            $orderNewData = array(
                "update_at" => time() + ( $_settings_data['server-info']['gmt_offset'] * 3600 ),
                "update_by" => 99999999,
                "status" => $status
            );

            /**
             * 操作数据库，修改订单的状态
             */
            $sql_uo = "UPDATE `ihattach_orders` SET ";
            if(COUNT($orderNewData) > 0){
                $prepareArr = array();
                foreach ($orderNewData as $k => $v){
                    $sql_uo = $sql_uo . ' `' . $k . '` = ' . $helper->getZhanWeiFuByFieldName($k) . ',';
                    array_push($prepareArr, $v);
                }
            }
            $sql_uo = substr($sql_uo, 0, -1);
            $sql_uo = $sql_uo . " WHERE `meta_id` = " . $helper->getZhanWeiFuByFieldName("meta_id");
            array_push($prepareArr, $id_db);
            $wpdb->query($wpdb->prepare($sql_uo, $prepareArr));

        }







    } else {

        // 付款失败
        $isSuccess = false;
        $msg = 'Betrag wurde bisher noch nicht bezahlt.';

    }

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