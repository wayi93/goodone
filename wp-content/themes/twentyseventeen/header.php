<?php
session_start();
include_once ( GET_STYLESHEET_DIRECTORY() . '/Util/Helper.php');
use SoGood\Support\Util\Helper;
$helper = new Helper();


$static_files_version = '19.07.17.007';


$location_adminLTE = "/wp-includes/lib/AdminLTE/";
$location_lazyload = "/wp-includes/lib/jquery_lazyload-1.7.2/";
$location_fly = "/wp-includes/lib/jquery_fly/";
$location_layer = "/wp-includes/lib/layer/";
$location_inc_css = "/wp-includes/css/";
$location_inc_js = "/wp-includes/js/";
$location_tag_editor = "/wp-includes/lib/jquery_tag_editor/";

/**
 * 判断，如果是首页，那么直接跳转到 Dashboard 页面
 */
if(is_front_page()){
    //重定向浏览器
    header("Location: /dashboard/");
    //确保重定向后，后续代码不会被执行
    exit;
}

/**
 * 判断，如果没有登陆，那么直接跳转到 Login 页面
 */
if(!is_user_logged_in()){
    //重定向浏览器
    header("Location: /do-login.php");
    //确保重定向后，后续代码不会被执行
    exit;
}

/**
 * 获得当前页面地址url
 */
$current_url = home_url(add_query_arg(array()));
//echo $helper->checkContainStr($current_url, 'overview');

/**
 * 得到当前用户信息, 并且根据不同的用户组跳转不同的页面
 */
global $current_user;
get_current_user();
$userGroup = $current_user->roles[0];
if(!($helper->canThisUserGroupAccess($userGroup, $current_url))){
    //重定向浏览器
    header("Location: /dashboard/");
    //确保重定向后，后续代码不会被执行
    exit;
}


