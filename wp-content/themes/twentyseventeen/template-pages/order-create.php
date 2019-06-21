<?php
/* Template Name: Idealhit Order Create Page */
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
 * 确定用户组
 */
global $current_user;
get_current_user();
$userGroup = $current_user->roles[0];
$username = $current_user->user_login;

$pageDW = $helper->getPageDealWith();
$pageDW_title = "";
$css_class_typ = "";
switch ($pageDW){
    case "order":
        $pageDW_title = "Bestellung";
        $css_class_typ = "primary";
        break;
    case "quote":
        $pageDW_title = "Angebot";
        $css_class_typ = "danger";
        break;
    default:
        $pageDW_title = "";
        $css_class_typ = "primary";
}

get_header();
?>

<!-- Content Wrapper. Contains page content -->
<div id="content-wrapper" class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?=$pageDW_title?>&nbsp;erstellen
            <small>Herzlich willkommen bei GoodOne Rechnungsplattform</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-dashboard"></i>&nbsp;Home</a></li>
            <li><?=$pageDW_title?></li>
            <li class="active">Erstellen</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

        <!--------------------------
          | Your Page Content Here |
          -------------------------->

        <div class="row">
            <div id="create-order-steps-wrap" class="col-md-11">

                <div id="create-order-step-1" class="box box-<?=$css_class_typ;?> padding-10-10-20-10">

                    <div class="box-header">
                        <h3 class="box-title"><b><?=$pageDW_title?> Schritt 1:</b> Produkt suchen und zum Warenkorb hinzufügen</h3>
                    </div>

                    <div id="order-create-step-1-body" class="box-body">

                        <img src="/wp-content/uploads/images/loading-spinning-circles.svg" />

                    </div>

                    <script>loadProducts("<?=$pageDW;?>-create");</script>


                </div>

                <div id="create-order-step-2" class="box box-<?=$css_class_typ;?> padding-10-10-20-10">

                    <div class="box-header">
                        <h3 class="box-title"><b><?=$pageDW_title?> Schritt 2:</b> Angaben zum Käufer</h3>
                    </div>

                    <div class="box-body">

                        <div class="row">

                            <!-- Kundeninfo Search Form -->
                            <div class="col-md-12">
                                <div class="box">

                                    <!-- box header start -->
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Kundensuche</h3>
                                    </div>
                                    <!-- box header end -->

                                    <div class="box-body">

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="search-kunden-Kid">Kunden-ID</label>
                                                <input class="form-control" id="search-kunden-Kid" placeholder="Kunden-ID" onkeyup="searchKundenBlockOtherInput();" />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="search-kunden-Knachname">Nachname</label>
                                                <input class="form-control" id="search-kunden-Knachname" placeholder="Nachname des Kunden" />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="search-kunden-Kvorname">Vorname</label>
                                                <input class="form-control" id="search-kunden-Kvorname" placeholder="Vorname des Kunden" />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="search-kunden-Kemail">E-Mail</label>
                                                <input class="form-control" id="search-kunden-Kemail" placeholder="E-Mail des Kunden" />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="search-kunden-Ktel">Tel.</label>
                                                <input class="form-control" id="search-kunden-Ktel" placeholder="Tel. des Kunden" />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="search-kunden-Kplz">PLZ</label>
                                                <input class="form-control" id="search-kunden-Kplz" placeholder="PLZ des Kunden" />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button id="btn-search-kunden" type="button" class="btn btn-primary">suchen</button>
                                        </div>

                                    </div>

                                    <!-- box header start -->
                                    <div id="search-kunden-result-wrap" class="box-footer with-border" style="display: none;"></div>
                                    <!-- box header end -->

                                </div>
                            </div>

                            <!-- Kundeninfo Form -->
                            <div class="col-md-4" id="form-wrap-kundeninfo">

                                <div class="box">
                                    <!-- box header start -->
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Kundeninfo</h3>
                                    </div>
                                    <!-- box header end -->
                                    <!-- form start -->
                                    <form role="form">
                                        <div class="box-body">
                                            <div class="form-group">
                                                <label for="Kemail">E-Mail Adresse</label>
                                                <input type="email" class="form-control" id="Kemail" onkeyup="afterInputCustomerEmail();" placeholder="E-Mail des Käufers">
                                            </div>
                                            <div class="form-group">
                                                <label for="KVorname">Vorname *</label>
                                                <input type="text" class="form-control" id="KVorname" placeholder="Vorname des Käufers">
                                            </div>
                                            <div class="form-group">
                                                <label for="KNachname">Nachname *</label>
                                                <input type="text" class="form-control" id="KNachname" placeholder="Nachname des Käufers">
                                            </div>
                                            <div class="form-group">
                                                <label>
                                                    <input class="iCheck-helper" type="checkbox" id="cb-iTaxFree">&nbsp;&nbsp;Ist mehrwertsteuerfrei?
                                                </label>
                                                <label>
                                                    <input class="iCheck-helper" type="checkbox" id="cb-wVNiPDF" onclick="switch_cb_wVNiPDF();" checked>&nbsp;&nbsp;Sollen Vor- und Nachname in der PDF Dokument angezeigt werden?
                                                </label>
                                                <?php //if($username == 'ying'){ ?>
                                                <?php //if(false){ ?>
                                                <label style="visibility:hidden;">
                                                    <input class="iCheck-helper" type="checkbox" id="cb-kRB" onclick="updateRabattSelectOptions();">&nbsp;&nbsp;Rabatt Beschränkung entfernen?
                                                </label>
                                                <?php //} ?>
                                            </div>
                                            <div id="KVatNr-wrap" class="form-group" style="display: none;">
                                                <div>
                                                    <label for="KNachname">MwSt-Nr. (VAT): *</label>
                                                    <input type="text" class="form-control" id="KVatNr" placeholder="Mehrwertsteuernummer (VAT-Nummer)">
                                                    <span style="color: #FF0000;">Um MwSt. auf 0% zu setzen, bitte geben Sie MwSt-Nr. ein.</span>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- form end -->
                                </div>

                            </div>

                            <!-- Rechnungsanschrift Form -->
                            <div class="col-md-4" id="form-wrap-rechnungsanschrift">

                                <div class="box">
                                    <!-- box header start -->
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Rechnungsanschrift</h3>
                                    </div>
                                    <!-- box header end -->
                                    <!-- form start -->
                                    <form role="form">
                                        <div class="box-body">
                                            <div class="form-group">
                                                <label for="KFirma-RA">Firma</label>
                                                <input type="text" class="form-control" id="KFirma-RA" placeholder="Firmenname des Empfängers">
                                            </div>
                                            <div class="form-group">
                                                <label for="KVorname-RA">Vorname *</label>
                                                <input type="text" class="form-control" id="KVorname-RA" placeholder="Vorname des Empfängers">
                                            </div>
                                            <div class="form-group">
                                                <label for="KNachname-RA">Nachname *</label>
                                                <input type="text" class="form-control" id="KNachname-RA" placeholder="Nachname des Empfängers">
                                            </div>
                                            <div class="form-group">
                                                <label for="KStrasse-RA">Straße und Hausnummer *</label>
                                                <input type="text" class="form-control" id="KStrasse-RA" placeholder="Straße und Hausnummer">
                                            </div>
                                            <div class="form-group">
                                                <label for="KStrasse2-RA">Adresszusatz</label>
                                                <input type="text" class="form-control" id="KStrasse2-RA" placeholder="Zusatzinfo der Adresse">
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-4" style="padding-left: unset !important;">
                                                    <label for="KPLZ-RA">PLZ *</label>
                                                    <input type="text" class="form-control" id="KPLZ-RA" placeholder="PLZ">
                                                </div>
                                                <div class="col-md-4" style="padding-left: unset !important;">
                                                    <label for="KOrt-RA">Ort *</label>
                                                    <input type="text" class="form-control" id="KOrt-RA" placeholder="Ort">
                                                </div>
                                                <div class="col-md-4" style="padding-left: unset !important; padding-right: unset !important; padding-bottom: 15px !important;">
                                                    <label for="KBundesland-RA">Land *</label>
                                                    <select id="KBundesland-RA" class="form-control" style="border-radius: 3px;">
                                                        <option>Loading ...</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="Ktelefon-RA">Telefonnummer</label>
                                                <input type="text" class="form-control" id="Ktelefon-RA" placeholder="Telefonnummer des Empfängers">
                                            </div>
                                            <div class="checkbox" style="padding-top: 8px;">
                                                <label>
                                                    <input class="iCheck-helper" type="checkbox" id="cb-iLavR"> Ist Lieferanschrift abweichend von Rechnungsanschrift?
                                                </label>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- form end -->
                                </div>

                            </div>

                            <!-- Lieferanschrift Form -->
                            <div class="col-md-4" id="form-wrap-lieferanschrift">

                                <div class="box" id="form-wrap-lieferanschrift-box">
                                    <!-- box header start -->
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Lieferanschrift</h3>
                                    </div>
                                    <!-- box header end -->
                                    <!-- form start -->
                                    <form role="form">
                                        <div class="box-body">
                                            <div class="form-group">
                                                <label for="KFirma-LA">Firma</label>
                                                <input type="text" class="form-control" id="KFirma-LA" placeholder="Firmenname des Empfängers">
                                            </div>
                                            <div class="form-group">
                                                <label for="KVorname-LA">Vorname *</label>
                                                <input type="text" class="form-control" id="KVorname-LA" placeholder="Vorname des Empfängers">
                                            </div>
                                            <div class="form-group">
                                                <label for="KNachname-LA">Nachname *</label>
                                                <input type="text" class="form-control" id="KNachname-LA" placeholder="Nachname des Empfängers">
                                            </div>
                                            <div class="form-group">
                                                <label for="KStrasse-LA">Straße und Hausnummer *</label>
                                                <input type="text" class="form-control" id="KStrasse-LA" placeholder="Straße und Hausnummer">
                                            </div>
                                            <div class="form-group">
                                                <label for="KStrasse2-LA">Adresszusatz</label>
                                                <input type="text" class="form-control" id="KStrasse2-LA" placeholder="Zusatzinfo der Adresse">
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-4" style="padding-left: unset !important;">
                                                    <label for="KPLZ-LA">PLZ *</label>
                                                    <input type="text" class="form-control" id="KPLZ-LA" placeholder="PLZ">
                                                </div>
                                                <div class="col-md-4" style="padding-left: unset !important;">
                                                    <label for="KOrt-LA">Ort *</label>
                                                    <input type="text" class="form-control" id="KOrt-LA" placeholder="Ort">
                                                </div>
                                                <div class="col-md-4" style="padding-left: unset !important; padding-right: unset !important; padding-bottom: 15px !important;">
                                                    <label for="KBundesland-LA">Land *</label>
                                                    <select id="KBundesland-LA" class="form-control" style="border-radius: 3px;">
                                                        <option>Loading ...</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="Ktelefon-LA">Telefonnummer</label>
                                                <input type="text" class="form-control" id="Ktelefon-LA" placeholder="Telefonnummer des Empfängers">
                                            </div>
                                        </div>
                                    </form>
                                    <!-- form end -->
                                </div>

                            </div>

                        </div>

                    </div>


                </div>

                <div id="create-order-step-3" class="box box-<?=$css_class_typ;?> padding-10-10-20-10">

                    <div class="box-header">
                        <h3 class="box-title"><b><?=$pageDW_title?> Schritt 3:</b> Bezahlung & Lieferung</h3>
                    </div>

                    <div class="box-body">

                        <div class="row">
                            <div class="col-md-4">

                                <div class="box">
                                    <div class="box-body box-profile">

                                        <ul class="list-group list-group-unbordered">
                                            <li class="list-group-item mouseoverhand" data-toggle="control-sidebar" style="border-top: none;">
                                                <span class="padding-l-20">Preissumme (netto)</span> <a class="pull-right padding-r-20">&euro;</a><input type="text" id="input-netto-preissumme" class="pull-right input-summe-block" value="0" disabled />
                                            </li>
                                            <li id="li-rabatt" class="list-group-item mouseoverhand" data-toggle="control-sidebar">
                                                <span class="padding-l-20">Rabatt</span> <a class="pull-right padding-r-20">&euro;</a><input type="text" id="input-summe-rabatt" class="pull-right input-summe-block discount-price" style="font-weight: bold;" value="0" disabled />
                                            </li>
                                            <li id="li-rabatt-bei-abholung" class="list-group-item mouseoverhand" data-toggle="control-sidebar">
                                                <span class="padding-l-20">Rabatt bei Abholung</span> <a class="pull-right padding-r-20">&euro;</a><input type="text" id="input-rabatt-abholung" class="pull-right input-summe-block discount-price" style="font-weight: bold;" value="0" disabled />
                                            </li>
                                            <li class="list-group-item mouseoverhand" data-toggle="control-sidebar" style="display: none;">
                                                <span class="padding-l-20">Versand & Bearbeitung</span> <a class="pull-right padding-r-20">&euro;</a><input type="text" id="input-summe-ver-Bear" class="pull-right input-summe-block" value="0" disabled />
                                            </li>
                                            <li class="list-group-item mouseoverhand" data-toggle="control-sidebar">
                                                <span class="padding-l-20"><b>Zwischensumme (netto)</b></span> <a class="pull-right padding-r-20">&euro;</a><input type="text" id="input-summe-zwischensumme" class="pull-right input-summe-block" style="font-weight: bold;" value="0" disabled />
                                            </li>
                                            <li class="list-group-item mouseoverhand" data-toggle="control-sidebar"">
                                                <span class="padding-l-20">MwSt.</span> <a class="pull-right padding-r-20">&euro;</a><input type="text" id="input-mwst" class="pull-right input-summe-block" value="0" disabled />
                                            </li>
                                            <li class="list-group-item mouseoverhand" data-toggle="control-sidebar" style="border-bottom: none;">
                                                <span class="padding-l-20"><b>Gesamtsumme (brutto)</b></span> <a class="pull-right padding-r-20">&euro;</a><input type="text" id="input-summe-gesamtsumme" class="pull-right input-summe-block" style="font-weight: bold;" value="0" disabled />
                                            </li>
                                        </ul>

                                    </div>
                                    <!-- /.box-body -->
                                </div>

                            </div>

                            <div class="col-md-4" <?php
                            if($pageDW == "quote"){
                                echo 'style="display:none;"';
                            }
                            ?> >

                                <div class="box">
                                    <div class="box-body box-profile">
                                        <!-- Date -->
                                        <div class="form-group">
                                            <div class="padding-t-10">
                                                <label for="input-versandart">Versandart</label>
                                                <div class="input-group date">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-truck"></i>
                                                    </div>
                                                    <input type="text" class="form-control pull-right" id="input-versandart" value="Zusendung" disabled />

                                                    <div class="input-group-btn">
                                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">setzen&nbsp;<span class="fa fa-caret-down"></span></button>
                                                        <ul class="dropdown-menu">
                                                            <li><a onclick="setInputVal('input-versandart', 'Abholung OR');setRabattBeiAbholung(0);">Abholung OR</a></li>
                                                            <li><a onclick="setInputVal('input-versandart', 'Abholung MR');setRabattBeiAbholung(1);">Abholung MR -1%</a></li>
                                                            <li><a onclick="setInputVal('input-versandart', 'Abholung MR');setRabattBeiAbholung(2);">Abholung MR -2%</a></li>
                                                            <li><a onclick="setInputVal('input-versandart', 'Abholung MR');setRabattBeiAbholung(3);">Abholung MR -3%</a></li>
                                                            <li><a onclick="setInputVal('input-versandart', 'Abholung MR');setRabattBeiAbholung(4);">Abholung MR -4%</a></li>
                                                            <li><a onclick="setInputVal('input-versandart', 'Abholung MR');setRabattBeiAbholung(5);">Abholung MR -5%</a></li>
                                                            <li><a onclick="setInputVal('input-versandart', 'Abholung MR');setRabattBeiAbholung(6);">Abholung MR -6%</a></li>
                                                            <li><a onclick="setInputVal('input-versandart', 'Abholung MR');setRabattBeiAbholung(7);">Abholung MR -7%</a></li>
                                                            <li><a onclick="setInputVal('input-versandart', 'Abholung MR');setRabattBeiAbholung(8);">Abholung MR -8%</a></li>
                                                            <li><a onclick="setInputVal('input-versandart', 'Abholung MR');setRabattBeiAbholung(9);">Abholung MR -9%</a></li>
                                                            <li><a onclick="setInputVal('input-versandart', 'Abholung MR');setRabattBeiAbholung(10);">Abholung MR -10%</a></li>
                                                            <li><a onclick="setInputVal('input-versandart', 'Zusendung');setRabattBeiAbholung(0);">Zusendung</a></li>
                                                        </ul>
                                                    </div>

                                                </div>
                                            </div>
                                            <!-- /.input group -->
                                            <div class="padding-t-10" style="display: none;">
                                                <label for="input-zusatzinfo">Zusatzinfo ( z.B.: Paket Tracking Nummer )</label>
                                                <div class="input-group date">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-info-circle"></i>
                                                    </div>
                                                    <input type="text" class="form-control pull-right" id="input-zusatzinfo" />
                                                </div>
                                            </div>
                                            <!-- /.input group -->
                                            <div class="padding-t-10">
                                                <label for="v-l-datepicker">Voraussichtliches Lieferdatum</label>
                                                <div class="input-group date">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                    <input type="text" class="form-control pull-right" id="v-l-datepicker" />
                                                </div>
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                        <!-- /.form group -->
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="box">
                                    <div class="box-body box-profile">

                                        <div class="padding-t-10">
                                            <label for="select-rabatt">Rabatt</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-sort-numeric-desc"></i></span>
                                                <select id="select-rabatt" onchange="setRabatt(this);updatePaymentSummary();" onkeyup="updatePaymentSummary();" class="form-control" style="padding-left: 5px; width: 100%; height: 34px; cursor: pointer;">
                                                    <option value="0">kein&nbsp;Rabatt</option>
                                                </select>
                                                <?php
                                                $s_r_o_arr = $helper->getRabattStufenByUserGroup($current_user->roles[0]);
                                                echo '<script>';
                                                foreach ($s_r_o_arr as $k => $v){
                                                    echo 'rabattStufen['.$k.'] = '.$v.';';
                                                }
                                                echo '</script>';
                                                ?>
                                            </div>
                                        </div>

                                        <div class="padding-t-10" style="display: none;">
                                            <label for="input-versandkosten">Versandkosten</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-truck"></i></span>
                                                <input type="text" class="form-control" id="input-versandkosten" onkeyup="updateVersandkosten(this);" value="0" />
                                            </div>
                                        </div>



                                        <div class="padding-t-10" <?php
                                        if($pageDW == "quote"){
                                            echo 'style="display:none;"';
                                        }
                                        ?> >
                                            <label for="input-zahlungsmethode">Zahlungsmethode *</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-credit-card"></i></span>
                                                <input type="text" class="form-control" id="input-zahlungsmethode" disabled />

                                                <div class="input-group-btn">
                                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">setzen&nbsp;<span class="fa fa-caret-down"></span></button>
                                                    <ul class="dropdown-menu">
                                                        <li><a onclick="setInputVal('input-zahlungsmethode', 'Barzahlung');showPaymentMethodTipp(0);">Barzahlung</a></li>
                                                        <li><a onclick="setInputVal('input-zahlungsmethode', 'EC-Karte');showPaymentMethodTipp(1);">EC-Karte</a></li>
                                                        <li><a onclick="setInputVal('input-zahlungsmethode', 'Überweisung');showPaymentMethodTipp(2);">Überweisung</a></li>
                                                        <li><a onclick="setInputVal('input-zahlungsmethode', 'Paypal');showPaymentMethodTipp(3);">Paypal</a></li>
                                                    </ul>
                                                </div>

                                            </div>
                                        </div>

                                        <div id="payment-method-tipp-wrap" class="padding-t-10"></div>



                                        <div class="height20">&nbsp;</div>

                                    </div>
                                </div>

                            </div>






