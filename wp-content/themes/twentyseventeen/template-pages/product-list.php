<?php
/* Template Name: Idealhit Product List Page */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/3/19
 * Time: 17:07
 */
get_header();
?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Produktliste
                <small>Herzlich willkommen bei GoodOne Rechnungsplattform</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i>&nbsp;Home</a></li>
                <li>Produkt</li>
                <li class="active">Produktliste</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content container-fluid">

            <!--------------------------
              | Your Page Content Here |
              -------------------------->

            <div id="products-wrap">
                <div id="master-pros-wrap" class="fLeft">
                    <img src="/wp-content/uploads/images/loading-spinning-circles.svg" />
                </div>
                <div id="pro-elems-wrap" class="fLeft"></div>
                <div class="clear"></div>
            </div>
            <script>loadProducts("product-list");</script>


        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

<?php get_footer();