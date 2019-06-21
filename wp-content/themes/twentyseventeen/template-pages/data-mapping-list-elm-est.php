<?php
/* Template Name: Idealhit Datenmapping Liste Elm-Est Page */
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
            Elements <=> Ersatzteile Mapping-Liste
            <small>Herzlich willkommen bei GoodOne Rechnungsplattform</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-dashboard"></i>&nbsp;Home</a></li>
            <li>Ersatzteil</li>
            <li class="active">Mapping Liste ( Elm <=> Est )</li>
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
                <div class="box box-primary" style="padding: 10px;">
                    <div class="box-header">
                        <h3 class="box-title" style="font-weight: bold;">n&nbsp;x&nbsp;Elements&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-exchange"></i>&nbsp;&nbsp;&nbsp;&nbsp;n&nbsp;x&nbsp;Ersatzteile</h3>
                    </div>

                    <!-- 搜索界面 Start -->
                    <div class="row" style="padding: 10px 20px; display: none;">
                        <div id="cPanel" class="col-md-6">
                            <div class="box box-primary" style="padding: 10px;">
                                <div class="box-header">
                                    <h3 class="box-title">Nach Elementsname oder -ean suchen</h3>
                                </div>
                                <div class="box-body">
                                    000
                                </div>
                            </div>
                        </div>
                        <div id="cPanel" class="col-md-6">
                            <div class="box box-primary" style="padding: 10px;">
                                <div class="box-header">
                                    <h3 class="box-title">Nach Ersatzteilsname oder -ean suchen</h3>
                                </div>
                                <div class="box-body">
                                    000
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 搜索界面 End -->

                    <div id="all-mappings-list-wrap" class="box-body box-profile">
                        <div id="all-mappings-list-wrap-canvas"></div>
                        <!-- <canvas id="all-mappings-list-wrap-canvas">Ihr Browser unterstützt das canvas-Bereich nicht.</canvas> -->
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


    <!-- 填充数据 -->
    <script>
        showLoadingLayer();
        loadMappingEANs(3, '0', 'elm-est');
    </script>

<?php get_footer();