<!--
                            <div class="col-md-3">

                                <div class="box">
                                    <div class="box-body box-profile">

                                        <div class="padding-t-10">
                                            <label for="input-zahlungsmethode">Zahlungsmethode *</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-credit-card"></i></span>
                                                <input type="text" class="form-control" id="input-zahlungsmethode" disabled />

                                                <div class="input-group-btn">
                                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">setzen&nbsp;<span class="fa fa-caret-down"></span></button>
                                                    <ul class="dropdown-menu">
                                                        <li><a onclick="setInputVal('input-zahlungsmethode', 'Barzahlung');showPaymentMethodTipp(0);">Barzahlung</a></li>
                                                        <li><a onclick="setInputVal('input-zahlungsmethode', 'EC-Karte');showPaymentMethodTipp(1);">EC-Karte</a></li>
                                                        <li><a onclick="setInputVal('input-zahlungsmethode', 'Überweisung');showPaymentMethodTipp(2);">Überweisung</a></li>
                                                        <li><a onclick="setInputVal('input-zahlungsmethode', 'Paypal');showPaymentMethodTipp(3);">Paypal</a></li>
                                                    </ul>
                                                </div>

                                            </div>
                                        </div>

                                        <div id="payment-method-tipp-wrap" class="padding-t-10"></div>

                                        <div class="padding-t-10" style="display: none;">
                                            <label for="input-bezahlt">Teil bezahlt</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-euro"></i></span>
                                                <input type="text" class="form-control" id="input-bezahlt" value="0" />
                                            </div>
                                        </div>

                                        <div class="height20">&nbsp;</div>

                                    </div>
                                </div>

                            </div>