?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <?php wp_head(); ?>

    <!-- jQuery LazyLoad 1.7.2 -->
    <script src="<?=$location_lazyload;?>jquery.lazyload.min.js"></script>
    <!-- ChartJS -->
    <script src="<?=$location_adminLTE;?>bower_components/chart.js/Chart.js?version=<?=$static_files_version?>"></script>
    <!-- 抛物线动画 -->
    <script src="<?=$location_fly;?>jquery.fly.min.js"></script>
    <!-- Layer -->
    <script src="<?=$location_layer;?>layer.js"></script>
    <!-- Morris charts -->
    <link rel="stylesheet" href="<?=$location_adminLTE;?>bower_components/morris.js/morris.css">
    <script src="<?=$location_adminLTE;?>bower_components/raphael/raphael.min.js"></script>
    <script src="<?=$location_adminLTE;?>bower_components/morris.js/morris.min.js"></script>
    <!-- bootstrap datepicker -->
    <script src="<?=$location_adminLTE;?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js?version=<?=$static_files_version?>"></script>
    <link rel="stylesheet" href="<?=$location_adminLTE;?>bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" />
    <!-- bootstrap date-range-picker -->
    <script src="<?=$location_adminLTE;?>bower_components/moment/min/moment.min.js"></script>
    <script src="<?=$location_adminLTE;?>bower_components/bootstrap-daterangepicker/daterangepicker.js?version=<?=$static_files_version?>"></script>
    <link rel="stylesheet" href="<?=$location_adminLTE;?>bower_components/bootstrap-daterangepicker/daterangepicker.css" />

    <?php
    /**
     * 针对个别页面的 Import
     */
    if($helper->checkContainStr($current_url, '/ersatzteil-create/')){
        // TreeView
        // https://www.jqueryscript.net/other/treeview-checkbox.html
        echo '<link rel="stylesheet" href="' . $location_inc_css . 'jquery.treeview.css">';
        echo '<script src="' . $location_inc_js . 'jquery/jquery.treeview.js"></script>';
    }
    if($helper->checkContainStr($current_url, '/ersatzteil-create/') ||
        $helper->checkContainStr($current_url, '/lagerbestand-real/')){
        // Multiple Select
        // http://multiple-select.wenzhixin.net.cn/examples/#basic.html
        echo '<link rel="stylesheet" href="' . $location_inc_css . 'multiple-select.css">';
        echo '<script src="' . $location_inc_js . 'multiple-select.js"></script>';
    }
    ?>

    <script src="<?=$location_inc_js;?>main_PrivilegedUser.js?version=<?=$static_files_version?>"></script>

    <!--=======================-->
    <!--======= MAIN JS =======-->
    <!--=======================-->
    <script src="<?=$location_inc_js;?>main.js?version=<?=$static_files_version?>"></script>

    <!--======= MAIN CSS =======-->
    <link rel="stylesheet" href="<?=$location_inc_css;?>main.css?version=<?=$static_files_version?>">

    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="<?=$location_adminLTE;?>plugins/iCheck/all.css">
    <!-- iCheck 1.0.1 -->
    <script src="<?=$location_adminLTE;?>plugins/iCheck/icheck.min.js"></script>

    <!-- DataTables -->
    <script src="<?=$location_adminLTE;?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?=$location_adminLTE;?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <link rel="stylesheet" href="<?=$location_adminLTE;?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">

    <?php
    /**
     * 下面的这段代码，会影响  <!-- bootstrap datepicker -->
     */
    if(
            $helper->checkContainStr($current_url, '/datenmapping-bearbeiten/') ||
            $helper->checkContainStr($current_url, '/datenmapping-lager-bearbeiten/')
    ){
        echo '<!-- tag_editor -->';
        echo '<link rel="stylesheet" href="' . $location_tag_editor . 'jquery.tag-editor.css?var=19.04.29.01">';
        echo '<script src="' . $location_tag_editor . 'jquery-ui.min.js"></script>';
        echo '<script src="' . $location_tag_editor . 'jquery.caret.min.js"></script>';
        echo '<script src="' . $location_tag_editor . 'jquery.tag-editor.js"></script>';
    }
    ?>

    <link rel="stylesheet" href="<?=$location_adminLTE;?>bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?=$location_adminLTE;?>bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?=$location_adminLTE;?>bower_components/Ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?=$location_adminLTE;?>dist/css/AdminLTE.min.css">
    <!-- Layer -->
    <link rel="stylesheet" href="<?=$location_layer;?>theme/default/layer.css">
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
          page. However, you can choose any other skin. Make sure you
          apply the skin class to the body tag so the changes take effect. -->
    <link rel="stylesheet" href="<?=$location_adminLTE;?>dist/css/skins/skin-blue.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery Price Format -->
    <script src="<?=$location_inc_js;?>jquery/jquery.priceformat.min.js?version=<?=$static_files_version?>"></script>

    <!-- qrcode -->
    <script src="<?=$location_inc_js;?>qrcode.js"></script>

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">


</head>

