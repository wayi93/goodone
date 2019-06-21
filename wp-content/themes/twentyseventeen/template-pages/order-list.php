<?php
/* Template Name: Idealhit Order List Page */
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
 * 判断请求订单的类型
 */
$current_url = home_url(add_query_arg(array()));
$api_param_type = 0;
$page_title = 'Bestellungsliste';
if($helper->checkContainStr($current_url, '/order-list-onhold')){
    $api_param_type = 1;
    $page_title = 'Bestellungsliste (Warte)';
}else if($helper->checkContainStr($current_url, '/order-list-notpaid')){
    $api_param_type = 3;
    $page_title = 'Bestellungsliste (Unbezahlt)';
}else if($helper->checkContainStr($current_url, '/order-list')){
    $api_param_type = 0;
    $page_title = 'Bestellungsliste';
}else if($helper->checkContainStr($current_url, '/quote-list')){
    $api_param_type = 2;
    $page_title = 'Angebotsliste';
}else if($helper->checkContainStr($current_url, '/ersatzteil-order-list')){
    $api_param_type = 4;
    $page_title = 'Ersatzteil Bestellungen Liste';
}else if($helper->checkContainStr($current_url, '/ersatzteil-order-onhold')){
    $api_param_type = 4;
    $page_title = 'Ersatzteil Bestellungen Liste (Warte)';
}

$pageDW = $helper->getPageDealWith();
$pageDW_title = "";
$pageDW_title_plural = "";
switch ($pageDW){
    case 'order':
        $pageDW_title = "Bestellung";
        $pageDW_title_plural = "Bestellungen";
        break;
    case 'quote':
        $pageDW_title = "Angebot";
        $pageDW_title_plural = "Angebote";
        break;
    case 'ersatzteil':
        $pageDW_title = "Ersatzteil Bestellung";
        $pageDW_title_plural = "Ersatzteil Bestellungen";
        break;
    default:
        $pageDW_title = "";
        $pageDW_title_plural = "";
}

get_header();
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?=$page_title?>
            <small>Herzlich willkommen bei GoodOne Rechnungsplattform</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-dashboard"></i>&nbsp;Home</a></li>
            <li><?=$pageDW_title?></li>
            <li class="active"><?=$page_title?></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

        <!--------------------------
          | Your Page Content Here |
          -------------------------->

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><?=$pageDW_title_plural;?></h3>
                    </div>
                    <div class="box-body box-profile" style="padding-bottom: 40px;">

                        <div id="orders-wrap"><img src="/wp-content/uploads/images/loading-spinning-circles.svg" /></div>

                    </div>
                </div>

            </div>
        </div>

        <script>queryOrderMainInfos(<?=$api_param_type;?>, '<?=$pageDW;?>');</script>


    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php get_footer();