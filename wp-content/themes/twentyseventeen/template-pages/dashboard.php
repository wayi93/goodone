<?php
/* Template Name: Idealhit Dashboard Page */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/3/19
 * Time: 17:07
 */
get_header();

include_once ( GET_STYLESHEET_DIRECTORY() . '/Util/Helper.php');
use SoGood\Support\Util\Helper;
$helper = new Helper();

/**
 * 得到当前用户信息, 并且根据不同的用户组跳转不同的页面
 */
global $current_user;
get_current_user();
$userGroup = $current_user->roles[0];
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard
            <small>Herzlich willkommen bei GoodOne Rechnungsplattform</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-dashboard"></i>&nbsp;Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

        <!--------------------------
          | Your Page Content Here |
          -------------------------->

        <div class="row">

            <!-- ============================================================================================= -->

            <?php if($userGroup != "accounting" && $userGroup != "apigroup" && $userGroup != "spedition"){ ?>
                <div class="col-md-3">

                    <div class="box padding-10-10-20-10">

                        <div class="box-header">
                            <h3 class="box-title">Cache Management</h3>
                        </div>

                        <div class="box-body">

                            <div class="height-10">&nbsp;</div>
                            <div>
                                <a class="btn btn-block btn-social btn-linkedin" onclick=updateProductDataBackend("dashboard-0");>
                                    <i class="fa fa-repeat"></i>Die Produktdaten aktualisieren
                                </a>
                            </div>

                            <div class="height-10">&nbsp;</div>
                            <div>
                                <a class="btn btn-block btn-social btn-linkedin" onclick=clearShoppingCart();>
                                    <i class="fa fa fa-shopping-cart"></i>Den Warenkorb leeren
                                </a>
                            </div>

                        </div>

                    </div>

                </div>
            <?php } ?>

            <!-- ============================================================================================= -->

            <?php if($userGroup != "accounting" && $userGroup != "apigroup" && $userGroup != "showroom"){ ?>
            <div class="col-md-3">

                <div class="box padding-10-10-20-10">

                    <div class="box-header">
                        <h3 class="box-title">Die Lagerarbeit</h3>
                    </div>

                    <div class="box-body">

                        <!-- 打印Lieferschein按钮 -->
                        <div class="height-10">&nbsp;</div>
                        <div>
                            <a href="/delivery-notes-list/" class="btn btn-block btn-social btn-linkedin">
                                <i class="fa fa-file-pdf-o"></i>Lieferscheine ausdrucken
                            </a>
                        </div>

                        <div class="height-10">&nbsp;</div>

                        <div>
                            <a href="/lagerbestand-real/" class="btn btn-block btn-social btn-linkedin">
                                <i class="fa fa-file-text-o"></i>Lagerbestand Real
                            </a>
                        </div>

                    </div>

                </div>

            </div>

            <?php } ?>

            <!-- ============================================================================================= -->

            <?php if($userGroup != "spedition"){ ?>
                <div class="col-md-3">

                    <div class="box padding-10-10-20-10">

                        <div class="box-header">
                            <h3 class="box-title">Die Statistiken</h3>
                        </div>

                        <div class="box-body">

                            <!-- 销售业绩 -->
                            <div class="height-10">&nbsp;</div>
                            <div>
                                <a href="/data-umsatzstatistik/" class="btn btn-block btn-social btn-linkedin">
                                    <i class="fa fa-bar-chart"></i>Umsatzstatistik
                                </a>
                            </div>

                            <?php if($helper->canThisUserGroupAccess($userGroup, "/data-analytics/")){ ?>
                                <div class="height-10">&nbsp;</div>
                                <!-- 按钮 -->
                                <div>
                                    <a href="/data-analytics/" class="btn btn-block btn-social btn-linkedin">
                                        <i class="fa fa-gears"></i>Datenanalyse
                                    </a>
                                </div>
                            <?php } ?>

                        </div>

                    </div>

                </div>

            <?php } ?>

            <!-- ============================================================================================= -->

        </div>

        <div class="row">

            <!-- ============================================================================================= -->

