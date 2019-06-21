<?php
/* Template Name: Idealhit Api DoPassword */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/4/25
 * Time: 16:24
 */

require_once( './wp-includes/class-phpass.php');
include_once ( GET_STYLESHEET_DIRECTORY() . '/Util/Helper.php');
use SoGood\Support\Util\Helper;
$helper = new Helper();

global $current_user;
get_current_user();

/**
 * 判断，如果没有登陆，那么直接跳转到 Login 页面
 */
if(!is_user_logged_in()){
    //重定向浏览器
    header("Location: /do-login/");
    //确保重定向后，后续代码不会被执行
    exit;
}else{

$action_name = 'DoPassword';
$isSuccess = false;
$msg = '';
$data = array();

if(isset($_POST["action"]) && isset($_POST["password"])){

    $password = trim(rawurldecode($_POST["password"]));

    if($_POST["action"] == "update"){

        wp_set_password($password, $current_user->ID);
        $isSuccess = true;

    }else if($_POST["action"] == "match"){

        $auth_res = wp_authenticate($current_user->user_login, $password);
        if($auth_res->data->user_login == $current_user->user_login){
            $isSuccess = true;
            $msg = 'Passwort ist richtig.';
        }else{
            $isSuccess = false;
            $msg = 'Passwort ist falsch.';
        }

    }else{
        $isSuccess = false;
        $msg = 'Action does not exist';
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