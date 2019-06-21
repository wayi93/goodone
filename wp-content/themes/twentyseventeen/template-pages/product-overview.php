<?php
/* Template Name: Idealhit Product Overview Page */
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
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Produktübersicht
            <small>Herzlich willkommen bei GoodOne Rechnungsplattform</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-dashboard"></i>&nbsp;Home</a></li>
            <li>Produkt</li>
            <li class="active">Produktübersicht</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">


        <!--------------------------
          | 两个大饼图 |
          -------------------------->
        <div id="Charts-Wrap" class="row">

            <div class="col-md-6">
                <!-- Products DONUT CHART 0 -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Inventarverfügbarkeit (Produktmenge auf Lager)</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <canvas id="productPieChart-0" style="height:250px"></canvas>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

            <div class="col-md-6">
                <!-- Products DONUT CHART 1 -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Produktvariante (Menge)</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <canvas id="productPieChart-1" style="height:250px"></canvas>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

        </div>

        <script>loadProducts("product-overview-0");loadProducts("product-overview-1");</script>


    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php get_footer();