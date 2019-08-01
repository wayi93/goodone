<?php
/* Template Name: Idealhit Order Page */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/3/19
 * Time: 17:07
 */
//include_once ( GET_STYLESHEET_DIRECTORY() . '/Util/phpqrcode-2010100721_1.1.4/phpqrcode/phpqrcode.php');
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
 * order or quote or ersatzteil
 */
$deal_with = "";
if($helper->checkContainStr($url, 'order')){
    $deal_with = "order";
}else if($helper->checkContainStr($url, 'quote')){
    $deal_with = "quote";
}else if($helper->checkContainStr($url, 'ersatzteil')){
    $deal_with = "ersatzteil";
}

$url_list = explode("/".$deal_with."/", $url);
$id = substr($url_list[1], 0, 7);
$id_db = intval($id) - 3000000;
$db_table_orders = "ihattach_orders";
$db_table_positions = "ihattach_positions";
$db_table_ersatzteil_reasons = "ihattach_ersatzteil_reasons";

/**
 * Session
 * 上传文件的PHP会读这个变量
 */
session_start();
$_SESSION["order-id-goodone"] = $id;



/**
 * 根据 order 和 quote 跟别准备变量
 */
$pageDW_title = "";
$pageDW_title_plus = "";
$nr_title = "";
$pageDW_id = 0;
$order_btn_txt = '';
switch ($deal_with){
    case "order":
        $pageDW_title = "Bestellung";
        $pageDW_title_plus = "Die Bestellung";
        $nr_title = "Bestell-Nr.";
        $pageDW_id = 0;
        $order_btn_txt = 'Bestellung bestätigen';
        break;
    case "quote":
        $pageDW_title = "Angebot";
        $pageDW_title_plus = "Das Angebot";
        $nr_title = "Angebot-Nr.";
        $pageDW_id = 1;
        $order_btn_txt = 'Bestellen';
        break;
    case "ersatzteil":
        $pageDW_title = "Ersatzteilbestellung";
        $pageDW_title_plus = "Die Ersatzteilbestellung";
        $nr_title = "Ersatzteil-Best.-Nr.";
        $pageDW_id = 2;
        $order_btn_txt = 'Bestellen';
        break;
    default:
        $pageDW_title = "";
}


$gesamtsumme = 0;
$shippingCosts = 0;

