<?php
/* Template Name: Idealhit DoPaypal Page */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/3/19
 * Time: 17:07
 *
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 * Sogood網點上面的程序才是最新的
 *
 */
header("Content-type: text/html; charset=utf-8");
include_once ( GET_STYLESHEET_DIRECTORY() . '/Util/Helper.php');
use SoGood\Support\Util\Helper;
$helper = new Helper();

global $wpdb;
$db_table_orders = "ihattach_orders";
$db_table_positions = "ihattach_positions";

global $current_user;
get_current_user();

/**
 * 部署的时候需要修改的变量
 */
//$business_paypal_email = "info@sogood.de";
$business_paypal_email = "info@maimai24.de";
$paypal_payments_endpoint = "https://www.paypal.com/cgi-bin/webscr";
/*
if($current_user->user_login == 'ying'){
	$business_paypal_email = "company@sogood.de";
    $paypal_payments_endpoint = "https://www.sandbox.paypal.com/cgi-bin/webscr";
}
*/

$url = home_url(add_query_arg(array()));
$url_list = explode("/dopaypal/", $url);
$id = substr($url_list[1], 0, 7);
$id_db = intval($id) - 3000000;
$verification_code = substr($url_list[1], 7, 2);

/**
 * 检查是否已经支付过
 */
// 查数据库: 主表信息
$q_main = "SELECT * FROM `" . $db_table_orders . "` WHERE `meta_id` = %d";
$order_main_infos_db = $wpdb->get_results($wpdb->prepare($q_main, $id_db));
$order_id_ab = "";
$status = "";
$order_main_infos = array();

$order_main_infos = $order_main_infos_db[0];
$verification_code_db = substr(strval($order_main_infos->create_at), 8, 2);

if(COUNT($order_main_infos_db) < 1 || $verification_code != $verification_code_db){

    echo $helper->getMessagePageHtml("Die Bestellung wurde nicht gefunden.");
    exit;

}else{

    $order_id_ab = $order_main_infos->order_id_ab;
    $status = $order_main_infos->status;

}


//if(isset($_POST["payment_status"]) && isset($_POST["custom"])){
if(!$helper->checkContainStr($status, "Versandvorbereitung") && strlen($order_id_ab) < 5){

    /**
     * Paypal 快速支付 参数
     */
    $query_params = array(

        "business" => $business_paypal_email,
        "upload" => "1",
        "cmd" => "_cart",
        "no_note" => "1",
        "lc" => "DE",
        "currency_code" => "EUR",
        "bn" => "Doporro_BuyNow_WPS_DE", //format <Company>_<Service>_<Product>_<Country>
        "return" => str_replace("https","http",home_url()) . "/payment-successful",
        "cancel_return" => str_replace("https","http",home_url()) . "/payment-cancelled",
        "notify_url" => str_replace("https","http",home_url()) . "/api/paypalpaymentnotify",
        "image_url" => str_replace("https","http",home_url()) . "/wp-content/uploads/images/logo_150x50.png",
        "custom" => $helper->encrypt($_settings_data["server-info"]["goodone_api_password"] . "#=#" . $id_db."#=#" . $_settings_data["server-info"]["goodone_api_username"], 'E', 'WaYi93')

    );

    // 读取数据库主表信息
    $tax = $order_main_infos->tax;
    $paidSum = $order_main_infos->paidSum;
    $query_params["first_name"] = $order_main_infos->customer_shipping_firstName;
    $query_params["last_name"] = $order_main_infos->customer_shipping_lastName;
    //$query_params["payer_email"] = $order_main_infos->goodone_customer_mail;
    $query_params["address_override"] = "1";
    $query_params["address1"] = $order_main_infos->customer_shipping_street;
    if(strlen($order_main_infos->customer_shipping_street1) > 0){
        $query_params["address2"] = $order_main_infos->customer_shipping_street1;
    }
    $query_params["city"] = $order_main_infos->customer_shipping_city;
    $query_params["country"] = $order_main_infos->customer_shipping_countryISO;
    $query_params["email"] = $order_main_infos->goodone_customer_mail;
    $query_params["zip"] = $order_main_infos->customer_shipping_postalCode;
    $query_params["tax_cart"] = $helper->formatPrice((floatval($paidSum) * intval($tax) / (intval($tax) + 100)), 'FLOAT');
    //$query_params["discount_rate_cart"] = intval($order_main_infos->discount) + intval($order_main_infos->discount_abholung);

    // 查数据库: Positons
    $q_pos = "SELECT * FROM `" . $db_table_positions . "` WHERE `order_id` = %d";
    $order_poss_db = $wpdb->get_results($wpdb->prepare($q_pos, $id_db));



    /**
     * 这里要计算一个合适的Rabatt金额，来抹平与Afterbuy的误差
     */
    $discount_rate_goodone = (intval($order_main_infos->discount) + intval($order_main_infos->discount_abholung)) / 100;
    $productNettoPriceSum = 0;
    if(COUNT($order_poss_db) > 0){
        for($j=0; $j<COUNT($order_poss_db); ++$j){
            if(floatval($order_poss_db[$j]->price) >= 0){
                $amount = $helper->formatPrice(floatval($order_poss_db[$j]->price), 'FLOAT');
                $quantity = $order_poss_db[$j]->quantity_want;
                $productNettoPriceSum = $productNettoPriceSum + ($amount * $quantity);
            }
        }
    }
    $query_params["discount_amount_cart"] = $productNettoPriceSum - (floatval($paidSum) * 100 / (intval($tax) + 100));
    $query_params["discount_amount_cart"] = $helper->formatPrice(floatval($query_params["discount_amount_cart"]), 'FLOAT');



    if(COUNT($order_poss_db) > 0){
        for($i=0; $i<COUNT($order_poss_db); ++$i){

            if(floatval($order_poss_db[$i]->price) > 0){

                $item_name = $order_poss_db[$i]->title;
                $query_params["item_name_".($i+1)] = $item_name;

                //$amount = $helper->formatPrice(floatval($order_poss_db[$i]->price) * (100 + intval($tax)) / 100, 'FLOAT');
                $amount = $helper->formatPrice(floatval($order_poss_db[$i]->price), 'FLOAT');
                $query_params["amount_".($i+1)] = $amount;

                $ean = $order_poss_db[$i]->ean;
                $query_params["item_number_".($i+1)] = $ean;

                $quantity = $order_poss_db[$i]->quantity_want;
                $query_params["quantity_".($i+1)] = $quantity;

                //$tax_val = $helper->formatPrice((floatval($amount) * intval($tax) /  100), 'FLOAT');
                //$query_params["tax_".($i+1)] = $tax_val;

            }

        }
    }

    // 组装URL
    $paypal_payments_endpoint_url = $paypal_payments_endpoint . "?";
    $isFirst = true;
    foreach($query_params AS $key => $value){
        if($isFirst){
            $paypal_payments_endpoint_url .= $key . '=' . urlencode(stripslashes($value));
        }else{
            $paypal_payments_endpoint_url .= '&' . $key . '=' . urlencode(stripslashes($value));
        }
        $isFirst = false;
    }

    //var_dump($query_params);
    //var_dump($paypal_payments_endpoint_url);

    header('location:' . $paypal_payments_endpoint_url);
    exit();


}else{

    echo $helper->getMessagePageHtml("Die Bestellung wurde schon bezahlt.");
    exit;

}