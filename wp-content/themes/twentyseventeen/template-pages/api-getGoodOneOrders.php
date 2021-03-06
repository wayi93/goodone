<?php
/* Template Name: Idealhit Api GetOrderMainInfos */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/3/19
 * Time: 17:07
 */
/**
 * 参数
 * type
 * [0] 所有订单 (包含[1]需要处理的订单)
 * [1] 缓存的需要处理的订单 (包含[3]未付款的订单)
 * [2] 所有 Angebote
 * [3] 所有未付款的订单
 * [4] 所有Ersatzteil订单
 * [5] 所有Gutschrift订单 (包含[6]未付款的)
 * [6] 所有未付款的Gutschrift订单
 */
/**
 *  2021-02-14: Add index to reduce the loading time
 *
 *  SQL Query:
 *  SELECT tbl_io.`meta_id`, tbl_io.`create_at`, tbl_io.`create_by`, tbl_io.`update_by`, tbl_io.`order_id_ab`, tbl_io.`customer_firstName`, tbl_io.`customer_lastName`, tbl_io.`customer_shipping_city`, tbl_io.`customer_shipping_countryISO`, tbl_io.`paidSum`, tbl_io.`status`, tbl_io.`status_quote`, tbl_io.`subtract_from_inventory`, tbl_io.`customer_userIdPlattform`, tbl_io.`order_id_ab_original`, tbl_io.`afterbuy_account`, tbl_idn.`number` FROM `ihattach_orders` AS tbl_io LEFT JOIN `ihattach_document_nrs` AS tbl_idn on tbl_io.`meta_id` = tbl_idn.`order_id`  WHERE tbl_io.`deal_with` = 'gutschrift'   ORDER BY `create_at` DESC
 *
 *  before change: 20.83 sec
 *  after change: 0.09 sec
 *
 *  Add index:
 *  ALTER TABLE ihattach_document_nrs ADD INDEX index_ihattach_document_nrs_orderid (order_id);
 *  ALTER TABLE ihattach_orders ADD INDEX index_ihattach_orders_dealwith (deal_with);
 */

$type = 0;
if(isset($_POST["type"])){
    $type = intval($_POST["type"]);
}

error_log("DUPA api-getGoodOneOrders.php");
error_log("DUPA Template Name: Idealhit Api GetOrderMainInfos");
/**
 * 确定用户组
 */
global $current_user;
get_current_user();
$userGroup = $current_user->roles[0];
$userLogin = $current_user->user_login;

/**
 * 判断，如果没有登陆，那么直接跳转到 Login 页面
 */
