<?php
/* Template Name: Idealhit Datenexport Page */
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
            Datenexport
            <small>Herzlich willkommen bei GoodOne Rechnungsplattform</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-dashboard"></i>&nbsp;Home</a></li>
            <li>Daten</li>
            <li class="active">Datenexport</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

        <!--------------------------
          | Your Page Content Here |
          -------------------------->

        <div class="row">

            <div id="cPanel" class="col-md-4">

                <!-- 重复模块 -->
                <div class="box box-primary" style="padding: 10px;">
                    <div class="box-header">
                        <h3 class="box-title">1. Online Real Fulfillment der allen Produkte</h3>
                    </div>
                    <div class="box-body box-profile">

                        <div class="fLeft" style="height: 30px; line-height: 30px;">Zeitraum:&nbsp;&nbsp;</div>
                        <div class="fLeft">
                            <select id="lagerbestand-historie-zeitraum" title="Zeitraum" class="padding-l-10 padding-r-10" style="height: 30px;">
                                <option value="30">1 Monat</option>
                                <option value="182">6 Monate</option>
                                <option value="365">1 Jahr</option>
                                <option value="550">1,5 Jahre</option>
                            </select>
                        </div>
                        <div class="fLeft" style="width: 30px;">&nbsp;</div>
                        <div class="fLeft" id="apfr_csv_export_btn" onclick="exportAllProductsFulfillmentRate();">
                            <button type="button" class="btn btn-primary">Exportieren</button>
                        </div>
                        <div class="clear" style="height: 15px;"></div>

                    </div>
                    <div id="AllProductsFulfillmentRateCSVLink" style="display: none;" class="box-footer"></div>

                </div>
                <!-- 重复模块 -->

            </div>

            <div id="dtlsPanel" class="col-md-8" style="display: none;">
                <div class="box box-primary" style="padding: 10px;">
                    <div class="box-header">
                        <h3 class="box-title" id="dtlsPanel-title">Details</h3>
                    </div>
                    <div id="dtls-wrap"> class="box-body box-profile"></div>
                    <div id="AllProductsFulfillmentRateCSVLink" style="display: none;" class="box-footer"></div>

                </div>
            </div>

        </div>

        <div class="row">

            <div id="cPanel" class="col-md-4">

                <!-- 重复模块 -->
                <div class="box box-primary" style="padding: 10px;">

                    <div class="box-header">
                        <h3 class="box-title">2. Die Gründe von Ersatzteile in CSV</h3>
                    </div>
                    <div class="box-body box-profile">

                        <div class="fLeft" onclick="ersatzteileReasonsManager.exportCSV('data-export-2');">
                            <button type="button" class="btn btn-primary">Exportieren</button>
                        </div>
                        <div class="clear" style="height: 15px;"></div>

                    </div>
                    <div id="ersatzteileReasonsCSVLink" style="display: none;" class="box-footer"></div>

                </div>
                <!-- 重复模块 -->

            </div>



        </div>




        <div class="row">

            <div id="cPanel" class="col-md-4">

                <!-- 重复模块 -->
                <div class="box box-primary" style="padding: 10px;">

                    <div class="box-header">
                        <h3 class="box-title">3. Die Gründe von Gutschriften in CSV</h3>
                    </div>
                    <div class="box-body box-profile">

                        <div class="fLeft" onclick="ersatzteileReasonsManager.exportCSV('data-export-5');">
                            <button type="button" class="btn btn-primary">Exportieren</button>
                        </div>
                        <div class="clear" style="height: 15px;"></div>

                    </div>
                    <div id="gutschriftenReasonsCSVLink" style="display: none;" class="box-footer"></div>

                </div>
                <!-- 重复模块 -->

            </div>



        </div>




        <!-- /api/export-csv-salesvolume/ -->
        <div class="row">

            <div id="cPanel" class="col-md-4">

                <!-- 重复模块 -->
                <div class="box box-primary" style="padding: 10px;">

                    <div class="box-header">
                        <h3 class="box-title">4. Verkaufsvolumen in CSV ( Alle 7 Tage )</h3>
                    </div>
                    <div class="box-body box-profile">

                        <div class="fLeft" style="height: 30px; line-height: 30px;">Zeitraum:&nbsp;&nbsp;</div>
                        <div class="fLeft">
                            <select id="zeitraum3" title="Zeitraum" class="padding-l-10 padding-r-10" style="height: 30px;">
                                <option value="30">1 Monat</option>
                                <option value="182">6 Monate</option>
                                <option value="365">1 Jahr</option>
                                <option value="550">1,5 Jahre</option>
                                <option value="730">2 Jahre</option>
                            </select>
                        </div>
                        <div class="fLeft" style="width: 30px;">&nbsp;</div>
                        <div class="fLeft" id="csv_export_btn_3" onclick="ersatzteileReasonsManager.exportCSV('data-export-3');">
                            <button type="button" class="btn btn-primary">Exportieren</button>
                        </div>
                        <div class="clear" style="height: 15px;"></div>

                    </div>
                    <div id="CSVLink3" style="display: none;" class="box-footer"></div>

                </div>
                <!-- 重复模块 -->

            </div>



        </div>


        <!-- /api/export-csv-stockquantity/ -->
        <div class="row">

            <div id="cPanel" class="col-md-4">

                <!-- 重复模块 -->
                <div class="box box-primary" style="padding: 10px;">

                    <div class="box-header">
                        <h3 class="box-title">5. Lagerbestand in CSV ( Jeden Tag )</h3>
                    </div>
                    <div class="box-body box-profile">

                        <div class="fLeft" style="height: 30px; line-height: 30px;">Zeitraum:&nbsp;&nbsp;</div>
                        <div class="fLeft">
                            <select id="zeitraum4" title="Zeitraum" class="padding-l-10 padding-r-10" style="height: 30px;">
                                <option value="30">1 Monat</option>
                                <option value="182">6 Monate</option>
                                <option value="365">1 Jahr</option>
                                <option value="550">1,5 Jahre</option>
                                <option value="730">2 Jahre</option>
                            </select>
                        </div>
                        <div class="fLeft" style="width: 30px;">&nbsp;</div>
                        <div class="fLeft" id="csv_export_btn_4" onclick="ersatzteileReasonsManager.exportCSV('data-export-4');">
                            <button type="button" class="btn btn-primary">Exportieren</button>
                        </div>
                        <div class="clear" style="height: 15px;"></div>

                    </div>
                    <div id="CSVLink4" style="display: none;" class="box-footer"></div>

                </div>
                <!-- 重复模块 -->

            </div>



        </div>

        <div style="height: 300px;">&nbsp;</div>


    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->


<?php get_footer();