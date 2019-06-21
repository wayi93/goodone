<?php
/* Template Name: Idealhit Lagerbestand Real Page */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/3/19
 * Time: 17:07
 */
get_header();

global $current_user;
get_current_user();
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Lagerbestand Real
            <small>Herzlich willkommen bei GoodOne Rechnungsplattform</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-dashboard"></i>&nbsp;Home</a></li>
            <li>Lager</li>
            <li class="active">Lagerbestand Real</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

        <!--------------------------
          | Your Page Content Here |
          -------------------------->

        <div class="row">

            <div id="cPanel" class="col-md-8">

                <!-- 重复模块 -->
                <div class="box box-primary" style="padding: 10px;">
                    <div class="box-header">
                        <h3 class="box-title">Lagerbestand&nbsp;Real&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;Quantity&nbsp;+&nbsp;Notsent&nbsp;)</h3>
                    </div>
                    <div id="lagerbestand-real-filter-wrap" class="box-body box-profile"></div>
                    <div class="box-body box-profile">

                        <div id="data-wrap"><img src="/wp-content/uploads/images/loading-spinning-circles.svg" /></div>

                    </div>

                </div>
                <!-- 重复模块 -->

            </div>



        </div>

        <div style="height: 300px;">&nbsp;</div>


    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
    showRealLagerbestandFullTable(false, {});
</script>


<?php get_footer();