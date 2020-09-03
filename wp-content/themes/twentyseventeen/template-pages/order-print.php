<?php
/* Template Name: Idealhit Order Print GoodOne Page */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/3/19
 * Time: 17:07
 */
include_once ( GET_STYLESHEET_DIRECTORY() . '/Util/Helper.php');
use SoGood\Support\Util\Helper;
$helper = new Helper();

/**
 * 得到当前用户信息, 并且根据不同的用户组跳转不同的页面
 */
global $current_user;
get_current_user();
$userGroup = $current_user->roles[0];

$url = home_url(add_query_arg(array()));

/**
 * order or quote
 */
$deal_with = "";
if($helper->checkContainStr($url, 'order')){
    $deal_with = "order";
}else if($helper->checkContainStr($url, 'quote')){
    $deal_with = "quote";
}else if($helper->checkContainStr($url, 'invoice')){
    $deal_with = "invoice";
}


$url_list = explode("/".$deal_with."-print/", $url);
$id = substr($url_list[1], 0, 7);
$id_db = intval($id) - 3000000;

$rechnungsnr = $id_db + 18070;

$db_table_orders = "ihattach_orders";
$db_table_positions = "ihattach_positions";

/**
 * 根据 order 和 quote 跟别准备变量
 */
$pageDW_title = "";
$pageDW_title_plus = "";
$nr_title = "";
$pdf_nr = "";
$text_1 = "";
$text_2 = "";
switch ($deal_with){
    case "order":
        $pageDW_title = "Bestellung";
        $pageDW_title_plus = "Die Bestellung";
        $nr_title = "Bestell-Nr.";
        $pdf_nr = $id;
        $text_1 = "Sehr geehrte Damen und Herren,";
        $text_2 = "vielen Dank für Ihre Bestellung. Wir freuen uns über Ihren Auftrag.<br>Details entnehmen Sie bitte der folgenden Übersicht:";
        break;
    case "quote":
        $pageDW_title = "Angebot";
        $pageDW_title_plus = "Das Angebot";
        $nr_title = "Angebot-Nr.";
        $pdf_nr = $id;
        $text_1 = "Sehr geehrte Damen und Herren,";
        $text_2 = "herzlichen Dank für Ihre Anfrage.<br>gerne unterbreiten wir Ihnen hiermit folgendes Angebot:";
        break;
    case "invoice":
        $pageDW_title = "Rechnung";
        $pageDW_title_plus = "Die Rechnung";
        $nr_title = "Rechnung-Nr.";
        $pdf_nr = $rechnungsnr;
        $text_1 = "Sehr geehrte Damen und Herren,";
        $text_2 = "vielen Dank für Ihre Bestellung und das uns entgegengebrachte Vertrauen.<br>Wir berechnen Ihnen hiermit wie folgt:";
        break;
    default:
        $pageDW_title = "";
        $pageDW_title_plus = "";
        $nr_title = "";
        $text_1 = "";
        $text_2 = "";
}

$gesamtsumme = 0;
$shippingCosts = 0;


