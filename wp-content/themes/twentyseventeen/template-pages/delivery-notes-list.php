<?php
/* Template Name: Idealhit Lieferscheine Liste Page */
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
            Lieferscheine Liste
            <small>Herzlich willkommen bei GoodOne Rechnungsplattform</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-dashboard"></i>&nbsp;Home</a></li>
            <li>Dokumente</li>
            <li class="active">Lieferscheine Liste</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

        <!--------------------------
          | Your Page Content Here |
          -------------------------->

        <div class="row">

            <div class="col-md-12">

                <!-- 重复模块 -->
                <div class="box box-primary" style="padding: 10px;">
                    <div class="box-header">
                        <h3 class="box-title">Lieferscheine von vom letzten 7 Tage</h3>
                    </div>
                    <div class="box-body box-profile" style="padding-bottom: 30px;">

                        <div id="delivery-notes-list-wrap">
                            <img src="/wp-content/uploads/images/loading-spinning-circles.svg" />
                        </div>

                    </div>
                    <div class="box-footer">
                        <span style="color: #FF0000;">Hinweis: Für frühere Lieferscheine wenden Sie sich bitte an GAO.</span>
                    </div>

                </div>
                <!-- 重复模块 -->

            </div>

            <div class="col-md-8"></div>

        </div>

        <div style="height: 300px;">&nbsp;</div>


    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>drawLieferscheineListeTable();</script>


<?php get_footer();