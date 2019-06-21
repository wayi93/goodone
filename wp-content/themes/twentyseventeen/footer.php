<?php
$location_inc_js = "/wp-includes/js/";
$location_adminLTE = "/wp-includes/lib/AdminLTE/";
?>

<!-- Main Footer -->
<footer class="main-footer no-print">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
        GoodOne Rechnungsplattform
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2018 <a href="#">GoodOne</a> Rechnungsplattform.</strong> All rights reserved.
</footer>


<!-- Warenkorb Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <div id="warenkorb-sidebar-wrap"></div>
</aside>
<!-- /.control-sidebar -->
<!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>



</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<!-- 更新购物车 -->
<script>updateProductDataBackendWeak("BackendOnly");updateShoppingCart();</script>

<!-- Bootstrap 3.3.7 -->
<script src="<?=$location_adminLTE;?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=$location_adminLTE;?>dist/js/adminlte.min.js"></script>
<!-- Btn Event -->
<script src="<?=$location_inc_js;?>btn-event.js?version=18.05.08.01"></script>
<!-- Cronjob -->
<script src="<?=$location_inc_js;?>cronjob.js?version=18.04.23.03"></script>



<!-- 根据Session里面的值,[scrollPageCode],适当的滚动页面到适当的位置 -->
<?php
if(isset($_SESSION['scrollPageCode'])){
    echo '<script>jQuery(document).ready(function($) { scrollPage('.$_SESSION["scrollPageCode"].'); });</script>';
}
?>


</body>
</html>