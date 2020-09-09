<?php
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/7/6
 * Time: 13:28
 */
header("Content-type: text/html; charset=utf-8");
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

$orderID = $_GET["orderID"];
$goodoneID = $_GET["goodoneID"];

// error_log("dupa payment-successful: orderID is ".$orderID);
// error_log("dupa payment-successful: goodoneID is ".$goodoneID);


echo $helper->getMessagePageHtml("Sie haben die Bestellung erfolgreich bezahlt.");


// curl goodone api "api/paypalpaymentnotify" with post param "custom"
// Status: Unbezahlt -> Versandvorbereitung
$url = "https://goodone.maimai24.de/api/paypalpaymentnotify";
$postFields = array(
    "payment_status" => "Completed",
    "goodoneID" => $goodoneID
);
$CURLOPT_HTTPHEADER_LIST = array(
    // 'Authorization: Basic ' . $authorization,
    'Accept: application/json',
    //'Content-Type: application/json'
);

$json = $helper->requestHttpApi($url, $postFields, $CURLOPT_HTTPHEADER_LIST);
error_log($json);