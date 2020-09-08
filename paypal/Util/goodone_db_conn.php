<?php
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/7/6
 * Time: 11:37
 */

// 连接数据库
$db_host = 'localhost';
$db_name = 'goodone_db';
$db_user = 'magento2';
$db_pwd = 'magento2';

//面向对象方式
$mysqli = new mysqli($db_host, $db_user, $db_pwd, $db_name);
//面向对象的方式屏蔽了连接产生的错误，需要通过函数来判断
if(mysqli_connect_error()){
    echo mysqli_connect_error();
}

//设置编码
$mysqli->set_charset("utf8");//或者 $mysqli->query("set names 'utf8'");
