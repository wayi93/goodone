<?php
/* Template Name: Idealhit Gutschrift Gründe Bearbeiten Page */
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

    <style>
        table tr:nth-child(odd){ background: #F7F7F7; }
        table td:nth-child(even){ }
        table tr:hover{ background-color: #F0F0F0; }
    </style>


    <!-- 隐藏数据 -->
    <div style="display: none;">
        <span id="reason-type">gutschrift</span>
    </div>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Gutschrift Gründe Bearbeiten
            <small>Herzlich willkommen bei GoodOne Rechnungsplattform</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-dashboard"></i>&nbsp;Home</a></li>
            <li>Gutschrift</li>
            <li class="active">Gründe Bearbeiten</li>
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
                        <h3 class="box-title">Die vorhandene Gründe verbessern</h3>
                    </div>
                    <div class="box-body box-profile">

                        <!-- http://www.ying.com/api/updateersatzteilreason/?act=0&id=13&reason=sss -->
                        <div id="ersatzteil-reasons-wrap"></div>

                    </div>

                </div>
                <!-- 重复模块 -->

            </div>


            <div id="cPanel" class="col-md-8">

                <!-- 重复模块 -->
                <div class="box box-primary" style="padding: 10px;">
                    <div class="box-header">
                        <h3 class="box-title">Einen neuen Grund erstellen</h3>
                    </div>
                    <div class="box-body box-profile">

                        <div class="form-group">
                            <label for="ersatzteil-reason-act1">Der neue Grund ( maximale Länge: 800 Zeichen )</label>
                            <textarea id="ersatzteil-reason-act1" class="form-control" rows="3" style="max-width: 100%; min-width: 100%;" placeholder="" onkeyup="calculateNotizTxtLen('ersatzteil-reason-act1');"></textarea>
                            <span id="reason-len-tip"></span>
                        </div>

                        <div class="padding-b-10">
                            <button id="btn-save" type="button" class="btn btn-primary" onclick="doErsatzteil(1,0,'ersatzteil-reason-act1','gutschrift');">Speichern</button>
                        </div>

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
    doErsatzteil(0,0,'NULL','gutschrift');
</script>

<?php get_footer();