if(!is_user_logged_in()){
    //重定向浏览器
    header("Location: /do-login/");
    //确保重定向后，后续代码不会被执行
    exit;
}else{

    $action_name = 'GetOrderMainInfos';
    $isSuccess = false;
    $msg = '';
    $data = array();

    $db_table_orders = "ihattach_orders";
    $db_table_positions = "ihattach_positions";

    global $wpdb;

    $condi_user = " AND tbl_io.`mediate` = '" . $userGroup . ".goodone' ";
    $user_group = $current_user->roles[0];
    if($user_group == "admin" || $user_group == "it" || $user_group == "accounting" || $user_group == "einkauf" || $user_group == "leader" || $user_group == "grocery"){
        $condi_user = " ";
    }
    if($type === 5 || $type === 6){
        if($userLogin === "bastian" || $userLogin === "selma"){
            $condi_user = " ";
        }
    }else{
        if($userLogin === "fan"){
            $condi_user = " ";
        }
    }


    /**
     * 多表联合查询，获取Rechnungsnummer
     */
    $query = "SELECT tbl_io.`meta_id`, tbl_io.`create_at`, tbl_io.`create_by`, tbl_io.`update_by`, tbl_io.`order_id_ab`, tbl_io.`customer_firstName`, tbl_io.`customer_lastName`, tbl_io.`customer_shipping_city`, tbl_io.`customer_shipping_countryISO`, tbl_io.`paidSum`, tbl_io.`status`, tbl_io.`status_quote`, tbl_io.`subtract_from_inventory`, tbl_io.`customer_userIdPlattform`, tbl_io.`order_id_ab_original`, tbl_io.`afterbuy_account`, tbl_idn.`number` FROM `ihattach_orders` AS tbl_io LEFT JOIN `ihattach_document_nrs` AS tbl_idn on tbl_io.`meta_id` = tbl_idn.`order_id` ";
    switch ($type){
        case 0:
            $query .= " WHERE tbl_io.`deal_with` = 'order' " . $condi_user;
            break;
        case 1:
            $query .= " WHERE tbl_io.`deal_with` = 'order' AND tbl_io.`status` NOT IN ('Versandvorbereitung', 'Storniert', 'Versendet') " . $condi_user;
            break;
        case 2:
            $query .= " WHERE tbl_io.`first_deal_with` = 'quote' " . $condi_user;
            break;
        case 3:
            $query .= " WHERE tbl_io.`deal_with` = 'order' AND tbl_io.`status` LIKE '%Unbezahlt%' " . $condi_user;
            break;
        case 4:
            $query .= " WHERE tbl_io.`deal_with` = 'ersatzteil' " . $condi_user;
            break;
        case 5:
            $query .= " WHERE tbl_io.`deal_with` = 'gutschrift' " . $condi_user;
            break;
        case 6:
            // $query .= " WHERE tbl_io.`deal_with` = 'gutschrift' AND tbl_io.`status` NOT IN ('Bezahlt', 'Storniert') " . $condi_user;
            $query .= " WHERE tbl_io.`deal_with` = 'gutschrift' AND tbl_io.`status` LIKE '%Unbezahlt%' AND  tbl_io.`status` LIKE '%Confirmed%' " . $condi_user;
            break;
        case 7:
            $query .= " WHERE tbl_io.`deal_with` = 'gutschrift' AND tbl_io.`status` LIKE '%Unbezahlt%' AND  tbl_io.`status` NOT LIKE '%Confirmed%' " . $condi_user;
            break;
        case 8:
            $query .= " WHERE tbl_io.`deal_with` = 'ersatzteil' AND tbl_io.`status` LIKE '%Neu%' " . $condi_user;
            break;
        case 9:
            $query .= " WHERE tbl_io.`deal_with` = 'ersatzteil' AND tbl_io.`status` LIKE '%Storniert%' " . $condi_user;
            break;
        default:
            $query .= " WHERE 1=1 " . $condi_user;
    }
    $query .= " ORDER BY `create_at` DESC";

    $order_main_infos = $wpdb->get_results($query);


    /**
    $query = "SELECT `meta_id`, `create_at`, `create_by`, `update_by`, `order_id_ab`, `customer_firstName`, `customer_lastName`, `customer_shipping_city`, `customer_shipping_countryISO`, `paidSum`, `status`, `status_quote`, `subtract_from_inventory` FROM `";
    $query .= $db_table_orders;
    switch ($type){
    case 0:
    $query .= "` WHERE `deal_with` = 'order' " . $condi_user;
    break;
    case 1:
    $query .= "` WHERE `deal_with` = 'order' AND `status` <> 'Versandvorbereitung' AND `status` <> 'Storniert' " . $condi_user;
    break;
    case 2:
    $query .= "` WHERE `first_deal_with` = 'quote' " . $condi_user;
    break;
    case 3:
    $query .= "` WHERE `deal_with` = 'order' AND `status`LIKE '%Unbezahlt%' " . $condi_user;
    break;
    default:
    $query .= "` WHERE 1=1 " . $condi_user;
    }
    $query .= " ORDER BY `create_at` DESC";

    $order_main_infos = $wpdb->get_results($query);
     */


    $isSuccess = true;

    if(COUNT($order_main_infos) < 1){
        $msg = "Keine Bestellung gefunden.";
    }else{
        foreach ($order_main_infos as $row){
            $ud = get_userdata($row->create_by);
            $ud_update = get_userdata($row->update_by);

            $customerUserIdPlattform = $row->customer_userIdPlattform;
            if(stristr($customerUserIdPlattform, 'amazon') !== false){
                $customerUserIdPlattform = 'Amazon';
            }else{
                $customerUserIdPlattform = '[Others]';
            }

            $order = array(
                "id" => ($row->meta_id + 3000000),
                //"create_at" => date("d.m.Y H:i:s", $row->create_at),
                "create_at" => date("Y-m-d H:i", $row->create_at),
                "create_by" => (!is_object($ud)?"":$ud->user_firstname) . "&nbsp;" . (!is_object($ud)?"":$ud->user_lastname),
                "update_by" => $row->update_by == 99999999 ? "Kunden" :(!is_object($ud_update)?"":$ud_update->user_firstname) . "&nbsp;" . (!is_object($ud_update)?"":$ud_update->user_lastname),
                "order_id_ab" => $row->order_id_ab,
                "customer_firstName" => $row->customer_firstName,
                "customer_lastName" => $row->customer_lastName,
                "customer_shipping_ort" => $row->customer_shipping_city . "-" . $row->customer_shipping_countryISO,
                "paidSum" => $row->paidSum,
                "status" => $row->status,
                "status_quote" => $row->status_quote,
                "subtract_from_inventory" => $row->subtract_from_inventory,
                "customer_userIdPlattform" => $customerUserIdPlattform,
                "number" => $row->number,
                "order_id_ab_original" => $row->order_id_ab_original,
                "afterbuy_account" => $row->afterbuy_account
            );
            array_push($data, $order);
        }
        $msg = "Bestellungen in GoodOne System wurden erfolgreich geladen.";
    }

    $results = array(
        'action' => $action_name,
        'isSuccess' => $isSuccess,
        'msg' => $msg,
        'data_quantity' => COUNT($data),
        'data' => $data,
        'userGroup' => $userGroup,
    );

    echo json_encode($results);


}
