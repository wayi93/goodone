<?php
/*
 * jQuery File Upload Plugin PHP Example
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * https://opensource.org/licenses/MIT
 */

error_reporting(E_ALL | E_STRICT);
session_start();
require('UploadHandler.php');
$orderID = $_SESSION["order-id-goodone"];
if(strlen($orderID) > 1){
    $upload_handler = new UploadHandler(null, true, null, $orderID);
}