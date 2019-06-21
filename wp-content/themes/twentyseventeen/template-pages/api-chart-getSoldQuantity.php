<?php
/* Template Name: Idealhit Api Chart GetSoldQuantity */
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

$action_name = 'GetSoldQuantity';
$isSuccess = false;
$msg = '';
$data = array();

    /**
     * 需要3个参数
     * $_POST["ean"]
     * $_POST["long"] 例如 365天
     * $_POST["interval"] 例如3   每三天显示一个销量
     */

if(isset($_POST["ean"]) && isset($_POST["long"]) && isset($_POST["interval"])){

    $csv_file_dir = dirname(dirname(dirname(dirname(__FILE__)))) . '/uploads/data/lagerbestand/';
    $data = $helper->getDreiTageVerkauftMenge($_POST["ean"], $_POST["long"], $_POST["interval"], $csv_file_dir);

    array_multisort(array_column($data,'date'),SORT_ASC, $data);

    $isSuccess = true;
    if(COUNT($data) > 0){
        $msg = 'Die Daten wurden erfolgreich geladen.';
    }else{
        $msg = 'Keine Daten wurde gefunden.';
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


}