-->










                        </div>

                    </div>


                </div>

                <div id="create-order-step-4" class="box box-<?=$css_class_typ;?> padding-10-10-20-10">

                    <div class="box-header">
                        <h3 class="box-title"><b><?=$pageDW_title?> Schritt 4:</b> Notiz</h3>
                    </div>

                    <div class="box-body">

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="order-comment">Memo für <?=$pageDW_title;?> ( maximale Länge: 800 Zeichen )</label>
                                    <textarea id="order-comment" class="form-control" rows="3" style="max-width: 100%; min-width: 100%;" placeholder="Memo wird im Afterbuy gespeichert werden." onkeyup="calculateNotizTxtLen('order-comment');"></textarea>
                                    <span id="notiz-txt-len-1"></span>
                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="order-comment">Rechnungsvermerk für <?=$pageDW_title;?> ( maximale Länge: 800 Zeichen )</label>
                                    <textarea id="order-comment-kunden" class="form-control" rows="3" style="max-width: 100%; min-width: 100%;" placeholder="Rechnungsvermerk wird in der Rechnung angezeigt werden." onkeyup="calculateNotizTxtLen('order-comment-kunden');"></textarea>
                                    <span id="notiz-txt-len-2"></span>
                                </div>

                            </div>

                        </div>

                    </div>


                </div>

                <div id="create-order-btn" class="padding-10-10-20-10 center">
                    <button type="button" class="btn btn-primary" style="width: 300px; font-size: 18px; font-weight: bold;" onclick="createOrder('<?=$pageDW?>');"><?=$pageDW_title?>&nbsp;erstellen</button>
                </div>

                <div class="height-800">&nbsp;</div>

            </div>

            <div id="step-line-control" class="col-md-1">
                <div id="goto-create-order-step-1-label" class="goto-create-order-step-label" onclick="scrollPage(1)">Schritt&nbsp;1</div>
                <div id="goto-create-order-step-2-label" class="goto-create-order-step-label" onclick="scrollPage(0)">Schritt&nbsp;2</div>
                <div id="goto-create-order-step-3-label" class="goto-create-order-step-label" onclick="scrollPage(2)">Schritt&nbsp;3</div>
                <div id="goto-create-order-step-4-label" class="goto-create-order-step-label" onclick="scrollPage(3)">Schritt&nbsp;4</div>
                <div id="goto-create-order-step-4-label" style="display: none;" class="goto-create-order-step-label" onclick="scrollPage(4)">Schritt&nbsp;5</div>
            </div>

        </div>


    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
    // 控制运输地址信息表单
    switchLieferanschriftForm("off");
    // 加载国家信息
    if(localStorage.getItem("countries-of-the-world") !== null){
        countries = JSON.parse(localStorage.getItem("countries-of-the-world"));
        fillCountrySelect("KBundesland-RA");
        fillCountrySelect("KBundesland-LA");
    }else{
        getCountriesAjax();
    }

    /**
     * 事件监听
     */
    // 搜索客户按钮事件
    var btn_sk = document.getElementById("btn-search-kunden");
    if(btn_sk.addEventListener)
        btn_sk.addEventListener("click",searchKunden,false);
    if(btn_sk.attachEvent)
        btn_sk.attachEvent("onclick",searchKunden);
    //
    $('#cb-iLavR').click(
        function () {
            if(this.checked){
                switchLieferanschriftForm("on");
            }else{
                switchLieferanschriftForm("off");
                syncAdresseR2L();
            }
        }
    );
    //
    $('#cb-iTaxFree').click(
        function () {
            if(this.checked){
                // 增值税 0%
                setTaxFrontend(0);
            }else{
                // 增值税 19%
                setTaxFrontend(19);
            }
        }
    );
    // Event fot Function: syncInput - Parameters
    var syncInputIds = [
        ['KFirma-RA', 'KFirma-LA', false],
        ['KStrasse-RA', 'KStrasse-LA', false],
        ['KStrasse2-RA', 'KStrasse2-LA', false],
        ['KPLZ-RA', 'KPLZ-LA', false],
        ['KOrt-RA', 'KOrt-LA', false],
        ['KBundesland-RA', 'KBundesland-LA', false],
        ['Ktelefon-RA', 'Ktelefon-LA', false]
    ];
    // Event fot Function: syncInput - Define
    for(var i=0; i<syncInputIds.length; ++i){
        var fId = syncInputIds[i][0];
        document.getElementById(fId).onkeyup = (function closure(j){
            return function () {
                var fId = syncInputIds[j][0];
                var tId = syncInputIds[j][1];
                var bS = syncInputIds[j][2];
                syncInput(fId, tId, bS);
            }
        })(i);
    }

    /**
     * one input to two inputs
     */
    $('#KVorname').keyup(function () {
        syncInput('KVorname', 'KVorname-RA', true);
        syncInput('KVorname', 'KVorname-LA', false);
    });
    $('#KNachname').keyup(function () {
        syncInput('KNachname', 'KNachname-RA', true);
        syncInput('KNachname', 'KNachname-LA', false);
    });
    /**
     * select
     */
    $('#KBundesland-RA').change(function () {
        syncInput('KBundesland-RA', 'KBundesland-LA', false);
    });

    /**
     * Scroll监听
     */
    $(window).scroll(function(){
        var scrollTop = $(this).scrollTop();
        $('#step-line-control').css('paddingTop', scrollTop);
    });

    /**
     * 初始化时间选择 Voraussichtliches Lieferdatum
     */
    initDatepicker('v-l-datepicker');

    /**
     * 定义价格格式
     * http://flaviosilveira.com/Jquery-Price-Format/
     */
    var inputsArr = ["input-summe-zwischensumme","input-summe-ver-Bear","input-summe-gesamtsumme","input-versandkosten","input-bezahlt","input-summe-rabatt","input-rabatt-abholung","input-mwst"];
    for(var i in inputsArr){
        switch (inputsArr[i]){
            case 'input-summe-ver-Bear':
                setInputPrice(inputsArr[i], (orderInfoArray.versandkosten * 100));
                break;
            case 'input-versandkosten':
                setInputPrice(inputsArr[i], (orderInfoArray.versandkosten * 100));
                break;
            case 'input-bezahlt':
                setInputPrice(inputsArr[i], (orderInfoArray.teilbezahlt * 100));
                break;
            default:
                setInputPrice(inputsArr[i], 0);

        }
    }

    /**
     * Update
     */
    updatePaymentSummary();



</script>

<?php get_footer();
