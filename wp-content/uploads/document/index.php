<?php
/**
 * 空目录，跳转到404
 */

//重定向浏览器
header("Location: /404/");
//确保重定向后，后续代码不会被执行
exit;