<?php if(
        $helper->canThisUserGroupAccess($userGroup, "/quote-create/") ||
        $helper->canThisUserGroupAccess($userGroup, "/order-create/") ||
        $helper->canThisUserGroupAccess($userGroup, "/ersatzteil-create/")
){ ?>
            <div class="col-md-3">

                <div class="box padding-10-10-20-10">

                    <div class="box-header">
                        <h3 class="box-title">Das Erstellen</h3>
                    </div>

                    <div class="box-body">

                        <?php if($helper->canThisUserGroupAccess($userGroup, "/quote-create/")){ ?>
                            <div class="height-10">&nbsp;</div>
                            <!-- 按钮 -->
                            <div>
                                <a href="/quote-create/" class="btn btn-block btn-social btn-linkedin">
                                    <i class="fa fa-pencil-square-o"></i>Angebot erstellen
                                </a>
                            </div>
                        <?php } ?>

                        <?php if($helper->canThisUserGroupAccess($userGroup, "/order-create/")){ ?>
                            <div class="height-10">&nbsp;</div>
                            <!-- 按钮 -->
                            <div>
                                <a href="/order-create/" class="btn btn-block btn-social btn-linkedin">
                                    <i class="fa fa-pencil-square-o"></i>Bestellung erstellen
                                </a>
                            </div>
                        <?php } ?>

                        <?php if($helper->canThisUserGroupAccess($userGroup, "/ersatzteil-create/")){ ?>
                            <div class="height-10">&nbsp;</div>
                            <!-- 按钮 -->
                            <div>
                                <a href="/ersatzteil-create/" class="btn btn-block btn-social btn-linkedin">
                                    <i class="fa fa-pencil-square-o"></i>Ersatzteil bestellen
                                </a>
                            </div>
                        <?php } ?>

                        <?php if($helper->canThisUserGroupAccess($userGroup, "/gutschrift-create/")){ ?>
                            <div class="height-10">&nbsp;</div>
                            <!-- 按钮 -->
                            <div>
                                <a href="/gutschrift-create/" class="btn btn-block btn-social btn-linkedin">
                                    <i class="fa fa-pencil-square-o"></i>Gutschrift bestellen
                                </a>
                            </div>
                        <?php } ?>

                    </div>

                </div>

            </div>
<?php } ?>

            <!-- ============================================================================================= -->

