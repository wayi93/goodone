<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

get_header(); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                404 Fehlerseite
                <small>HTTP Fehler 404 Seite nicht gefunden!</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i>&nbsp;Home</a></li>
                <li class="active">404 Fehlerseite</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content container-fluid">

            <img class="img-404-error" src="/wp-includes/images/404.png" />


        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

<?php get_footer();
