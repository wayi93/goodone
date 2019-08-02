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
 * 0 所有订单 (包含[1]需要处理的订单)
 * 1 缓存的需要处理的订单 (包含[3]未付款的订单)
 * 2 所有 Angebote
 * 3 所有未付款的订单
 * 4 所有Ersatzteil订单
 */
$type = 0;
if(isset($_POST["type"])){
    $type = $_POST["type"];
}

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
    if($user_group == "admin" || $user_group == "it" || $user_group == "accounting" || $user_group == "einkauf"){
        $condi_user = " ";
    }
    if($userLogin == "fan"){
        $condi_user = " ";
    }


    /**
     * 多表联合查询，获取Rechnungsnummer
     */
    $query = "SELECT tbl_io.`meta_id`, tbl_io.`create_at`, tbl_io.`create_by`, tbl_io.`update_by`, tbl_io.`order_id_ab`, tbl_io.`customer_firstName`, tbl_io.`customer_lastName`, tbl_io.`customer_shipping_city`, tbl_io.`customer_shipping_countryISO`, tbl_io.`paidSum`, tbl_io.`status`, tbl_io.`status_quote`, tbl_io.`subtract_from_inventory`, tbl_idn.`number` FROM `ihattach_orders` AS tbl_io LEFT JOIN `ihattach_document_nrs` AS tbl_idn on tbl_io.`meta_id` = tbl_idn.`order_id` ";
    switch ($type){
        case 0:
            $query .= " WHERE tbl_io.`deal_with` = 'order' " . $condi_user;
            break;
        case 1:
            $query .= " WHERE tbl_io.`deal_with` = 'order' AND tbl_io.`status` <> 'Versandvorbereitung' AND tbl_io.`status` <> 'Storniert' " . $condi_user;
            break;
        case 2:
            $query .= " WHERE tbl_io.`first_deal_with` = 'quote' " . $condi_user;
            break;
        case 3:
            $query .= " WHERE tbl_io.`deal_with` = 'order' AND tbl_io.`status`LIKE '%Unbezahlt%' " . $condi_user;
            break;
        case 4:
            $query .= " WHERE tbl_io.`deal_with` = 'ersatzteil' " . $condi_user;
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

            $order = array(
                "id" => ($row->meta_id + 3000000),
                //"create_at" => date("d.m.Y H:i:s", $row->create_at),
                "create_at" => date("Y-m-d H:i", $row->create_at),
                "create_by" => $ud->user_firstname . "&nbsp;" . $ud->user_lastname,
                "update_by" => $row->update_by == 99999999 ? "Kunden" : $ud_update->user_firstname . "&nbsp;" . $ud_update->user_lastname,
                "order_id_ab" => $row->order_id_ab,
                "customer_firstName" => $row->customer_firstName,
                "customer_lastName" => $row->customer_lastName,
                "customer_shipping_ort" => $row->customer_shipping_city . "-" . $row->customer_shipping_countryISO,
                "paidSum" => $row->paidSum,
                "status" => $row->status,
                "status_quote" => $row->status_quote,
                "subtract_from_inventory" => $row->subtract_from_inventory,
                "number" => $row->number
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