<?php if(
        $helper->canThisUserGroupAccess($userGroup, "/quote-list/") ||
        $helper->canThisUserGroupAccess($userGroup, "/order-list/") ||
        $helper->canThisUserGroupAccess($userGroup, "/order-list-notpaid/") ||
        $helper->canThisUserGroupAccess($userGroup, "/order-list-onhold/") ||
        $helper->canThisUserGroupAccess($userGroup, "/ersatzteil-order-list/") ||
        $helper->canThisUserGroupAccess($userGroup, "/ersatzteil-order-notconfirmed/") ||
        $helper->canThisUserGroupAccess($userGroup, "/ersatzteil-order-canceled/") ||
        $helper->canThisUserGroupAccess($userGroup, "/gutschrift-order-list/") ||
        $helper->canThisUserGroupAccess($userGroup, "/gutschrift-order-list-notpaid/") ||
        $helper->canThisUserGroupAccess($userGroup, "/gutschrift-order-list-notconfirmed/")
){ ?>
            <div class="col-md-3">

                <div class="box padding-10-10-20-10">

                    <div class="box-header">
                        <h3 class="box-title">Die Listen</h3>
                    </div>

                    <div class="box-body">

                        <?php if($helper->canThisUserGroupAccess($userGroup, "/quote-list/")){ ?>
                            <div class="height-10">&nbsp;</div>
                            <!-- 按钮 -->
                            <div>
                                <a href="/quote-list/" class="btn btn-block btn-social btn-linkedin">
                                    <i class="fa fa-file-text-o"></i>Angebote (Liste)
                                </a>
                            </div>
                        <?php } ?>

                        <?php if($helper->canThisUserGroupAccess($userGroup, "/order-list/")){ ?>
                            <div class="height-10">&nbsp;</div>
                            <!-- 按钮 -->
                            <div>
                                <a href="/order-list/" class="btn btn-block btn-social btn-linkedin">
                                    <i class="fa fa-file-text-o"></i>Bestellungen (Liste)
                                </a>
                            </div>
                        <?php } ?>

                        <?php if($helper->canThisUserGroupAccess($userGroup, "/order-list-notpaid/")){ ?>
                            <div class="height-10">&nbsp;</div>
                            <!-- 按钮 -->
                            <div>
                                <a href="/order-list-notpaid/" class="btn btn-block btn-social btn-linkedin">
                                    <i class="fa fa-file-text-o"></i>Unbezahlte Bestellungen (Liste)
                                </a>
                            </div>
                        <?php } ?>

                        <?php if($helper->canThisUserGroupAccess($userGroup, "/order-list-onhold/")){ ?>
                            <div class="height-10">&nbsp;</div>
                            <!-- 按钮 -->
                            <div>
                                <a href="/order-list-onhold/" class="btn btn-block btn-social btn-linkedin">
                                    <i class="fa fa-file-text-o"></i>Bestellungen (Warte)
                                </a>
                            </div>
                        <?php } ?>

                        <?php if($helper->canThisUserGroupAccess($userGroup, "/ersatzteil-order-list/")){ ?>
                            <div class="height-10">&nbsp;</div>
                            <!-- 按钮 -->
                            <div>
                                <a href="/ersatzteil-order-list/" class="btn btn-block btn-social btn-linkedin">
                                    <i class="fa fa-file-text-o"></i>Ersatzteile (Liste)
                                </a>
                            </div>
                        <?php } ?>

                        <!-- 2021-06-07 Dashboard Button -->
                        <?php if($helper->canThisUserGroupAccess($userGroup, "/ersatzteil-order-notconfirmed/")){ ?>
                            <div class="height-10">&nbsp;</div>
                            <!-- 按钮 -->
                            <div>
                                <a href="/ersatzteil-order-notconfirmed/" class="btn btn-block btn-social btn-linkedin">
                                    <i class="fa fa-file-text-o"></i>Ersatzteile (Liste zu bestätigen)
                                </a>
                            </div>
                        <?php } ?>

                        <?php if($helper->canThisUserGroupAccess($userGroup, "/ersatzteil-order-canceled/")){ ?>
                            <div class="height-10">&nbsp;</div>
                            <!-- 按钮 -->
                            <div>
                                <a href="/ersatzteil-order-canceled/" class="btn btn-block btn-social btn-linkedin">
                                    <i class="fa fa-file-text-o"></i>Ersatzteile (Liste storniert)
                                </a>
                            </div>
                        <?php } ?>

                        <?php if($helper->canThisUserGroupAccess($userGroup, "/gutschrift-order-list/")){ ?>
                            <div class="height-10">&nbsp;</div>
                            <!-- 按钮 -->
                            <div>
                                <a href="/gutschrift-order-list/" class="btn btn-block btn-social btn-linkedin">
                                    <i class="fa fa-file-text-o"></i>Gutschriften (Liste)
                                </a>
                            </div>
                        <?php } ?>

                        <!-- 2021-02-21 Dashboard Button -->
                        <?php if($helper->canThisUserGroupAccess($userGroup, "/gutschrift-order-list-notconfirmed/")){ ?>
                            <div class="height-10">&nbsp;</div>
                            <!-- 按钮 -->
                            <div>
                                <a href="/gutschrift-order-list-notconfirmed/" class="btn btn-block btn-social btn-linkedin">
                                    <i class="fa fa-file-text-o"></i>Gutschriften (Liste zu bestätigen)
                                </a>
                            </div>
                        <?php } ?>

                        <?php if($helper->canThisUserGroupAccess($userGroup, "/gutschrift-order-list-notpaid/")){ ?>
                            <div class="height-10">&nbsp;</div>
                            <!-- 按钮 -->
                            <div>
                                <a href="/gutschrift-order-list-notpaid/" class="btn btn-block btn-social btn-linkedin">
                                    <i class="fa fa-file-text-o"></i>Gutschriften (Liste zu zahlen)
                                </a>
                            </div>
                        <?php } ?>
                    </div>

                </div>

            </div>
<?php } ?>

            <!-- ============================================================================================= -->

        </div>


    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php

// 提示修改密码
global $current_user;
get_current_user();
$auth_res = wp_authenticate($current_user->user_login, "maimai");
if($auth_res->data->user_login == $current_user->user_login){
    echo '<script>layer.confirm("Wir empfehlen Ihnen Ihr generiertes Passwort aus Sicherheitsgründen zu ändern.", {
                    icon: 7,
                    title: "Konto Tipp: ID-10026",
                    btn: ["Ja, jetzt ändern", "Nein, später"] //按钮
                }, function(){
                    window.location.href="/my-account/";
                }, function(){
                    //console.log(ods);
                });</script>';
}

get_footer();