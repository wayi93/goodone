<?php
/* Template Name: Idealhit Datenanalyse Page */
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
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Datenanalyse
            <small>Herzlich willkommen bei GoodOne Rechnungsplattform</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-dashboard"></i>&nbsp;Home</a></li>
            <li>Daten</li>
            <li class="active">Datenanalyse</li>
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
                        <h3 class="box-title">Umsatz-Linien</h3>
                        <hr>
                        <div>
                            <div class="fLeft" style="height: 30px; line-height: 30px;">EAN:&nbsp;&nbsp;</div>
                            <div class="fLeft">
                                <input title="EAN" id="chart-1-ean" class="form-control" style="height: 30px; border-radius: 3px;" placeholder="42507553XXXXX" onkeyup="showChart1();" />
                            </div>
                            <div class="fLeft" style="width: 30px;">&nbsp;</div>
                            <div class="fLeft" style="height: 30px; line-height: 30px;">Zeitraum:&nbsp;&nbsp;</div>
                            <div class="fLeft">
                                <select id="chart-1-zeitraum" title="Zeitraum" class="padding-l-10 padding-r-10" style="height: 30px;">
                                    <option value="365">1 Jahr</option>
                                    <option value="550">1,5 Jahre</option>
                                </select>
                            </div>
                            <div class="fLeft" style="width: 40px;">&nbsp;</div>
                            <div class="fLeft" onclick="showChart1();">
                                <button id="btn-search-kunden" type="button" class="btn btn-primary">Diagramm erstellen</button>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <hr style="margin-bottom: 0 !important;">
                    </div>
                    <div class="box-body box-profile">
                        <div id="chart-1-product-name">
                        </div>
                        <div id="chart-1-wrap" class="box-body chart-responsive">
                            <img src="/wp-content/uploads/images/loading-spinning-circles.svg" />
                        </div>
                    </div>
                </div>

                <div class="box box-primary padding-10">
                    <div class="box-header">
                        <h3 class="box-title">Umsatz Normal Distribution</h3>
                        <hr>
                        <div>
                            <div class="fLeft" style="height: 30px; line-height: 30px;">EAN:&nbsp;&nbsp;</div>
                            <div class="fLeft">
                                <input title="EAN" id="chart-2-ean" class="form-control" style="height: 30px; border-radius: 3px;" placeholder="42507553XXXXX" onkeyup="showChart2();" />
                            </div>
                            <div class="fLeft" style="width: 30px;">&nbsp;</div>
                            <div class="fLeft" style="height: 30px; line-height: 30px;">Einheitszeit&nbsp;Länge:&nbsp;&nbsp;</div>
                            <div class="fLeft">
                                <select id="chart-2-einheitszeit-laenge" title="Einheitszeit Länge" class="padding-l-10 padding-r-10" style="height: 30px;">
                                    <option value="3">3 Tage</option>
                                    <option value="7">7 Tage</option>
                                </select>
                            </div>
                            <div class="fLeft" style="width: 30px;">&nbsp;</div>
                            <div class="fLeft" style="height: 30px; line-height: 30px;">Zeitraum:&nbsp;&nbsp;</div>
                            <div class="fLeft">
                                <select id="chart-2-zeitraum" title="Zeitraum" class="padding-l-10 padding-r-10" style="height: 30px;">
                                    <option value="365">1 Jahr</option>
                                    <option value="550">1,5 Jahre</option>
                                </select>
                            </div>
                            <div class="fLeft" style="width: 30px;">&nbsp;</div>
                            <div class="fLeft" style="height: 30px; line-height: 30px;">Fulfillment:&nbsp;&nbsp;</div>
                            <div class="fLeft">
                                <select id="chart-2-fulfillment" title="Fulfillment" class="padding-l-10 padding-r-10" style="height: 30px;">
                                    <option value="0.9">90%</option>
                                    <option value="0.95">95%</option>
                                    <option value="0.99">99%</option>
                                </select>
                            </div>
                            <div class="fLeft" style="width: 40px;">&nbsp;</div>
                            <div class="fLeft" onclick="showChart2();">
                                <button id="btn-search-kunden" type="button" class="btn btn-primary">Diagramm erstellen</button>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <hr style="margin-bottom: 0 !important;">
                    </div>
                    <div class="box-body box-profile">
                        <div id="chart-2-product-name">
                        </div>
                        <div id="chart-2-wrap" class="box-body chart-responsive">
                            <img src="/wp-content/uploads/images/loading-spinning-circles.svg" />
                        </div>
                    </div>
                </div>

                <div class="box box-primary padding-10" style="display: none;">
                    <div class="box-header">
                        <h3 class="box-title">Benutzerkonsumgewohnheiten</h3>
                        <hr>
                        <div>
                            <div class="form-group fLeft" style="height: 30px; width: 300px;">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="chart-3-date-von-bis" title="chart-3-date-von-bis" style="height: 30px;" />
                                </div>
                            </div>
                            <div class="fLeft" style="width: 30px;">&nbsp;</div>
                            <div class="fLeft" style="height: 30px; line-height: 30px;">Wochentage:&nbsp;&nbsp;</div>
                            <div class="fLeft">
                                <select id="chart-3-wochentage" title="Wochentage" class="padding-l-10 padding-r-10" style="height: 30px;">
                                    <option value="1">Montag</option>
                                    <option value="2">Dienstag</option>
                                    <option value="3">Mittwoch</option>
                                    <option value="4">Donnerstag</option>
                                    <option value="5">Freitag</option>
                                    <option value="6">Samstag</option>
                                    <option value="7">Sonntag</option>
                                </select>
                            </div>
                            <div class="fLeft" style="width: 30px;">&nbsp;</div>
                            <div class="fLeft" style="height: 30px; line-height: 30px;">Konto:&nbsp;&nbsp;</div>
                            <div class="fLeft">
                                <select id="chart-3-konto" title="Konto" class="padding-l-10 padding-r-10" style="height: 30px;">
                                    <option value="0">ALL</option>
                                    <option value="1">Sogood</option>
                                    <option value="2">Mai & Mai</option>
                                </select>
                            </div>
                            <div class="fLeft" style="width: 40px;">&nbsp;</div>
                            <div class="fLeft" onclick="showChart3();">
                                <button id="btn-search-kunden" type="button" class="btn btn-primary">Diagramm erstellen</button>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <hr style="margin-bottom: 0 !important;">
                    </div>
                    <div class="box-body box-profile">
                        <div id="chart-3-wrap" class="box-body chart-responsive">
                            <img src="/wp-content/uploads/images/loading-spinning-circles.svg" />
                        </div>
                    </div>
                </div>

                <!-- AREA CHART -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Lagerbestand Historie</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-header with-border">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="input-lagerbestand-historie-ean" onkeyup="getProductStockHistory();" placeholder="EAN: 42507553XXXXX" />
                        </div>
                        <div class="fLeft" style="width: 20px;">&nbsp;</div>
                        <div class="fLeft" style="height: 30px; line-height: 30px;">Zeitraum:&nbsp;&nbsp;</div>
                        <div class="fLeft">
                            <select id="lagerbestand-historie-zeitraum" title="Zeitraum" class="padding-l-10 padding-r-10" style="height: 30px;">
                                <option value="30">1 Monat</option>
                                <option value="182">6 Monate</option>
                                <option value="365">1 Jahr</option>
                                <option value="550" selected>1,5 Jahre</option>
                            </select>
                        </div>
                        <div class="fLeft" style="width: 40px;">&nbsp;</div>
                        <div class="fLeft" onclick="getProductStockHistory();">
                            <button type="button" class="btn btn-primary">Diagramm erstellen</button>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div id="lagerbestand-historie-product-name" class="col-md-9"></div>
                    <div id="lagerbestand-historie-chart-wrap" class="box-body chart-responsive">
                        <img src="/wp-content/uploads/images/loading-spinning-circles.svg" />
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

            </div>
            <div class="col-md-9"></div>
        </div>

        <div class="box box-primary padding-10">
            <div class="box-header">
                <h3 class="box-title">Gründe vom Ersatzteile</h3>
                <hr>
                <div>
                    <div class="fLeft" style="height: 30px; line-height: 30px;">Anfangszeit:&nbsp;&nbsp;</div>
                    <div class="fLeft" style="width: 10px;">&nbsp;</div>
                    <div class="fLeft">
                        <?php
                        $thisYear = intval(date( "Y"));
                        $thisMonth = intval(date( "m"));
                        ?>
                        <select id="ersatzteil-reason-start-year" title="" onchange="initErsatzteilReasontartSMonthSelect(<?=$thisYear?>,<?=$thisMonth?>);" onkeyup="" class="padding-l-10 padding-r-10" style="height: 30px;">
                            <?php
                            for($syi = 2019; $syi <= intval($thisYear); ++$syi){
                                $selectedTagY = '';
                                if($thisYear === $syi){
                                    $selectedTagY = 'selected';
                                }
                                echo '<option value="' . $syi . '" ' . $selectedTagY . '>' . $syi . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="fLeft" style="width: 5px;">&nbsp;</div>
                    <div class="fLeft" id="ersatzteil-reason-start-month-div"></div>
                    <div class="fLeft" style="width: 40px;">&nbsp;</div>
                    <div class="fLeft">
                        <button id="btn-load-ersatzteil-reason" type="button" class="btn btn-primary" onclick="ersatzteileReasonsManager.exportCSV('data-analytics-1')">Daten laden</button>
                    </div>
                    <div class="clear"></div>
                </div>
                <hr style="margin-bottom: 0 !important;">
            </div>
            <div class="box-body box-profile">
                <div id="chart-2-product-name">
                </div>
                <div id="ersatzteil-reason-wrap" class="box-body chart-responsive"></div>
            </div>
        </div>

        <div style="height: 300px;">&nbsp;</div>


    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

    <script>
        $('#chart-3-date-von-bis').daterangepicker({
            opens: 'right',
            startDate: moment().startOf('hour').add((0 - 24 * 365), 'hour'),
            endDate: moment().startOf('hour'),
            locale: {
                format: 'DD.MM.YYYY'
            }
        });
        $('#lagerbestand-historie-chart-wrap').html('');
        $('#chart-1-wrap').html('');
        $('#chart-2-wrap').html('');
        $('#chart-3-wrap').html('');

        initErsatzteilReasontartSMonthSelect(<?=$thisYear?>, <?=$thisMonth?>);
    </script>

<?php get_footer();