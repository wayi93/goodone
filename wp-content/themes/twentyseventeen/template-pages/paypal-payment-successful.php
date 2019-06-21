<?php
/* Template Name: Idealhit Paypal Payment Successful Page */

include_once ( GET_STYLESHEET_DIRECTORY() . '/Util/Helper.php');
use SoGood\Support\Util\Helper;
$helper = new Helper();

echo $helper->getMessagePageHtml("Sie haben die Bestellung erfolgreich bezahlt.");