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

echo $helper->getMessagePageHtml("Zahlungsprozess wurde storniert.");
