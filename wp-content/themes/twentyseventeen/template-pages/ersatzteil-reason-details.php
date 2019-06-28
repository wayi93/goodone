<?php
/* Template Name: Idealhit Ersatzteil Reason Details Page */
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

global $current_user;
get_current_user();

$year = 2019;
$month = 6;
if(isset($_GET['year']) && isset($_GET['month'])){
    $year = intval($_GET['year']);
    $month = intval($_GET['month']);

    echo '<div id="show-year-div" style="display: none;">' . $year . '</div>';
    echo '<div id="show-month-div" style="display: none;">' . $month . '</div>';
}else{
    //重定向浏览器
    //header("Location: /404/");
    echo '<script>window.location.href="/404/";</script>';
    //确保重定向后，后续代码不会被执行
    exit;
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Gründe vom Ersatzteile
            <small>Herzlich willkommen bei GoodOne Rechnungsplattform</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-dashboard"></i>&nbsp;Home</a></li>
            <li>Daten</li>
            <li>Datenanalyse</li>
            <li class="active">Gründe vom Ersatzteile</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

        <!--------------------------
          | Your Page Content Here |
          -------------------------->

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary padding-10">
                    <div class="box-header">
                        <h3 class="box-title">Gründe&nbsp;vom&nbsp;Ersatzteile&nbsp;vom&nbsp;<?php echo $helper->getMonthTitle($month, false); ?>&nbsp;<?=$year?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="btn btn-primary no-print" onclick="window.print();"><i class="fa fa-fw fa-print"></i>&nbsp;ausdrucken</span></h3>
                    </div>
                    <div id="result-wrap" class="box-body box-profile">
                        <img src="/wp-content/uploads/images/loading-spinning-circles.svg" />
                    </div>
                </div>

            </div>
            <div class="col-md-9"></div>
        </div>

        <div style="height: 300px;">&nbsp;</div>


    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

    <script>ersatzteileReasonsManager.exportCSV('ersatzteil-reason-details-1');</script>

<?php get_footer();