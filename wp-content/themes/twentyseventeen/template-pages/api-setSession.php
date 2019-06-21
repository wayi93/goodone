<?php
/* Template Name: Idealhit Api SetSession */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/4/25
 * Time: 16:24
 */
session_start();

if(isset($_POST["key"]) && isset($_POST["value"]) && isset($_POST["action"])){
    switch ($_POST["action"]){
        case "add":
            $_SESSION[$_POST["key"]] = $_POST["value"];
            break;
        case "delete":
            unset($_SESSION[$_POST["key"]]);
            break;
        default:
            //
    }
}else{

    //重定向浏览器
    header("Location: /404/");
    //确保重定向后，后续代码不会被执行
    exit;

}