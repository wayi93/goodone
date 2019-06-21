<?php
/* Template Name: Idealhit Api GetProductStockHistoryByEAN */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/4/25
 * Time: 16:24
 */

include_once ( GET_STYLESHEET_DIRECTORY() . '/Util/Helper.php');
use SoGood\Support\Util\Helper;
$helper = new Helper();

/**
 * 判断，如果没有登陆，那么直接跳转到 Login 页面
 */
if(!is_user_logged_in()){
    //重定向浏览器
    header("Location: /do-login/");
    //确保重定向后，后续代码不会被执行
    exit;
}else{

$action_name = 'GetProductStockHistoryByEAN';
$isSuccess = false;
$msg = '';
$data = array();

if(isset($_POST["ean"]) && isset($_POST["zr"])){

    $product_stock_dir = dirname(dirname(dirname(dirname(__FILE__)))) . '/uploads/data/lagerbestand/';
    $data = $helper->getProductStockHistoryByEAN($_POST["ean"], $product_stock_dir, $_POST["zr"]);

	$data_without_null = array();

	foreach ($data as $k=>$v){
		if(!is_null($v)){
			array_push($data_without_null, $v);
		}
	}
	
    $isSuccess = true;
    if(COUNT($data_without_null) > 0){
        $msg = 'Die Daten wurden erfolgreich geladen.';
    }else{
        $msg = 'Keine Lagerbestand-Info von EAN <b>' . $_POST["ean"] . '</b> wurde gefunden.';
    }

}else{
    $isSuccess = false;
    $msg = 'There is no parameter.';
}

$results = array(
    'action' => $action_name,
    'isSuccess' => $isSuccess,
    'msg' => $msg,
    'data_quantity' => COUNT($data_without_null),
    'data' => $data_without_null
);

echo json_encode($results);


}