if(strlen($url_list[1]) > 10){

    //重定向浏览器
    header("Location: /404/");
    //确保重定向后，后续代码不会被执行
    exit;

}else{

    get_header();

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
        case "ersatzteil":
            $query = "SELECT * FROM `" . $db_table_orders . "` WHERE `meta_id` = %d AND `deal_with` = 'ersatzteil'";
            break;
        default:
            $pageDW_title = "";
    }

    echo '<div id="order_id" style="display: none;">' . $id_db . '</div>';

    $order_main_infos_db = $wpdb->get_results($wpdb->prepare($query, $id_db));

    if(COUNT($order_main_infos_db) < 1){

        echo '<div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    ID #';
        echo $id;
        echo '<small>Herzlich willkommen bei GoodOne Rechnungsplattform</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="/"><i class="fa fa-dashboard"></i>&nbsp;Home</a></li>
                    <li class="active">ID #';
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
                                ' . $pageDW_title_plus . ' #';
        echo $id;
        echo ' wurde nicht gefunden.
                            </div>
                        </div>
                    </div>
             </div>
            
</section>
            </div>';

    }else {


        $order_main_infos = $order_main_infos_db[0];

        /**
         * 判断是否发送到了 Afterbuy
         * 如果 已经发送到了 Afterbuy 就从Afterbuy读，如果没有就从GoodOne数据库读
         */
        $verification_code_db = substr(strval($order_main_infos->create_at), 8, 2);
        $create_at = $order_main_infos->create_at;
        $create_by_id = $order_main_infos->create_by;
        $ud = get_userdata($create_by_id);
        $create_by_name = $ud->user_firstname . "&nbsp;" . $ud->user_lastname;
        $update_by_id = $order_main_infos->update_by;
        $update_by_name = "";
        if(intval($update_by_id) > 0){
            $ud1 = get_userdata($update_by_id);
            $update_by_name = $update_by_id == 99999999 ? "Kunden" : $ud1->user_firstname . "&nbsp;" . $ud1->user_lastname;
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
        $subtract_from_inventory = $order_main_infos->subtract_from_inventory;
        $show_customer_name_in_doc = $order_main_infos->show_customer_name_in_doc;
        $memo_kuaiji = $order_main_infos->memo_kuaiji;
        $afterbuy_account = $order_main_infos->afterbuy_account;


        /**
         * 读取数据库load订单Positions数据
         */
        $query_pos = "SELECT * FROM `" . $db_table_positions . "` WHERE `order_id` = %d";
        $order_poss_db = $wpdb->get_results($wpdb->prepare($query_pos, $id_db));

        /**
         * 读取数据库 订单 Rechungsnummer
         */
        $query_invoiceNr = "SELECT `number` FROM `ihattach_document_nrs` WHERE `order_id` = %d AND `type` = 'Rechnung' LIMIT 1";
        $invoiceNr_db = $wpdb->get_results($wpdb->prepare($query_invoiceNr, $id_db));
        $invoiceNr = 'N/A';
        if(COUNT($invoiceNr_db) > 0){
            $invoiceNr = $invoiceNr_db[0]->number;
        }

        ?>

        <!-- 隐藏数据 -->
        <div style="display: none;">
            <!-- Afterbuy Order ID -->
            <span id="order-id-ab"><?=$order_id_ab?></span>
            <!-- Order Customer Info -->
            <span id="order-info-KFirma-RA"><?=$customer_company?></span>
            <span id="order-info-KVorname-RA"><?=$customer_firstName?></span>
            <span id="order-info-KNachname-RA"><?=$customer_lastName?></span>
            <span id="order-info-KStrasse-RA"><?=$customer_street?></span>
            <span id="order-info-KStrasse2-RA"><?=$customer_street1?></span>
            <span id="order-info-KPLZ-RA"><?=$customer_postalCode?></span>
            <span id="order-info-KOrt-RA"><?=$customer_city?></span>
            <span id="order-info-KBundesland-RA"><?=$customer_country?></span>
            <span id="order-info-KBundesland-ISO-RA"><?=$customer_countryISO?></span>
            <span id="order-info-Ktelefon-RA"><?=$customer_phone?></span>
            <span id="order-info-Kemail"><?=$goodone_customer_mail?></span>
            <span id="order-info-KFirma-LA"><?=$customer_shipping_company?></span>
            <span id="order-info-KVorname-LA"><?=$customer_shipping_firstName?></span>
            <span id="order-info-KNachname-LA"><?=$customer_shipping_lastName?></span>
            <span id="order-info-KStrasse-LA"><?=$customer_shipping_street?></span>
            <span id="order-info-KStrasse2-LA"><?=$customer_shipping_street1?></span>
            <span id="order-info-KPLZ-LA"><?=$customer_shipping_postalCode?></span>
            <span id="order-info-KOrt-LA"><?=$customer_shipping_city?></span>
            <span id="order-info-KBundesland-LA"><?=$customer_shipping_country?></span>
            <span id="order-info-KBundesland-ISO-LA"><?=$customer_shipping_countryISO?></span>
            <span id="order-info-Ktelefon-LA"><?=$customer_shipping_phone?></span>
            <!-- Order Info -->
            <span id="order-show-name-in-doc"><?=$show_customer_name_in_doc?></span>
        </div>


        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <?=$pageDW_title;?>&nbsp;#<?=$id;?>
                    <small>Herzlich willkommen bei GoodOne Rechnungsplattform</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="/"><i class="fa fa-dashboard"></i>&nbsp;Home</a></li>
                    <li class="active"><?=$pageDW_title;?>&nbsp;#<?=$id;?></li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content container-fluid">

                <!--------------------------
                  | Your Page Content Here |
                  -------------------------->

                <div class="row">


                    <div class="col-md-12">

                        <!-- Rechnung start
                        <div class="pad margin no-print">
                            <div class="callout callout-info" style="margin-bottom: 0!important;">
                                <h4><i class="fa fa-info"></i> TIPP:</h4>
                                Um die Bestellungsbestätigung #<?= $id ?> aus zu drucken, klicken Sie bitte den Button,
                                der am unteren Ende der Rechnung liegt.
                            </div>
                        </div>-->

                        <!-- Main content -->
                        <section class="invoice">
                            <!-- title row -->
                            <div class="row">
                                <div class="col-xs-12">
                                    <h2 class="page-header" style="width: 100% !important;">
                                        <img class="fLeft" src="/wp-content/uploads/images/logo_277x50.png"/>
                                        <?php
                                        if(!$helper->checkContainStr($status, 'Storniert')){
                                            if($helper->canThisUserGroupUse($userGroup, 0) && $status != "Versandvorbereitung"){
                                                if($deal_with == 'order' || $status_quote != "Bestellt"){
                                                    echo '<span id="order-btn-bestellen" class="oder-page-top-btn pull-right btn btn-success" style="display:block; " onclick="retryCreateOrder('.$id_db.', '.$pageDW_id.', '. ($subtract_from_inventory=="NO"?0:1) .');"><i class="fa fa-file-text-o"></i>&nbsp;&nbsp;'.$order_btn_txt.'</span>';
                                                }
                                            }
                                            if($helper->canThisUserGroupUse($userGroup, 1) && $helper->checkContainStr($status, 'Unbezahlt')){
                                                echo '<span id="order-btn-bestellen" class="oder-page-top-btn pull-right btn btn-success" style="display:block; " onclick="retryCreateOrder_paid('.$id_db.', '.$pageDW_id.', '. ($subtract_from_inventory=="NO"?0:1) .', ' . ($status=='Unbezahlt'?0:1) . ', \''. $status .'\');"><i class="fa fa-credit-card"></i>&nbsp;&nbsp;Zahlung&nbsp;prüfen</span>';
                                            }
                                            if($helper->canThisUserGroupUse($userGroup, 2) && strlen($order_id_ab) < 5 && $deal_with != "quote"){
                                                echo '<span id="order-btn-cancel" class="oder-page-top-btn pull-right btn btn-danger" style="display:block; " onclick="cancelOrder('.$id_db.','.$pageDW_id.');"><i class="fa fa-remove"></i>&nbsp;&nbsp;Stornieren</span>';
                                            }
                                        }
                                        ?>

                                        <!-- 下一版本开发
                                        <span id="order-btn-bearbeiten" class="oder-page-top-btn pull-right btn btn-primary" style="display:block; " onclick="activeUpdateOrder();"><i class="fa fa-edit"></i>&nbsp;Bearbeiten</span>
                                        <span id="order-btn-abbrechen" class="oder-page-top-btn pull-right btn btn-primary" style="display:none; " onclick="deactiveUpdateOrder();"><i class="fa fa-undo"></i>&nbsp;Abbrechen</span>
                                        <span id="order-btn-speichern" class="oder-page-top-btn pull-right btn btn-success" style="display:none; "><i class="fa fa-save"></i>&nbsp;Speichern</span>
                                        -->

                                        <small class="pull-right" style="display:block; padding-top: 25px;"><b>Erstelldatum:</b> <?php echo date("d.m.Y H:i:s", $create_at); ?>
                                        </small>
                                    </h2>
                                </div>
                                <!-- /.col -->
                            </div>


                            <!-- 编辑订单相关的 Buttons -->
                            <div id="order-edit-btns-wrap" class="row padding-l-20 padding-b-10">
                                <?php
                                if($helper->canThisUserGroupUse($userGroup, 3) && strlen($order_id_ab) < 5){
                                    echo '<span id="order-btn-edit" class="pull-left btn btn-warning" style="display:block; " onclick="editOrder();"><i class="fa fa-edit"></i>&nbsp;&nbsp;Bearbeiten</span>';
                                    echo '<span id="order-btn-abbrechen" class="pull-left btn btn-warning margin-r-5" style="display:none; " onclick="cancelEditOrder();"><i class="fa fa-undo"></i>&nbsp;Abbrechen</span>';
                                    echo '<span id="order-btn-speichern" class="pull-left btn btn-success" style="display:none; " onclick="saveEditOrder('.$id_db.','.$pageDW_id.');"><i class="fa fa-save"></i>&nbsp;Speichern</span>';
                                }
                                ?>
                            </div>


                            <!-- info row -->
                            <div id="customer-current-info-wrap" class="row invoice-info">
                                <div class="col-sm-4 invoice-col">
                                    Rechnungsadresse
                                    <address>

                                        <?php
                                        //if($show_customer_name_in_doc == 'Y'){
                                        if($customer_firstName != 'N/A' || $customer_lastName != 'N/A'){
                                            echo '<strong>'.$customer_firstName.'&nbsp;'.$customer_lastName.'</strong>';
                                            echo '<br>';
                                        }
                                        ?>
                                        <?php
                                        if (strlen($customer_company) > 0) {
                                            echo "<strong>" . $customer_company . "</strong>";
                                            echo '<br>';
                                        }
                                        ?>
                                        <?= $customer_street ?>
                                        <?php
                                        if(strlen($customer_street1) > 0){
                                            echo '<br>' . $customer_street1;
                                        }
                                        ?>
                                        <br><?= $customer_city ?>,&nbsp;<?= $customer_countryISO ?>-<?= $customer_postalCode ?>
                                        <br><?= $customer_country ?>
                                        <br>Tel.: <?= $customer_phone ?>
                                        <br>E-Mail: <?= $goodone_customer_mail ?>
                                    </address>
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-4 invoice-col">
                                    Versandadresse
                                    <address>
                                        <?php
                                        //if($show_customer_name_in_doc == 'Y'){
                                        if($customer_shipping_firstName != 'N/A' || $customer_shipping_lastName != 'N/A'){
                                            echo '<strong>'.$customer_shipping_firstName.'&nbsp;'.$customer_shipping_lastName.'</strong>';
                                            echo '<br>';
                                        }
                                        ?>
                                        <?php
                                        if (strlen($customer_shipping_company) > 0) {
                                            echo "<strong>" . $customer_shipping_company . "</strong>";
                                            echo '<br>';
                                        }
                                        ?>
                                        <?= $customer_shipping_street ?>
                                        <?php
                                        if(strlen($customer_shipping_street1) > 0){
                                            echo '<br>' . $customer_shipping_street1;
                                        }
                                        ?>
                                        <br><?= $customer_shipping_city ?>,&nbsp;<?= $customer_shipping_countryISO ?>-<?= $customer_shipping_postalCode ?>
                                        <br><?= $customer_shipping_country ?>
                                        <br>Tel.: <?= $customer_shipping_phone ?>
                                        <br>E-Mail: <?= $goodone_customer_mail ?>
                                    </address>
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-4 invoice-col">
                                    <b>Status:</b> <span id="order_status"><?php
                                        switch ($deal_with){
                                            case 'order':
                                                echo $status;
                                                break;
                                            case 'quote':
                                                echo $status_quote;
                                                break;
                                            case 'ersatzteil':
                                                echo $status;
                                                break;
                                            default:
                                                //
                                        }
                                        ?></span><br>
                                    <?php
                                    if($subtract_from_inventory == "NO"){
                                        echo '<b>E2 Lagerbastand: </b> nicht ändern<br>';
                                    }else{
                                        echo '<b>E2 Lagerbastand: </b> ändern<br>';
                                    }
                                    ?>

                                    <?php if($deal_with == 'order'){
                                        ?>
                                        <br>
                                        <?php
                                    }?>

                                    <b><?=$nr_title;?>:</b> #<?= $id ?><br>

                                    <?php if($deal_with === 'order' || $deal_with === 'ersatzteil'){
                                        ?>
                                        <b>Afterbuy Bestell-Nr.:</b> <?php
                                        //echo $order_id_ab;
                                        if(strlen($order_id_ab) > 5){
                                            switch ($afterbuy_account)
                                            {
                                                case 'sogood':
                                                    echo '<a href="https://farm02.afterbuy.de/afterbuy/auktionsliste.aspx?art=edit&id=' . $order_id_ab . '" target="_blank">' . $order_id_ab . '&nbsp;<i class="fa fa-link"></i></a>';
                                                    break;
                                                default:
                                                    echo '<a href="https://farm04.afterbuy.de/afterbuy/auktionsliste.aspx?art=edit&id=' . $order_id_ab . '" target="_blank">' . $order_id_ab . '&nbsp;<i class="fa fa-link"></i></a>';
                                            }
                                        }else{
                                            echo $order_id_ab;
                                        }
                                        ?>&nbsp;(<?php
                                        switch ($afterbuy_account)
                                        {
                                            case 'sogood':
                                                echo 'SoGood';
                                                break;
                                            default:
                                                echo 'Mai&Mai';
                                        }
                                        ?>)<br/>
                                        <b>Rechnungsnr.:</b> <?=$invoiceNr?><br>
                                        <br>
                                        <?php
                                    }?>

                                    <b>Erstellt von:</b> <?= $create_by_name ?><br>
                                    <?php
                                    if($update_by_name != ""){
                                        echo '<b>Aktualisiert von:</b> ' . $update_by_name . '<br>';
                                    }
                                    ?>
                                    <?php
                                    if(intval($discount) > 0){
                                        //echo '<br><b>Rabatt:</b> <span style="color: #FF0000; font-weight: bold;">' . $discount . '%</span>';
                                    }
                                    if(intval($discount_abholung) > 0){
                                        //echo '<br><b>Rabatt Abholung:</b> <span style="color: #FF0000; font-weight: bold;">' . $discount_abholung . '%</span>';
                                    }

                                    if(strlen($vat_nr) > 0){
                                        echo '<br><b>MwSt-Nr. von Kunde:</b> <span>' . $vat_nr . '</span>';
                                    }
                                    ?>
                                    <br><br>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->


                            <!-- info edit row -->
                            <div id="customer-info-wrap" class="row padding-10" style="display: none"></div>

                            <!-- Table row -->
                            <div class="row">
                                <div class="col-xs-12 table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>EAN</th>
                                            <th>Produktname</th>
                                            <th style="text-align: center;">Anzahl</th>
                                            <th style="text-align: center;">Verfügbar</th>
                                            <th style="text-align: right;">Preis (netto)</th>
                                            <!--<th style="text-align: right;">MwSt.</th>-->
                                            <th style="text-align: right;">Zwischensumme (netto)</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        <?php
                                        $tax = 0;
                                        if(COUNT($order_poss_db) > 0){
                                            for($i=0; $i<COUNT($order_poss_db); ++$i){
                                                $price = $order_poss_db[$i]->price;
                                                $quantity = $order_poss_db[$i]->quantity_want;
                                                $PxQ = $price * $quantity;

                                                $gesamtsumme = $gesamtsumme + (((round($price * (100 + $order_poss_db[$i]->tax))) / 100) * $quantity);

                                                $ean = $order_poss_db[$i]->ean;
                                                $rabattStyle = '';
                                                if($ean == '8888888888888' || $ean == '9999999999999'){
                                                    $rabattStyle = ' font-weight: bold; color: #FF0000; ';
                                                }else{
                                                    $tax = $order_poss_db[$i]->tax;
                                                }

                                                /**
                                                 * 初始化库存，loading 还是 N/A
                                                 */
                                                $td_verfuegbar_init = '<img src="/wp-content/uploads/images/loading-spin-1s-200px.svg" style="height: 16px !important;" />';
                                                if(!$helper->checkStrInString('42512429', $ean)){
                                                    $td_verfuegbar_init = '-';
                                                }

                                                /**
                                                 * 提取质量问题原因
                                                 */
                                                $reasons = $order_poss_db[$i]->reasons;
                                                $reasonsHTML = '';
                                                if($deal_with === 'ersatzteil'){
                                                    $query_est_reasons = "SELECT `reason` FROM `" . $db_table_ersatzteil_reasons . "` WHERE `meta_id` IN (" . $reasons . ")";
                                                    $reasonsFromDB = $wpdb->get_results($query_est_reasons);

                                                    if(COUNT($reasonsFromDB) > 0){

                                                        $howManyReasonsText = '1 Grund';
                                                        if(COUNT($reasonsFromDB) > 1){
                                                            $howManyReasonsText = COUNT($reasonsFromDB) . ' Gründe';
                                                        }

                                                        $reasonsHTML .= '<br/><div style="margin-top: 8px; font-style:italic;">' . $howManyReasonsText . ' zum Ersatzteil</div><ul style="margin-left: 25px; font-style:italic;">';
                                                        for($irs=0; $irs<COUNT($reasonsFromDB); ++$irs){
                                                            $reasonsHTML .= '<li>' . $reasonsFromDB[$irs]->reason . '</li>';
                                                        }
                                                        $reasonsHTML .= '</ul>';

                                                    }
                                                }

                                                echo '<tr>
                                                    <td>' . $ean . '</td>
                                                    <td>' . $order_poss_db[$i]->title . $reasonsHTML . '</td>
                                                    <td style="text-align: center;">' . $order_poss_db[$i]->quantity_want . '</td>
                                                    <td style="text-align: center;" id="td-verfuegbar-' . ($i+1) . '">' . $td_verfuegbar_init . '</td>
                                                    <td style="text-align: right; '.$rabattStyle.'">' . $helper->formatPrice($price, 'EUR') . '&nbsp;&euro;</td>
                                                    <!--<td style="text-align: right;">' . $tax . '%</td>-->
                                                    <td style="text-align: right; '.$rabattStyle.'">' . $helper->formatPrice($PxQ, 'EUR') . '&nbsp;&euro;</td>
                                                </tr>';

                                            }
                                        }
                                        ?>

                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->

                            <!-- 订单 Positions 下面的信息 -->
                            <div class="row padding-t-20">

                                <div class="col-xs-8">

                                    <?php if($deal_with == "order"){ ?>
                                    <div class="col-xs-6">
                                        <p class="lead">Zahlungsinfo:</p>
                                        <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                            <b>Zahlungsmethode:</b> <?=$zahlungsmethode?>
                                            <?php
                                            if($zahlungsmethode == 'Überweisung'){
                                                echo '<br><b>Bankverbindung:</b>'.
                                                    '<br><b>Institut:</b> Commerzbank'.
                                                    '<br><b>BLZ:</b> 50040000'.
                                                    '<br><b>Konto:</b> 413058900'.
                                                    '<br><b>BIC:</b> COBADEFF004'.
                                                    '<br><b>IBAN:</b> DE72 5004 0000 0413 0589 00'.
                                                    '<br><b>USt-IdNr.:</b> DE265808049';
                                            }
                                            if($zahlungsmethode == 'Paypal'){
                                                echo '<br>Mit Paypal zahlen Sie mittels QR-Code schnell und sicher.';
                                                echo '<br>Bitte scannen Sie den folgenden QR-Code:';

                                                $value = 'https://www.sogood.de/api/do-paypal.php?token=' . $id . $verification_code_db . '/'; //二维码内容
                                                /*
                                                $errorCorrectionLevel = 'L'; //容错级别
                                                $matrixPointSize = 6; //生成图片大小
                                                QRcode::png($value, __DIR__ . '\..\..\..\uploads\images\qrcode\paypal-'.$id.'.png', $errorCorrectionLevel, $matrixPointSize, 2);
                                                */

                                                echo '<br><img style="padding-top: 8px; width: 150px; " src="/wp-content/uploads/images/logo_paypal.png" />';
                                                echo '<br><span id="qrcode" style="padding: 10px;"></span>';
echo '<script>
var qrcode = new QRCode(document.getElementById("qrcode"), {
	width : 150,
	height : 150
});
qrcode.makeCode("'.$value.'");
</script>';

                                                echo '<br>Als Alternative können Sie auch den folgenden Link im Computer besuchen:';
                                                echo '<br>'.$value;

                                            }

                                                //test
                                                //echo "<a href='". $value ."' target='_blank'>" . $value . "</a>";

                                            ?>
                                        </p>
                                    </div>
                                    <?php } ?>

                                    <?php if($deal_with == "order"){ ?>
                                    <div class="col-xs-6">
                                        <p class="lead">Lieferungsinfo:</p>
                                        <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                            <b>Versandart:</b> <?=$versandart?><br>
                                            <b>Voraussichtliches Lieferdatum:</b> <?=$expectedDeliveryDate?>
                                        </p>
                                    </div>
                                    <?php } ?>

                                    <?php //if(strlen($memo_big_account) > 0){ ?>
                                        <div class="col-xs-6">
                                            <?php if($userGroup == $ud->roles[0]){ ?>
                                                <p id="p-Rechnungsvermerk-btns" class="lead">Rechnungsvermerk: <span class="btn btn-default" style="font-size: 12px;" onclick="activeMemoEditMode(1);">Bearbeiten</span></p>
                                            <?php }else{ ?>
                                                <p id="p-Rechnungsvermerk-btns" class="lead">Rechnungsvermerk:</p>
                                            <?php } ?>
                                            <p id="p-Rechnungsvermerk" class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                                <?=$memo_big_account?>
                                            </p>
                                        </div>
                                    <?php //} ?>

                                    <?php if($deal_with === 'order' || $deal_with === 'ersatzteil'){ ?>
                                        <?php //if(strlen($memo) > 0){ ?>
                                            <div class="col-xs-6">
                                                <?php if($userGroup == 'accounting' || $userGroup == $ud->roles[0]){ ?>
                                                    <p id="p-Memo-btns" class="lead">Memo: <span class="btn btn-default" style="font-size: 12px;" onclick="activeMemoEditMode(0);">Bearbeiten</span></p>
                                                <?php }else{ ?>
                                                    <p id="p-Memo-btns" class="lead">Memo:</p>
                                                <?php } ?>
                                                <p id="p-Memo" class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                                    <?=$memo?>
                                                </p>
                                            </div>
                                        <?php //} ?>
                                    <?php } ?>

                                    <?php if($deal_with !== 'quote'){ ?>
                                        <?php //if(strlen($memo_kuaiji) > 0){ ?>
                                            <div class="col-xs-6">
                                                <?php if($userGroup == 'accounting'){ ?>
                                                    <p id="p-Buchhaltungsmemo-btns" class="lead">Buchhaltungsmemo: <span class="btn btn-default" style="font-size: 12px;" onclick="activeMemoEditMode(2);">Bearbeiten</span></p>
                                                <?php }else{ ?>
                                                    <p id="p-Buchhaltungsmemo-btns" class="lead">Buchhaltungsmemo:</p>
                                                <?php } ?>

                                                <p id="p-Buchhaltungsmemo" class="text-muted well well-sm no-shadow" style="margin-top: 10px;" >
                                                    <?=$memo_kuaiji?>
                                                </p>
                                            </div>
                                        <?php //} ?>
                                    <?php } ?>

                                </div>

                                <div class="col-xs-4">
                                    <p class="lead">Zusammenfassung</p>

                                    <div class="table-responsive">
                                        <table class="table">
                                            <tr>
                                                <th style="width:50%">Zwischensumme:</th>
                                                <td style="text-align: right;"><?php echo $helper->formatPrice($gesamtsumme - (round($gesamtsumme * 100 * $order_tax / (100 + $order_tax)) / 100), 'EUR'); ?>&nbsp;&euro;</td>
                                            </tr>
                                            <tr>
                                                <th>Mwst. <?=$order_tax?>%:</th>
                                                <td style="text-align: right;"><?php
                                                    echo $helper->formatPrice(round($gesamtsumme * 100 * $order_tax / (100 + $order_tax)) / 100, 'EUR');
                                                    ?>&nbsp;&euro;</td>
                                            </tr>
                                            <tr>
                                                <th>Gesamtsumme:</th>
                                                <td style="text-align: right;"><?php
                                                    echo $helper->formatPrice($gesamtsumme, 'EUR');
                                                    ?>&nbsp;&euro;</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.col -->

                            </div>
                            <!-- 订单 Positions 下面的信息 -->


                            <!-- this row will not appear when printing-->
                            <div id="order-btn-print-wrap" class="row no-print" style="margin-top: 30px;">
                                <div class="col-xs-12">
                                    <!--<a href="/<?=$deal_with;?>-print/<?=$id;?>/" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> Drucken</a>
                                    &nbsp;&nbsp;-->
                                    <?php
                                    if($deal_with === 'quote'){
                                        echo '<a href="/document/'.$id.'0/" target="_blank" class="btn btn-default" style="margin-left: 12px;"><i class="fa fa-print"></i>&nbsp;&nbsp;Angebot&nbsp;drucken</a>';
                                        //echo '<a href="/quote-print/'.$id.'/" target="_blank" class="btn btn-default" style="margin-left: 12px;"><i class="fa fa-print"></i>&nbsp;&nbsp;Angebot&nbsp;drucken</a>';
                                    }
                                    if($deal_with === 'order' || $deal_with ==='ersatzteil'){
                                        echo '<a href="/document/'.$id.'1/" target="_blank" class="btn btn-default" style="margin-left: 12px;"><i class="fa fa-print"></i>&nbsp;&nbsp;Auftragsbestätigung&nbsp;drucken</a>';
                                        //echo '<a href="/order-print/'.$id.'/" target="_blank" class="btn btn-default" style="margin-left: 12px;"><i class="fa fa-print"></i>&nbsp;&nbsp;Auftragsbestätigung&nbsp;drucken</a>';
                                    }
                                    if(($deal_with === 'order' && strlen($order_main_infos->order_id_ab) > 7) || $deal_with ==='ersatzteil'){
                                        if(!$helper->checkStrInString('Abholung', $versandart)){
                                            echo '<a href="#" onclick="return printDeliveryNoteConfirm(' . trim($id) . '2,'.$id_db.',' . ($afterbuy_account === 'sogood' ? '1' : '2') . ')" class="btn btn-default" style="margin-left: 12px;"><i class="fa fa-print"></i>&nbsp;&nbsp;Lieferschein&nbsp;drucken</a>';
                                        }else{
                                            echo '<a href="/document/'.$id.'2/" target="_blank" class="btn btn-default" style="margin-left: 12px;"><i class="fa fa-print"></i>&nbsp;&nbsp;Lieferschein&nbsp;drucken</a>';
                                        }
                                    }
                                    //if($deal_with == "order" && strlen($order_main_infos->order_id_ab) > 9){
                                    if($deal_with === 'order' || $deal_with ==='ersatzteil'){
                                        echo '<a href="/document/'.$id.'3/" target="_blank" class="btn btn-default" style="margin-left: 12px;"><i class="fa fa-print"></i>&nbsp;&nbsp;Rechnung&nbsp;drucken</a>';
                                        //echo '<a href="/invoice-print/'.$id.'/" target="_blank" class="btn btn-default" style="margin-left: 12px;"><i class="fa fa-print"></i>&nbsp;&nbsp;Rechnung&nbsp;drucken</a>';
                                    }
                                    ?>
                                    <a class="btn btn-default" onclick="openAnhangWindow('<?=$id;?>','<?=$pageDW_title;?>');" style="margin-left: 10px;"><i class="fa fa-folder-open-o"></i>&nbsp;Anhang&nbsp;(&nbsp;<span id="file-quantity-<?=$id;?>"><img src="/wp-content/uploads/images/loading-spin-1s-200px.svg" style="height: 16px !important;" /></span>&nbsp;)</a>
                                </div>
                        </section>
                        <!-- /.content -->

                        <div class="clearfix"></div>
                        <!-- Rechnung end -->




                        <!-- test Operation History -->
                        <?php
                        //if($current_user->user_login == 'ying'){
                            $doc_type_area = '999';
                            switch ($deal_with){
                                case 'order':
                                    $doc_type_area = '0, 1';
                                    break;
                                case 'quote':
                                    $doc_type_area = '1';
                                    break;
                                case 'ersatzteil':
                                    $doc_type_area = '4';
                                    break;
                                default:
                                    //
                            }
                            $query_history = "SELECT `create_at`, `message`, `create_by` FROM `ihattach_operation_history` WHERE `order_id` = %d AND `doc_type` in (" . $doc_type_area . ") ORDER BY `create_at` DESC";
                            $history_list = $wpdb->get_results($wpdb->prepare($query_history, $id_db));

// 显示表格
echo '<section class="invoice">
<div style="font-size: 30px; color:#3c8dbc; border-bottom: 1px solid #eee; padding-bottom: 10px;">Operation History</div>
<div id="operation-history-table-wrap" class="padding-b-10">';

    if(count($history_list) > 0){
        echo '<table>
                <tr>
                    <th><b>Zeit</b></th>
                    <th><b>Message</b></th>
                    <th><b>Benutzer</b></th>
                </tr>';
        for($ihl = 0; $ihl < count($history_list); ++$ihl){

            $ud_history = get_userdata($history_list[$ihl]->create_by);
            $username_history = $ud_history->user_firstname . "&nbsp;" . $ud_history->user_lastname;
            if($history_list[$ihl]->create_by == 88888888){
                $username_history = 'System';
            }else if($history_list[$ihl]->create_by == 99999999){
                $username_history = 'Kunden';
            }

            echo '<tr>
                <td>' . date('d.m.Y H:i', $history_list[$ihl]->create_at) . '</td>
                <td>' . $history_list[$ihl]->message . '</td>
                <td>' . $username_history . '</td>
            </tr>';
        }

        echo '</table>';
    }else{
        echo '<br/>Keine Daten gefunden.';
    }

echo '</div>
</section>';


                        //}
                        ?>





                    </div>


                </div>


            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <script>
            updateFileQuantityOrderPage("<?=$id;?>");
        </script>

        <?php



        /**
         * 刷产品库存
         */
        if(COUNT($order_poss_db) > 0){
            for($j=0; $j<COUNT($order_poss_db); ++$j){

                $ean = $order_poss_db[$j]->ean;
                if($helper->checkStrInString('42512429', $ean) || $helper->checkStrInString('42507553', $ean) || $helper->checkStrInString('777777777777', $ean)){
                    // send Ajax 显示库存
                    echo '<script>setVerfuegbarByEAN("' . $ean . '", "td-verfuegbar-' . ($j+1) . '");</script>';
                }

            }
        }

        /**
         * 从 Afterbuy 读取 Rechnungsvermerk 和 Memo
         */
        if(strlen($order_id_ab) > 3){
            echo '<script>loadMemoAndRVFromAB("' . $order_id_ab . '");</script>';
        }

    }
}
get_footer();