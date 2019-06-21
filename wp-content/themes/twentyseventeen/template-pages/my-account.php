<?php
/* Template Name: Idealhit My Account Page */
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
            Mein Konto
            <small>Herzlich willkommen bei GoodOne Rechnungsplattform</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-dashboard"></i>&nbsp;Home</a></li>
            <li class="active">Mein Konto</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

        <!--------------------------
          | Your Page Content Here |
          -------------------------->

        <div class="row">
            <div class="col-md-3">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Kontodetails von Ihnen</h3>
                    </div>
                    <div class="box-body box-profile" style="padding-bottom: 40px;">
                        <div class="text-row" style="border-top: 1px solid #f4f4f4;">&nbsp;&nbsp;Benutzername: <?php echo $current_user->user_login; ?></div>
                        <div class="text-row">&nbsp;&nbsp;Passwort: <span id="pwd-input-wrap">&middot;&middot;&middot;&middot;&middot;&middot;&middot;&middot;</span></div>
                        <div class="text-row">&nbsp;&nbsp;E-Mail: <?php echo $current_user->user_email; ?></div>
                        <div class="text-row">&nbsp;&nbsp;Name: <?php echo ($current_user->user_firstname . "&nbsp;". $current_user->user_lastname); ?></div>
                        <div class="padding-t-20"><span class="btn btn-primary" id="konto-page-change-psw-btn" onclick="activeInputForChangingPsw();">Passwort ändern</span></div>
                    </div>
                </div>

            </div>
            <div class="col-md-9"></div>
        </div>

        <?php

        /**
         * 得到当前用户信息
         */

        //echo 'User level: ' . $current_user->user_level . "<br>";
        //echo 'User group: ' . $current_user->roles[0] . "<br>";
        //echo 'User first name: ' . $current_user->user_firstname . "<br>";
        //echo 'User last name: ' . $current_user->user_lastname . "<br>";
        //echo 'User display name: ' . $current_user->display_name . "<br>";
        //echo 'User ID: ' . $current_user->ID . "<br>";

        ?>


    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php get_footer();