if(strlen($url_list[1]) > 10){

    //重定向浏览器
    header("Location: /404/");
    //确保重定向后，后续代码不会被执行
    exit;

}else{

    /**
     * 读取数据库load订单主表数据
     */
    global $wpdb;

    $query = "SELECT * FROM `" . $db_table_orders . "` WHERE `meta_id` = %d";

    switch ($deal_with){
        case "order":
            $query = "SELECT * FROM `" . $db_table_orders . "` WHERE `meta_id` = %d AND `deal_with` = 'order'";
            break;
        case "quote":
            $query = "SELECT * FROM `" . $db_table_orders . "` WHERE `meta_id` = %d AND `first_deal_with` = 'quote'";
            break;
        case "invoice":
            $query = "SELECT * FROM `" . $db_table_orders . "` WHERE `meta_id` = %d AND `deal_with` = 'order'";
            break;
        default:
            $pageDW_title = "";
    }

    $order_main_infos_db = $wpdb->get_results($wpdb->prepare($query, $id_db));

    if(COUNT($order_main_infos_db) < 1){

        get_header();

        echo '<div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    ' . $pageDW_title . ' #';
        echo $id;
        echo '<small>Herzlich willkommen bei GoodOne Rechnungsplattform</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="/"><i class="fa fa-dashboard"></i>&nbsp;Home</a></li>
                    <li class="active">' . $pageDW_title . ' #';
        echo $id;
        echo '</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content container-fluid">
            
            <div class="row">
                    <div class="col-md-12">
                        <div class="pad margin no-print">
                            <div class="callout callout-warning" style="margin-bottom: 0!important;">
                                <h4><i class="fa fa-warning"></i> 404 Fehler:</h4>
                                '.$pageDW_title_plus.' #';
        echo $id;
        echo ' wurde nicht gefunden.
                            </div>
                        </div>
                    </div>
             </div>
            
</section>
            </div>';

        get_footer();

    }else {


        $order_main_infos = $order_main_infos_db[0];

        /**
         * 判断是否发送到了 Afterbuy
         * 如果 已经发送到了 Afterbuy 就从Afterbuy读，如果没有就从GoodOne数据库读
         */
        $verification_code_db = substr(strval($order_main_infos->create_at), 8, 2);
        $create_at = $order_main_infos->create_at;
        $create_at_datum = date("d.m.Y", $create_at);
        $create_by_id = $order_main_infos->create_by;
        $ud = get_userdata($create_by_id);
        $create_by_name = $ud->user_firstname . "&nbsp;" . $ud->user_lastname;
        $update_by_id = $order_main_infos->update_by;
        $update_by_name = "";
        if(intval($update_by_id) > 0){
            $ud1 = get_userdata($update_by_id);
            $update_by_name = $ud1->user_firstname . "&nbsp;" . $ud1->user_lastname;
        }
        $order_id_ab = $order_main_infos->order_id_ab;
        $discount = $order_main_infos->discount;
        $discount_abholung = $order_main_infos->discount_abholung;
        //$customer_id = $order_main_infos->customer_id;
        $goodone_customer_mail = $order_main_infos->goodone_customer_mail;
        $goodone_customer_firstName = $order_main_infos->goodone_customer_firstName;
        $goodone_customer_lastName = $order_main_infos->goodone_customer_lastName;
        // 账单地址
        $customer_company = $order_main_infos->customer_company;
        $customer_city = $order_main_infos->customer_city;
        $customer_countryISO = $order_main_infos->customer_countryISO;
        $customer_country = $order_main_infos->customer_country;
        $customer_fax = $order_main_infos->customer_fax;
        $customer_title = $order_main_infos->customer_title;
        $customer_firstName = $order_main_infos->customer_firstName;
        $customer_lastName = $order_main_infos->customer_lastName;
        $customer_phone = $order_main_infos->customer_phone;
        $customer_postalCode = $order_main_infos->customer_postalCode;
        $customer_street = $order_main_infos->customer_street;
        $customer_street1 = $order_main_infos->customer_street1;
        // 送货地址
        $customer_shipping_company = $order_main_infos->customer_shipping_company;
        $customer_shipping_city = $order_main_infos->customer_shipping_city;
        $customer_shipping_countryISO = $order_main_infos->customer_shipping_countryISO;
        $customer_shipping_country = $order_main_infos->customer_shipping_country;
        $customer_shipping_fax = $order_main_infos->customer_shipping_fax;
        $customer_shipping_title = $order_main_infos->customer_shipping_title;
        $customer_shipping_firstName = $order_main_infos->customer_shipping_firstName;
        $customer_shipping_lastName = $order_main_infos->customer_shipping_lastName;
        $customer_shipping_phone = $order_main_infos->customer_shipping_phone;
        $customer_shipping_postalCode = $order_main_infos->customer_shipping_postalCode;
        $customer_shipping_street = $order_main_infos->customer_shipping_street;
        $customer_shipping_street1 = $order_main_infos->customer_shipping_street1;
        // 其他
        $zahlungsmethode = $order_main_infos->payment_method;
        $versandart = $order_main_infos->shipping_method;
        $expectedDeliveryDate = $order_main_infos->expected_delivery_date;
        // 新增
        $memo = $order_main_infos->memo;
        $status = $order_main_infos->status;
        $status_quote = $order_main_infos->status_quote;
        $vat_nr = $order_main_infos->vat_nr;
        $order_tax = $order_main_infos->tax;
        $memo_big_account = $order_main_infos->memo_big_account;

        /**
         * 读取数据库load订单Positions数据
         */
        $query_pos = "SELECT * FROM `" . $db_table_positions . "` WHERE `order_id` = %d";
        $order_poss_db = $wpdb->get_results($wpdb->prepare($query_pos, $id_db));

        ?>


        <!--===================== page output =====================-->

        <!DOCTYPE html>
        <html>
        <head>
            <title>Dokument - GoodOne</title>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <!-- qrcode -->
            <script src="/wp-includes/js/qrcode.js"></script>
            <style>
                body {
                    width: 100%;
                    height: 100%;
                    margin: 0;
                    padding: 0;
                    background-color: #FAFAFA;
                    font: 12pt "Tahoma";
                }
                * {
                    box-sizing: border-box;
                    -moz-box-sizing: border-box;
                }
                .page {
                    width: 210mm;
                    min-height: 297mm;
                    padding: 15mm;
                    margin: 10mm auto;
                    border: 1px #D3D3D3 solid;
                    border-radius: 5px;
                    background: white;
                    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
                }
                .subpage {
                    /* padding: 1cm; */
                    /* border: 5px red solid; */
                    /* height: 267mm; */
                    /* outline: 2cm #FFEAEA solid; */
                }

                @page {
                    size: A4;
                    margin: 0;
                }
                @media print {
                    html, body {
                        width: 210mm;
                        height: 297mm;
                    }
                    .page {
                        margin: 0;
                        border: initial;
                        border-radius: initial;
                        width: initial;
                        min-height: initial;
                        box-shadow: initial;
                        background: initial;
                        page-break-after: always;
                    }
                }

                #logo { width: 66mm; }
                #logo > img { width: 100%; }

                .fLeft { float: left; }
                .fRight { float: right; }
                .clear { float:none; clear:both; }
                .right { text-align: right; }

                .font-size-12 { font-size: 12px; }
                .scale-82 {
                    -webkit-transform: scale(0.82) translate(-11%, 0px);
                    -moz-transform: scale(0.82) translate(-11%, 0px);
                    -ms-transform: scale(0.82) translate(-11%, 0px);
                    -o-transform: scale(0.82) translate(-11%, 0px);
                    transform: scale(0.82) translate(-11%, 0px);
                }

                .address-wrap { margin-top: 20px; }
                .doc-title { font-size: 28px; font-weight: bold; margin-top: -30px; }
                .txt-before-positions { margin-top: 20px; }
                .positions-wrap { margin: 30px 0 10px 0; }
                #pos-table > tbody > tr > td { padding: 10px 5px; border-bottom: 1px solid #000000; }
                #sum-table > tbody > tr > td { padding: 8px 5px; }
                .border-bottom { border-bottom: 1px solid #000000; }
                .border-bottom-double { border-bottom: double #000000; }
                .payment-method { margin-top: 30px; }
                .memo-for-customer { margin-top: 30px; }
                #doc-footer { margin-top: 50px; }

            </style>
        </head>
        <body>
        <div class="book">
            <div class="page">
                <div class="subpage font-size-12">
                    <div id="logo"><img src="/wp-content/uploads/images/logo_pdf.png" /></div>
                    <div class="scale-82" style="margin-top: 20px; text-decoration: underline;">Mai & Mai GmbH - Siemensstraße 13b - 63128 Dietzenbach</div>
                    <div class="address-wrap">
                        <div class="fLeft">
                            <?php
                            if (strlen($customer_company) > 0) {
                                echo "<strong>" . $customer_company . "</strong><br>";
                            }
                            ?>
                            <?= $customer_firstName ?>&nbsp;<?= $customer_lastName ?><br>
                            <?= $customer_street ?><br>
                            <?php
                            if(strlen($customer_street1) > 0){
                                echo $customer_street1 . '<br>';
                            }
                            ?>
                            <?= $customer_countryISO ?>-<?= $customer_postalCode ?>,&nbsp;<?= $customer_city ?>
                        </div>
                        <div class="fRight right">
                            <b>Mai & Mai GmbH</b><br>
                            Siemensstraße 13b<br>
                            63128 Dietzenbach<br>
                            Tel.: +49 6074 698 0066<br>
                            www.doporro.com<br>
                            <br>
                            <b><?=$nr_title;?>:</b> <?=$pdf_nr;?><br>
                            <b>Datum:</b> <?=$create_at_datum;?><br>
                            <?php
                            if($deal_with != "quote"){
                                echo '<b>Lieferdatum:</b> '.$expectedDeliveryDate;
                            }
                            ?>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="doc-title"><?=$pageDW_title;?></div>
                    <div class="txt-before-positions">
                        <span style="line-height: 30px;"><?=$text_1;?></span><br>
                        <?=$text_2;?>
                    </div>
                    <div class="positions-wrap">
                        <table id="pos-table" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center" valign="top">Pos</td>
                                <td align="left" valign="top">Beschreibung</td>
                                <td align="right" valign="top">Einzelpreis</td>
                                <td align="right" valign="top">Menge</td>
                                <td align="right" valign="top">Gesamtpreis</td>
                            </tr>

                            <?php
                            if(COUNT($order_poss_db) > 0){
                                for($i=0; $i<COUNT($order_poss_db); ++$i){
                                    $price = $order_poss_db[$i]->price;
                                    $quantity = $order_poss_db[$i]->quantity_want;
                                    $PxQ = $price * $quantity;

                                    $gesamtsumme = $gesamtsumme + (((round($price * (100 + $order_poss_db[$i]->tax))) / 100) * $quantity);

                                    $ean = $order_poss_db[$i]->ean;

                                    echo '<tr>
                                            <td align="center" valign="top">'.($i+1).'</td>
                                            <td align="left" valign="top"><b>'.$order_poss_db[$i]->title.'</b><br><span class="scale-82">Artikelnr.: '.$ean.'</span></td>
                                            <td align="right" valign="top">' . $helper->formatPrice($price, 'EUR') . '&nbsp;&euro;&nbsp;&nbsp;</td>
                                            <td align="right" valign="top">'.$quantity.'&nbsp;&nbsp;</td>
                                            <td align="right" valign="top">' . $helper->formatPrice($PxQ, 'EUR') . '&nbsp;&euro;&nbsp;&nbsp;</td>
                                        </tr>';

                                }
                            }
                            ?>

                        </table>
                    </div>
                    <div>
                        <table id="sum-table" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30%">&nbsp;</td>
                                <td width="30%">&nbsp;</td>
                                <td align="left" class="border-bottom">Zwischensumme</td>
                                <td align="right" class="border-bottom"><?php echo $helper->formatPrice($gesamtsumme - (round($gesamtsumme * 100 * $order_tax / (100 + $order_tax)) / 100), 'EUR'); ?>&nbsp;&euro;&nbsp;&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="30%">&nbsp;</td>
                                <td width="30%">&nbsp;</td>
                                <td align="left" class="border-bottom">zzgl. 19 % MwSt</td>
                                <td align="right" class="border-bottom"><?php echo $helper->formatPrice(round($gesamtsumme * 100 * $order_tax / (100 + $order_tax)) / 100, 'EUR'); ?>&nbsp;&euro;&nbsp;&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="30%">&nbsp;</td>
                                <td width="30%">&nbsp;</td>
                                <td align="left" class="border-bottom-double"><b>Gesamtsumme</b></td>
                                <td align="right" class="border-bottom-double"><?php echo $helper->formatPrice($gesamtsumme, 'EUR'); ?>&nbsp;&euro;&nbsp;&nbsp;</td>
                            </tr>
                        </table>
                    </div>
                    <div class="payment-method">
                        <div><b>Zahlungsmethode:</b> <?=$zahlungsmethode;?></div>
                        <?php
                        if($zahlungsmethode == "Paypal"){
                            echo '<div style="padding-top: 10px;">Mit Paypal zahlen Sie mittels QR-Code schnell und sicher.<br>Bitte scannen Sie den folgenden QR-Code:</div>';
                            echo '<div style="padding-top: 10px;"><img src="/wp-content/uploads/images/logo_paypal.png" /></div>';
                            echo '<div style="padding-top: 10px;"><span id="qrcode"></span></div>';

                            $value = 'https://www.sogood.de/api/do-paypal.php?token=' . $id . $verification_code_db . '/'; //二维码内容

                        }else if($zahlungsmethode == "Überweisung"){
                            //echo '<div style="padding-top: 10px;">Bitte geben Sie als Verwendungszweck bei Ihrer Überweisung den Text: <b>REC-'.$rechnungsnr.'</b> an.</div>';
                            //echo '<div style="padding-top: 10px;"><b>Bankverbindung:</b><br>Institut: Commerzbank<br>BLZ: 50040000<br>Konto: 413058900<br>BIC: COBADEFF004<br>IBAN: DE72 5004 0000 0413 0589 00<br>USt-IdNr.: DE265808049</div>';
                        }
                        ?>
                    </div>

                    <?php
                    /**
                     * 大客户备注
                     */
                    if(strlen($memo_big_account) > 0){
                        echo '<div class="memo-for-customer">' . $memo_big_account . '</div>';
                    }
                    ?>
                    <div id="doc-footer">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="20%" valign="top" class="scale-82">
                                    Mai &amp; Mai  GmbH<br>
                                    Siemensstrasse  13b<br>
                                    63218 Dietzenbach<br>
                                    Deutschland
                                </td>
                                <td width="20%" valign="top" class="scale-82">
                                    Tel.: +49 6074 6980066<br>
                                    Mail: info@maimai24.de
                                </td>
                                <td width="20%" valign="top" class="scale-82">
                                    Amtsgericht Offenbach<br>
                                    HRB 44229<br>
                                    Geschäftsführerin:<br>
                                    Wei Tao<br>
                                </td>
                                <td width="20%" valign="top" class="scale-82">
                                    Bankverbindung:<br>
                                    Commerzbank<br>
                                    BLZ: 50040000<br>
                                    Konto: 413058900<br>
                                </td>
                                <td width="20%" valign="top" class="scale-82">
                                    BIC: COBADEFF004<br>
                                    IBAN: DE72 5004 0000 0413 0589 00<br>
                                    UStIdNr: DE265808049<br>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        </body>
        </html>







        <?php
    }
}

?>

<script>
    var qrcode = new QRCode(document.getElementById("qrcode"), {
        width : 150,
        height : 150
    });
    qrcode.makeCode("<?=$value;?>");
</script>

<script>
    window.print();
    setTimeout('window.close()', 3000);
</script>