<body class="hold-transition skin-blue fixed sidebar-mini">
<div class="wrapper">

    <!-- Main Header -->
    <header class="main-header">

        <!-- Logo -->
        <a href="/" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><img src="/wp-content/uploads/images/logo_23x27.png" /></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>GoodOne</b></span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <?php
            if(!$helper->checkContainStr($current_url, '/ersatzteil-create/')) {
                ?>
                <!-- Warenkorb Button -->
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- Control Sidebar Toggle Button -->
                        <li>
                            <a href="#" data-toggle="control-sidebar"
                               style="padding-left: 18px !important; padding-right: 18px !important; margin-right: 10px;"><b>Warenkorb&nbsp;&nbsp;</b><i
                                        id="icon-shopping-cart-rt" class="fa fa-shopping-cart"></i><span
                                        id="shopping-cart-pos-quantity"></span></a>
                        </li>
                    </ul>
                </div>
                <?php
            }
            ?>

        </nav>
    </header>

    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">

        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">

            <!-- Sidebar user panel (optional) -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="/wp-content/uploads/images/avatar/user-default.jpg" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p><?php echo $current_user->user_firstname . '&nbsp;' . $current_user->user_lastname; ?></p>
                    <!-- Status -->
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <ul class="sidebar-menu" data-widget="tree">
                <!-- Optionally, you can add icons to the links -->
                <?php if($helper->canThisUserGroupAccess($userGroup, "/dashboard/")){ ?>
                <li class="<?php if($helper->checkContainStr($current_url, '/dashboard/')){echo 'active';} ?>"><a href="/dashboard/"><i class="fa fa-dashboard"></i>&nbsp;<span><b>Dashboard</b></span></a></li>
                <?php } ?>

                <?php if($helper->canThisUserGroupAccess($userGroup, "/product-overview/") || $helper->canThisUserGroupAccess($userGroup, "/product-list/")){ ?>
                <li class="treeview <?php if($helper->checkContainStr($current_url, '/product-overview/') || $helper->checkContainStr($current_url, '/product-list/')){echo 'active';} ?>">
                    <a href="#"><i class="fa fa-th"></i>&nbsp;<span><b>Produkt</b></span>
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                      </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if($helper->canThisUserGroupAccess($userGroup, "/product-overview/")){ ?>
                        <li><a href="/product-overview/"><i class="fa fa-pie-chart"></i>&nbsp;Übersicht</a></li>
                        <?php } ?>
                        <?php if($helper->canThisUserGroupAccess($userGroup, "/product-list/")){ ?>
                        <li><a href="/product-list/"><i class="fa fa-table"></i>&nbsp;Produktliste</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>

                <?php if($helper->canThisUserGroupAccess($userGroup, "/order-overview/") || $helper->canThisUserGroupAccess($userGroup, "/order-create/") || $helper->canThisUserGroupAccess($userGroup, "/order-list/") || $helper->canThisUserGroupAccess($userGroup, "/order-list-onhold/") || $helper->canThisUserGroupAccess($userGroup, "/order-list-notpaid/")){ ?>
                <li class="treeview <?php if($helper->checkContainStr($current_url, '/order-overview/') || $helper->checkContainStr($current_url, '/order-create/') || $helper->checkContainStr($current_url, '/order-list/') || $helper->checkContainStr($current_url, '/order-list-onhold/') || $helper->checkContainStr($current_url, '/order-list-notpaid/') || $helper->checkContainStr($current_url, '/order/')){echo 'active';} ?>">
                    <a href="#"><i class="fa fa-file-text-o"></i>&nbsp;<span><b>Bestellung</b></span>
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                      </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if($helper->canThisUserGroupAccess($userGroup, "/order-overview/")){ ?>
                        <li><a href="/order-overview/"><i class="fa fa-pie-chart"></i>&nbsp;Übersicht</a></li>
                        <?php } ?>
                        <?php if($helper->canThisUserGroupAccess($userGroup, "/order-create/")){ ?>
                        <li><a href="/order-create/"><i class="fa fa-edit"></i>&nbsp;Erstellen</a></li>
                        <?php } ?>
                        <?php if($helper->canThisUserGroupAccess($userGroup, "/order-list/")){ ?>
                            <li><a href="/order-list/"><i class="fa fa-table"></i>&nbsp;Bestellungsliste</a></li>
                        <?php } ?>
                        <?php if($helper->canThisUserGroupAccess($userGroup, "/order-list-onhold/")){ ?>
                            <li><a href="/order-list-onhold/"><i class="fa fa-table"></i>&nbsp;Bestellungen (Warte)</a></li>
                        <?php } ?>
                        <?php if($helper->canThisUserGroupAccess($userGroup, "/order-list-notpaid/")){ ?>
                            <li><a href="/order-list-notpaid/"><i class="fa fa-table"></i>&nbsp;Bestellungen (Unbezahlt)</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>

                <?php if($helper->canThisUserGroupAccess($userGroup, "/quote-create/") || $helper->canThisUserGroupAccess($userGroup, "/quote-list/")){ ?>
                    <li class="treeview <?php if($helper->checkContainStr($current_url, '/quote-create/') || $helper->checkContainStr($current_url, '/quote-list/')){echo 'active';} ?>">
                        <a href="#"><i class="fa fa-file-text-o"></i>&nbsp;<span><b>Angebot</b></span>
                            <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                          </span>
                        </a>
                        <ul class="treeview-menu">
                            <?php if($helper->canThisUserGroupAccess($userGroup, "/quote-create/")){ ?>
                                <li><a href="/quote-create/"><i class="fa fa-edit"></i>&nbsp;Erstellen</a></li>
                            <?php } ?>
                            <?php if($helper->canThisUserGroupAccess($userGroup, "/quote-list/")){ ?>
                                <li><a href="/quote-list/"><i class="fa fa-table"></i>&nbsp;Angebotsliste</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php
                if($helper->canThisUserGroupAccess($userGroup, "/datenmapping-bearbeiten/") ||
                    $helper->canThisUserGroupAccess($userGroup, "/datenmapping-list-pro-elm/") ||
                    $helper->canThisUserGroupAccess($userGroup, "/datenmapping-list-elm-est/") ||
                    $helper->canThisUserGroupAccess($userGroup, "/ersatzteil-create/") ||
                    $helper->canThisUserGroupAccess($userGroup, "/ersatzteil-order-list/") ||
                    $helper->canThisUserGroupAccess($userGroup, "/ersatzteil-order-onhold/") ||
                    $helper->canThisUserGroupAccess($userGroup, "/ersatzteil-grund-edit/")
                ){ ?>
                    <li class="treeview <?php
                    if($helper->checkContainStr($current_url, "/datenmapping-bearbeiten/") ||
                        $helper->checkContainStr($current_url, "/datenmapping-list-pro-elm/") ||
                        $helper->checkContainStr($current_url, "/datenmapping-list-elm-est/") ||
                        $helper->checkContainStr($current_url, "/ersatzteil-create/") ||
                        $helper->checkContainStr($current_url, "/ersatzteil-order-list/") ||
                        $helper->checkContainStr($current_url, "/ersatzteil-order-onhold/") ||
                        $helper->checkContainStr($current_url, "/ersatzteil-grund-edit/")
                    ){echo 'active';} ?>">
                        <a href="#"><i class="fa fa-cubes"></i>&nbsp;<span><b>Ersatzteil</b></span>
                            <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                          </span>
                        </a>
                        <ul class="treeview-menu">
                            <?php if($helper->canThisUserGroupAccess($userGroup, "/datenmapping-bearbeiten/")){ ?>
                                <li><a href="/datenmapping-bearbeiten/"><i class="fa fa-exchange"></i>&nbsp;Mapping Erstellen</a></li>
                            <?php } ?>
                            <?php if($helper->canThisUserGroupAccess($userGroup, "/ersatzteil-grund-edit/")){ ?>
                                <li><a href="/ersatzteil-grund-edit/"><i class="fa fa-calendar-times-o"></i>&nbsp;Gründe Bearbeiten</a></li>
                            <?php } ?>
                            <?php if($helper->canThisUserGroupAccess($userGroup, "/datenmapping-list-pro-elm/")){ ?>
                                <li><a href="/datenmapping-list-pro-elm/"><i class="fa fa-exchange"></i>&nbsp;Mapping Liste ( Pro <=> Elm )</a></li>
                            <?php } ?>
                            <?php if($helper->canThisUserGroupAccess($userGroup, "/datenmapping-list-elm-est/")){ ?>
                                <li><a href="/datenmapping-list-elm-est/"><i class="fa fa-exchange"></i>&nbsp;Mapping Liste ( Elm <=> Est )</a></li>
                            <?php } ?>
                            <?php if($helper->canThisUserGroupAccess($userGroup, "/ersatzteil-create/")){ ?>
                                <li><a href="/ersatzteil-create/"><i class="fa fa-edit"></i>&nbsp;Ersatzteil bestellen</a></li>
                            <?php } ?>
                            <?php if($helper->canThisUserGroupAccess($userGroup, "/ersatzteil-order-list/")){ ?>
                                <li><a href="/ersatzteil-order-list/"><i class="fa fa-table"></i>&nbsp;Ersatzteil Bestellungen</a></li>
                            <?php } ?>
                            <?php if($helper->canThisUserGroupAccess($userGroup, "/ersatzteil-order-onhold/")){ ?>
                                <li><a href="/ersatzteil-order-onhold/"><i class="fa fa-table"></i>&nbsp;Ersatzteil Bestellungen (Warte)</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php if(
                    $helper->canThisUserGroupAccess($userGroup, "/delivery-notes-list/") ||
                    $helper->canThisUserGroupAccess($userGroup, "/datenmapping-lager-bearbeiten/") ||
                    $helper->canThisUserGroupAccess($userGroup, "/datenmapping-list-regal-elm/") ||
                    $helper->canThisUserGroupAccess($userGroup, "/lagerbestand-real/")
                ){ ?>
                    <li class="treeview <?php if(
                        $helper->checkContainStr($current_url, '/delivery-notes-list/') ||
                        $helper->checkContainStr($current_url, '/datenmapping-lager-bearbeiten/') ||
                        $helper->checkContainStr($current_url, '/datenmapping-list-regal-elm/') ||
                        $helper->checkContainStr($current_url, '/lagerbestand-real/')
                    ){echo 'active';} ?>">
                        <a href="#"><i class="fa fa-building-o"></i>&nbsp;<span><b>Lager</b></span>
                            <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                          </span>
                        </a>
                        <ul class="treeview-menu">
                            <?php if($helper->canThisUserGroupAccess($userGroup, "/delivery-notes-list/")){ ?>
                                <li><a href="/delivery-notes-list/"><i class="fa fa-file-pdf-o"></i>&nbsp;Lieferscheine Liste</a></li>
                            <?php } ?>
                            <?php if($helper->canThisUserGroupAccess($userGroup, "/lagerbestand-real/")){ ?>
                                <li><a href="/lagerbestand-real/"><i class="fa fa-file-text-o"></i>&nbsp;Lagerbestand Real</a></li>
                            <?php } ?>
                            <?php if($helper->canThisUserGroupAccess($userGroup, "/datenmapping-lager-bearbeiten/")){ ?>
                                <li><a href="/datenmapping-lager-bearbeiten/"><i class="fa fa-exchange"></i>&nbsp;Datenmapping Bearbeiten</a></li>
                            <?php } ?>
                            <?php if($helper->canThisUserGroupAccess($userGroup, "/datenmapping-list-regal-elm/")){ ?>
                                <li><a href="/datenmapping-list-regal-elm/"><i class="fa fa-exchange"></i>&nbsp;Mapping Liste ( Regal <=> Elm )</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php
                if($helper->canThisUserGroupAccess($userGroup, "/data-analytics/") ||
                    $helper->canThisUserGroupAccess($userGroup, "/data-export/") ||
                    $helper->canThisUserGroupAccess($userGroup, "/data-umsatzstatistik/")
                ){ ?>
                    <li class="treeview <?php
                    if($helper->checkContainStr($current_url, '/data-analytics/') ||
                        $helper->checkContainStr($current_url, '/data-export/') ||
                        $helper->checkContainStr($current_url, '/data-umsatzstatistik/')
                    ){echo 'active';} ?>">
                        <a href="#"><i class="fa fa-database"></i>&nbsp;<span><b>Datenanalyse</b></span>
                            <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                          </span>
                        </a>
                        <ul class="treeview-menu">
                            <?php if($helper->canThisUserGroupAccess($userGroup, "/data-umsatzstatistik/")){ ?>
                                <li><a href="/data-umsatzstatistik/"><i class="fa fa-bar-chart"></i>&nbsp;Umsatzstatistik</a></li>
                            <?php } ?>
                            <?php if($helper->canThisUserGroupAccess($userGroup, "/data-analytics/")){ ?>
                                <li><a href="/data-analytics/"><i class="fa fa-gears"></i>&nbsp;Datenanalyse</a></li>
                            <?php } ?>
                            <?php if($helper->canThisUserGroupAccess($userGroup, "/data-export/")){ ?>
                                <li><a href="/data-export/"><i class="fa fa-download"></i>&nbsp;Datenexport</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php if($helper->canThisUserGroupAccess($userGroup, "/my-account/")){ ?>
                <li class="<?php if($helper->checkContainStr($current_url, '/my-account/')){echo 'active';} ?>"><a href="/my-account/"><i class="fa fa-user-circle"></i>&nbsp;<span><b>Mein Konto</b></span></a></li>
                <?php } ?>


                <li>
                    <div class="control-panel">
                        <a href="/do-login.php?action=logout" class="btn btn-default btn-flat" style="color: #222d32;">&nbsp;<i class="fa fa-sign-out"></i>&nbsp;&nbsp;<span>Abmelden</span></a>
                    </div>
                </li>


            </ul>
            <!-- /.sidebar-menu -->


            <!-- Version -->
            <div class="verion-sidebar"><a href="/release" target="_blank">Version 1.7.0<br><i class="fa fa-fw fa-link"></i>Release Report</a></div>


        </section>
        <!-- /.sidebar -->
    </aside>
