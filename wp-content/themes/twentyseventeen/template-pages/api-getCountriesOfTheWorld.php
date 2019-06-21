<?php
/* Template Name: Idealhit Api GetCountriesOfTheWorld */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/4/25
 * Time: 16:24
 */

/**
 * 判断，如果没有登陆，那么直接跳转到 Login 页面
 */
if(!is_user_logged_in()){
    //重定向浏览器
    header("Location: /do-login/");
    //确保重定向后，后续代码不会被执行
    exit;
}else{


global $wpdb;

$action_name = 'GetCountriesOfTheWorld';
$isSuccess = false;
$msg = '';
$data = array();

$db_table_name = "ihattach_countries_of_the_world";
$sql = "SELECT `country`, `iso_2`, `in_common_use` FROM `".$db_table_name."` WHERE 1=1 ORDER BY `in_common_use` DESC";
$resultsInDB = $wpdb->get_results($sql);
if(COUNT($resultsInDB) > 0){
    foreach ($resultsInDB as $itm){
        $one_data = array(
            'country' => $itm->country,
            'iso_2' => $itm->iso_2,
            'in_common_use' => $itm->in_common_use
        );
        array_push($data, $one_data);
    }
    $isSuccess = true;
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