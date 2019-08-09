<?php
/* Template Name: Idealhit Gutschrift Create Page */
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
    case "gutschrift":
        $pageDW_title = "Gutschrift";
        $css_class_typ = "primary";
        break;
    default:
        $pageDW_title = "";
        $css_class_typ = "primary";
}

get_header();

?>

    <style>
        table tr:nth-child(odd){ background: #F7F7F7; }
        table td:nth-child(even){ }
        table tr:hover{ background-color: #858585; color: #ffffff; }
    </style>
    <!-- 读取Gutschrift原因 -->
    <script>doErsatzteil(3,0,'NULL','gutschrift');</script>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <?=$pageDW_title?> erstellen
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

                <div id="cPanel" class="col-md-12">

                    <!-- 重复模块 -->
                    <div class="box box-<?=$css_class_typ;?>" style="padding: 10px;">
                        <div class="box-header">
                            <h3 class="box-title">1. Schritt: Afterbuy Order-ID eingeben</h3>
                        </div>
                        <div class="box-body box-profile">

                            <div class="form-group">

                                <div class="col-md-4" style="max-width: 165px; padding-left: unset !important; padding-right: unset !important; padding-right: 20px !important;">
                                    <select id="ab-konto" class="form-control" style="border-radius: 3px;">
                                        <option disabled="" value="0" selected>Afterbuy Konto</option>
                                        <option value="sogood">Sogood</option>
                                        <option value="maimai">Mai & Mai</option>
                                    </select>
                                </div>
                                <div class="col-md-4" style="max-width: 160px; padding-left: unset !important;">
                                    <input type="text" class="form-control" id="order-id" placeholder="Afterbuy Order-ID" value=""
                                           onkeyup="autoSelectAfterbuyAccount();ersatzteilCreatePageClear();"
                                           onchange="autoSelectAfterbuyAccount();ersatzteilCreatePageClear();"
                                    >
                                </div>
                                <div class="col-md-4" style="padding-left: unset !important;">
                                    <button id="btn-search-ab-oder" type="button" class="btn btn-primary" onclick="searchOrderFromAfterbuy();">Bestellung suchen</button>
                                </div>

                            </div>

                        </div>
                        <div class="box-body box-profile hinweis">
                            &nbsp;&nbsp;<b>Hinweis:</b>&nbsp;&nbsp;Afterbuy Konto muss nicht manuell ausgewählt werden.
                            <br/>&nbsp;&nbsp;<span style="color: #ffffff;"><b>Hinweis:</b>&nbsp;&nbsp;</span>Es kann nach Angabe der Order-ID automatisch erkannt werden.
                            <br/>&nbsp;&nbsp;<span style="color: #ffffff;"><b>Hinweis:</b>&nbsp;&nbsp;</span>( zu 99% richtig )
                        </div>

                    </div>
                    <!-- 重复模块 -->

                    <!-- 重复模块 -->
                    <div id="block-customer-info" class="box box-<?=$css_class_typ;?>" style="padding: 10px; display: none;">
                        <div class="box-header">
                            <h3 class="box-title">2.&nbsp;Schritt:&nbsp;Angaben&nbsp;zum&nbsp;Käufer&nbsp;&nbsp;[&nbsp;<span id="afterbuy-customer-id" style="color: #337ab7; font-weight: bold;"><img src="/wp-content/uploads/images/loading-spin-1s-200px.svg" style="height: 16px !important;" /></span>&nbsp;]</h3>
                        </div>
                        <div class="box-body box-profile">

                            <div id="customer-info-wrap"></div>

                        </div>

                    </div>
                    <!-- 重复模块 -->

                    <!-- 重复模块 -->
                    <div id="block-gutschrift-info" class="box box-<?=$css_class_typ;?>" style="padding: 10px; display: none;">
                        <div class="box-header">
                            <h3 class="box-title">3. Schritt: Gutschrift-Info</h3>
                        </div>
                        <div class="box-body box-profile">

                            <div class="row padding-l-10 padding-r-20">
                                <div class="col-md-6">
                                    <div id="gutschrift-btn-gesendet" class="info-box bg-aqua gutschrift-btn" onclick="GutschriftManager.updateBtns(true);">
                                        <span class="info-box-icon"><i class="fa fa-truck"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-number" style="line-height: 44px;">Die ware wurde schon an den Kunden versendet.</span>

                                            <div class="progress">
                                                <div class="progress-bar" style="width: 100%"></div>
                                            </div>
                                            <span class="progress-description">Bitte die Tracking Nummer der Rücksendung eingeben, wann es immer möglich ist.</span>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div id="gutschrift-btn-nichtgesendet" class="info-box bg-aqua gutschrift-btn" onclick="GutschriftManager.updateBtns(false);">
                                        <span class="info-box-icon"><i class="fa fa-bank"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-number" style="line-height: 44px;">Die ware steht derzeit noch im Lager.</span>

                                            <div class="progress">
                                                <div class="progress-bar" style="width: 100%"></div>
                                            </div>
                                            <span class="progress-description">Bitte die Gründe für Gutschrift auswählen.</span>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                </div>
                            </div>
                            <script>GutschriftManager.updateBtns(gutschriftWareGesendet);</script>

                            <div class="row padding-l-10 padding-r-20">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gutschrift-betrag">Gutschriftsbetrag * ( Bruttopreis z.B.: <span style="color: #FF0000;">36,5</span> )</label>
                                        <input type="text" class="form-control" id="gutschrift-betrag" placeholder="">
                                    </div>
                                </div>
                                <div id="tracking-nr-ruecksendung-wrap" class="col-md-6">
                                    <div class="form-group">
                                        <label for="tracking-nr-ruecksendung">Tracking Nummer der Rücksendung</label>
                                        <input type="text" class="form-control" id="tracking-nr-ruecksendung" placeholder="">
                                    </div>
                                </div>
                                <div id="gutschrift-reason-wrap" class="col-md-6" style="display: none;">
                                    <div class="form-group">
                                        <label for="gutschrift-reason">Gründe zur Gutschrift *</label>
                                        <select id="gutschrift-reason" multiple="multiple" style="width: 100%; height: 34px; color: #333333 !important;"></select>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                    <!-- 重复模块 -->

                    <!-- 重复模块 -->
                    <div id="block-ersatzteil-info" class="box box-<?=$css_class_typ;?>" style="padding: 10px; display: none;">
                        <div class="box-header">
                            <h3 class="box-title">4. Schritt: Produkte oder Teile wählen, welche die Gutschrift verursacht haben.</h3>
                        </div>
                        <div class="box-body box-profile">

                            <div id="ersatzteile-select-wrap"></div>

                        </div>

                    </div>
                    <!-- 重复模块 -->

                    <!-- 重复模块 -->
                    <div id="shopping-cart-wrap" class="box box-<?=$css_class_typ;?>" style="padding: 10px !important; display: none;">
                        <div class="box-header">
                            <h3 class="box-title">5. Schritt: Gründe zur Gutschrift</h3>
                        </div>
                        <div class="box-body box-profile">

                            <div id="shopping-cart-div"></div>

                        </div>

                    </div>
                    <!-- 重复模块 -->

                    <!-- 重复模块 -->
                    <div id="order-comment-wrap" class="box box-<?=$css_class_typ;?>" style="padding: 10px; display: none;">
                        <div class="box-header">
                            <h3 class="box-title"><span id="step-comment-id">6</span>. Schritt: Notiz</h3>
                        </div>
                        <div class="box-body box-profile">

                            <div class="form-group">
                                <label for="order-comment">Memo für Bestellung ( maximale Länge: 800 Zeichen )</label>
                                <textarea id="order-comment" class="form-control" rows="3" style="max-width: 100%; min-width: 100%;" placeholder="Memo wird im Afterbuy gespeichert werden." onkeyup="calculateNotizTxtLen('order-comment');"></textarea>
                                <span id="notiz-txt-len-1"></span>
                            </div>

                        </div>

                    </div>
                    <!-- 重复模块 -->

                </div>

                <div id="create-ersatzteil-btn" class="padding-10-10-20-10 center" style="display: none;">
                    <button type="button" class="btn btn-primary" style="width: 300px; font-size: 18px; font-weight: bold;" onclick="createOrder('<?=$pageDW?>');"><?=$pageDW_title?>&nbsp;erstellen</button>
                </div>


            </div>





            <div class="height-800">&nbsp;</div>



        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

<script>
    scrollPageTo(0);
</script>

<?php get_footer();