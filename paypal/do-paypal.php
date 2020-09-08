<?php
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/7/6
 * Time: 10:56
 */
header("Content-type: text/html; charset=utf-8");
require_once ('Util/goodone_db_conn.php');
include_once ('Util/Helper.php');
use SoGood\api\Util\Helper;
$helper = new Helper();

/**
 * xiaobo 2020-09-03 initialize the logging
 * PHP code for logging error into a given file 
 */
// path of the log file where errors need to be logged 
$log_file = "./my-errors.log"; 
// setting error logging to be active 
ini_set("log_errors", TRUE);  
// setting the logging file in php.ini 
ini_set('error_log', $log_file); 

if(!isset($_GET["token"]) || !is_numeric(substr($_GET["token"], 0, 9))){
    /*
     * xiaobo 2020-09-03 disable 404 redirect to login
     * won't redirect customer to the internal goodone login site. 
     */
    // header('location: /404/');
    echo $helper->getMessagePageHtml("Invalidate token: ".$_GET["token"]."<br/><br/>
    	Please contact your customer support to retrieve the valid token.");
    exit();

}else{
    /**
     * 部署的时候需要修改的变量
     */
    $domain_url = "https://www.sogood.de";
    $domain_url_1 = "http://goodone.maimai24.de";
    $business_paypal_email = "info@maimai24.de";
    $paypal_payments_endpoint = "https://www.paypal.com/cgi-bin/webscr";
    if(false){
        $business_paypal_email = "company@sogood.de";
        $paypal_payments_endpoint = "https://www.sandbox.paypal.com/cgi-bin/webscr";
    }

    /**
     * 涉及到的数据库表
     */
    $db_table_orders = "ihattach_orders";
    $db_table_positions = "ihattach_positions";

    /**
     * 解析URL参数
     */
    $token = $_GET["token"];
    $id = substr($token, 0, 7);
    $id_db = intval($id) - 3000000;
    $verification_code = substr($token, 7, 2);

    /*
    var_dump($id);
    var_dump($id_db);
    var_dump($verification_code);
    */

    $sql = "SELECT * FROM `" . $db_table_orders . "` WHERE `meta_id` = " . $id_db;
    $result = $mysqli->query($sql);
    if($result === false){ //执行失败
        echo $mysqli->errno . "-" . $mysqli->error;
    }

    //行数
    if($result->num_rows != 1){

        echo $helper->getMessagePageHtml("Die Zahlungsinformation wurde nicht gefunden.");
        exit;

    }else{

        $order_main_infos = array();
        //获取数据
        while($row = $result->fetch_assoc()){
            $order_main_infos = $row;
        }

        /**
         * 验证：读取订单主表数据
         */
        $create_at = $order_main_infos["create_at"];
        $status = $order_main_infos["status"];
        $order_id_ab = $order_main_infos["order_id_ab"];

        /**
         * 检查验证位
         */
        $verification_code_db = substr(strval($create_at), 8, 2);
        if($verification_code_db != $verification_code){

            echo $helper->getMessagePageHtml("Die Zahlungsinformation wurde nicht gefunden.");
            exit;

        }else{
            /**
             * 检查是否已经付款
             */
            if($helper->checkContainStr($status, "Unbezahlt")){
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
                    "return" => $domain_url . "/api/payment-successful.php",
                    "cancel_return" => $domain_url . "/api/payment-cancelled.php",
                    "notify_url" => $domain_url_1 . "/api/paypalpaymentnotify",
                    "image_url" => $domain_url . "/api/images/logo_150x50.png",
                    "custom" => $helper->encrypt("jk89uOP!zO" . "#=#" . $id_db."#=#" . "9RtznoMmEy1", 'E', 'WaYi93')

                );

                /**
                 * 读取数据库主表信息
                 */
                $tax = $order_main_infos["tax"];
                $paidSum = $order_main_infos["paidSum"];
                $query_params["first_name"] = $order_main_infos["customer_shipping_firstName"];
                $query_params["last_name"] = $order_main_infos["customer_shipping_lastName"];
                $query_params["address_override"] = "1";
                $query_params["address1"] = $order_main_infos["customer_shipping_street"];
                if(strlen($order_main_infos["customer_shipping_street1"]) > 0){
                    $query_params["address2"] = $order_main_infos["customer_shipping_street1"];
                }
                $query_params["city"] = $order_main_infos["customer_shipping_city"];
                $query_params["country"] = $order_main_infos["customer_shipping_countryISO"];
                $query_params["email"] = $order_main_infos["goodone_customer_mail"];
                $query_params["zip"] = $order_main_infos["customer_shipping_postalCode"];
                $query_params["tax_cart"] = $helper->formatPrice((floatval($paidSum) * intval($tax) / (intval($tax) + 100)), 'FLOAT');
                $firstname = $order_main_infos["goodone_customer_firstName"];
                $lastname = $order_main_infos["goodone_customer_lastName"];

                /**
                 * 查询Positions的数据
                 */
                $sql_pos = "SELECT * FROM `" . $db_table_positions . "` WHERE `order_id` = " . $id_db;
                $result_pos = $mysqli->query($sql_pos);

                /**
                 * 获取Positons数据
                 */
                $order_poss_db = array();
                if($result_pos->num_rows > 0){
                    while($row = $result_pos->fetch_assoc()){
                        array_push($order_poss_db, $row);
                    }
                }
                //var_dump($order_poss_db);

                /**
                 * 这里要计算一个合适的Rabatt金额，来抹平与Afterbuy的误差
                 */
                $discount_rate_goodone = (intval($order_main_infos["discount"]) + intval($order_main_infos["discount_abholung"])) / 100;
                $productNettoPriceSum = 0;
                if(COUNT($order_poss_db) > 0){
                    for($j=0; $j<COUNT($order_poss_db); ++$j){
                        if(floatval($order_poss_db[$j]["price"]) >= 0){
                            $amount = $helper->formatPrice(floatval($order_poss_db[$j]["price"]), 'FLOAT');
                            $quantity = $order_poss_db[$j]["quantity_want"];
                            $productNettoPriceSum = $productNettoPriceSum + ($amount * $quantity);
                        }
                    }
                }
                $query_params["discount_amount_cart"] = $productNettoPriceSum - (floatval($paidSum) * 100 / (intval($tax) + 100));
                $query_params["discount_amount_cart"] = $helper->formatPrice(floatval($query_params["discount_amount_cart"]), 'FLOAT');

                /**
                 * 整理好Position的数据
                 */
/*
                if(COUNT($order_poss_db) > 0){
                    for($i=0; $i<COUNT($order_poss_db); ++$i){

                        if(floatval($order_poss_db[$i]["price"]) > 0){

                            $item_name = $order_poss_db[$i]["title"];
                            $query_params["item_name_".($i+1)] = $item_name;

                            //$amount = $helper->formatPrice(floatval($order_poss_db[$i]->price) * (100 + intval($tax)) / 100, 'FLOAT');
                            $amount = $helper->formatPrice(floatval($order_poss_db[$i]["price"]), 'FLOAT');
                            $query_params["amount_".($i+1)] = $amount;

                            $ean = $order_poss_db[$i]["ean"];
                            $query_params["item_number_".($i+1)] = $ean;

                            $quantity = $order_poss_db[$i]["quantity_want"];
                            $query_params["quantity_".($i+1)] = $quantity;

                        }

                    }
                }
*/
		if(COUNT($order_poss_db) > 0){
			$i_count = 0;
                    for($i=0; $i<COUNT($order_poss_db); ++$i){
                        if(floatval($order_poss_db[$i]["price"]) > 0){

                            $item_name = $order_poss_db[$i]["title"];
                            $query_params["item_name_".($i_count+1)] = $item_name;

                            //$amount = $helper->formatPrice(floatval($order_poss_db[$i]->price) * (100 + intval($tax)) / 100, 'FLOAT');
                            $amount = $helper->formatPrice(floatval($order_poss_db[$i]["price"]), 'FLOAT');
                            $query_params["amount_".($i_count+1)] = $amount;

                            $ean = $order_poss_db[$i]["ean"];
                            $query_params["item_number_".($i_count+1)] = $ean;

                            $quantity = $order_poss_db[$i]["quantity_want"];
                            $query_params["quantity_".($i_count+1)] = $quantity;
							
			    ++$i_count;

                        }

                    }
                }

                /**
                 * 组装URL
                 */
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
                //echo $paypal_payments_endpoint_url;
                
                /**
                 * xiaobo 2020-09-04 disable redirect to Paypal Checkout HTML
                 */
                // header('location:' . $paypal_payments_endpoint_url);

			    // xiaobo fuck begins here 
				// error_log("dupa token is ".$token); 
				// error_log("dupa id is ".$id);
				// error_log("dupa paypal checkout url is ".$paypal_payments_endpoint_url);
				echo $helper->getPaypalPageHtml($id, $firstname, $lastname, $paidSum); 
			    exit();
			    // xiaobo fuck ends here 

            }
            else 
            {
                echo $helper->getMessagePageHtml("Die Bestellung wurde schon bezahlt.");
                exit;
            }

        }

    }

    //关闭连接
    $mysqli->close();
}
