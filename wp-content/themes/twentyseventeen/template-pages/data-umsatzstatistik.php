<?php
/* Template Name: Idealhit Umsatzstatistik Page */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/3/19
 * Time: 17:07
 */
get_header();

global $wpdb;
global $current_user;
get_current_user();
$current_user_id = $current_user->ID;

/**
 * $UserTyp
 * 0 所有用户
 * 1 Showroom老板Murat
 * 2 黄总
 */
$UserTyp = 0;
$UserList = array();
$sql = 'SELECT `ID`, `user_login`, `display_name` FROM `ih_users` WHERE `ID` NOT IN (1) ORDER BY `display_name` ASC;';
$user_ids_db = $wpdb->get_results($sql);
switch ($current_user->user_login){
    case 'murat': //murat
        $UserTyp = 1;
        break;
    case 'huang': //huang
        $UserTyp = 2;
        break;
    case 'gao':
        $UserTyp = 2;
        break;
    case 'ying':
        $UserTyp = 2;
        break;
    case 'alicia':
        $UserTyp = 2;
        break;
    case 'tao':
        $UserTyp = 2;
        break;
    default:
        $UserTyp = 0;
}

switch ($UserTyp) {
    case 1:
        if(COUNT($user_ids_db) > 0){
            $all_user_ids = '';
            foreach ($user_ids_db as $aRow) {
                $aUser_data = get_userdata($aRow->ID);
                $aUser_group = $aUser_data->roles[0];
                if($aUser_group == 'showroom'){
                    $all_user_ids = $all_user_ids . $aRow->ID . ',';
                    $aUser = array(
                        'ID' => $aRow->ID,
                        'user_login' => $aRow->user_login,
                        'display_name' => $aRow->display_name,
                        'user_group' => $aUser_group
                    );
                    array_push($UserList, $aUser);
                }
            }
            array_push($UserList, array(
                'ID' => substr(  $all_user_ids,0,strlen($all_user_ids)-1 ),
                'user_login' => '',
                'display_name' => 'Showroom Alle',
                'user_group' => 'showroom'
            ));
        }
        break;
    case 2:
        if(COUNT($user_ids_db) > 0){

            // Group Showroom
            $all_user_ids = '';
            foreach ($user_ids_db as $aRow) {
                $aUser_data = get_userdata($aRow->ID);
                $aUser_group = $aUser_data->roles[0];
                if($aUser_group == 'showroom'){
                    $all_user_ids = $all_user_ids . $aRow->ID . ',';
                    $aUser = array(
                        'ID' => $aRow->ID,
                        'user_login' => $aRow->user_login,
                        'display_name' => $aRow->display_name,
                        'user_group' => $aUser_group
                    );
                    array_push($UserList, $aUser);
                }
            }
            array_push($UserList, array(
                'ID' => substr(  $all_user_ids,0,strlen($all_user_ids)-1 ),
                'user_login' => '',
                'display_name' => 'Showroom Alle',
                'user_group' => 'showroom'
            ));

            // Group Sales
            $all_user_ids = '';
            foreach ($user_ids_db as $aRow) {
                $aUser_data = get_userdata($aRow->ID);
                $aUser_group = $aUser_data->roles[0];
                if($aUser_group == 'sales'){
                    $all_user_ids = $all_user_ids . $aRow->ID . ',';
                    $aUser = array(
                        'ID' => $aRow->ID,
                        'user_login' => $aRow->user_login,
                        'display_name' => $aRow->display_name,
                        'user_group' => $aUser_group
                    );
                    array_push($UserList, $aUser);
                }
            }
            array_push($UserList, array(
                'ID' => substr(  $all_user_ids,0,strlen($all_user_ids)-1 ),
                'user_login' => '',
                'display_name' => 'Sales Alle',
                'user_group' => 'sales'
            ));

            // Group Support
            $all_user_ids = '';
            foreach ($user_ids_db as $aRow) {
                $aUser_data = get_userdata($aRow->ID);
                $aUser_group = $aUser_data->roles[0];
                if($aUser_group == 'support'){
                    $all_user_ids = $all_user_ids . $aRow->ID . ',';
                    $aUser = array(
                        'ID' => $aRow->ID,
                        'user_login' => $aRow->user_login,
                        'display_name' => $aRow->display_name,
                        'user_group' => $aUser_group
                    );
                    array_push($UserList, $aUser);
                }
            }
            array_push($UserList, array(
                'ID' => substr(  $all_user_ids,0,strlen($all_user_ids)-1 ),
                'user_login' => '',
                'display_name' => 'Support Alle',
                'user_group' => 'support'
            ));

            // Group Marketing
            $all_user_ids = '';
            foreach ($user_ids_db as $aRow) {
                $aUser_data = get_userdata($aRow->ID);
                $aUser_group = $aUser_data->roles[0];
                if($aUser_group == 'marketing'){
                    $all_user_ids = $all_user_ids . $aRow->ID . ',';
                    $aUser = array(
                        'ID' => $aRow->ID,
                        'user_login' => $aRow->user_login,
                        'display_name' => $aRow->display_name,
                        'user_group' => $aUser_group
                    );
                    array_push($UserList, $aUser);
                }
            }
            array_push($UserList, array(
                'ID' => substr(  $all_user_ids,0,strlen($all_user_ids)-1 ),
                'user_login' => '',
                'display_name' => 'Marketing Alle',
                'user_group' => 'marketing'
            ));

            // Group Einkauf
            $all_user_ids = '';
            foreach ($user_ids_db as $aRow) {
                $aUser_data = get_userdata($aRow->ID);
                $aUser_group = $aUser_data->roles[0];
                if($aUser_group == 'einkauf'){
                    $all_user_ids = $all_user_ids . $aRow->ID . ',';
                    $aUser = array(
                        'ID' => $aRow->ID,
                        'user_login' => $aRow->user_login,
                        'display_name' => $aRow->display_name,
                        'user_group' => $aUser_group
                    );
                    array_push($UserList, $aUser);
                }
            }
            array_push($UserList, array(
                'ID' => substr(  $all_user_ids,0,strlen($all_user_ids)-1 ),
                'user_login' => '',
                'display_name' => 'Einkauf Alle',
                'user_group' => 'einkauf'
            ));

        }
        break;
    default:
        //
}


?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Umsatzstatistik
            <small>Herzlich willkommen bei GoodOne Rechnungsplattform</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-dashboard"></i>&nbsp;Home</a></li>
            <li>Daten</li>
            <li class="active">Umsatzstatistik</li>
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
                        <?php if($UserTyp == 0){ ?>
                            <h3 class="box-title">Prüfen Sie Ihren Umsatz</h3>
                        <?php }elseif ($UserTyp == 1){ ?>
                            <h3 class="box-title">Prüfen Sie den Showroom Umsatz</h3>
                        <?php }elseif ($UserTyp == 2){ ?>
                            <h3 class="box-title">Prüfen Sie den Umsatz</h3>
                        <?php } ?>
                    </div>
                    <div class="box-body box-profile">
                        <div id="control-panel" class="row umsatzstatistik-control-panel">

                            <!-- Zeitraum Input -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon" style="height: 34.5px;">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" style="height: 34.5px;" id="start-end-date">
                                    </div>
                                </div>
                            </div>

                            <!-- User select -->
                            <?php if ($UserTyp == 1 || $UserTyp == 2){ ?>
                                <div class="col-md-4" style="margin-bottom: 15px;">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa  fa-user"></i></span>
                                        <select id="umsatzstatistik-user-ids" class="form-control">
                                            <option value="0" style="color: #b2b2b2;">--- Mitarbeiter wählen ---</option>
                                            <?php
                                            if(COUNT($UserList) > 0){
                                                foreach ($UserList as $aU){
                                                    // 设定Option的背景颜色
                                                    $option_style = '';
                                                    switch ($aU["user_group"]){
                                                        case 'showroom':
                                                            $option_style = 'background-color: #FFCCCC;';
                                                            break;
                                                        case 'sales':
                                                            $option_style = 'background-color: #FFFFCC;';
                                                            break;
                                                        case 'support':
                                                            $option_style = 'background-color: #CCFFFF;';
                                                            break;
                                                        case 'marketing':
                                                            $option_style = 'background-color: #ffe0e0;';
                                                            break;
                                                        case 'einkauf':
                                                            $option_style = 'background-color: #e5cccc;';
                                                            break;
                                                        default:
                                                            //
                                                    }
                                                    // 如果是老板登陆，那就在用户名字后面显示 [用户组]
                                                    if($UserTyp == 2){
                                                        echo '<option value="' . $aU["ID"] . '" style="' . $option_style . '">' . $aU["display_name"] . ' [' . ucfirst($aU["user_group"]) . ']</option>';
                                                    }else{
                                                        echo '<option value="' . $aU["ID"] . '" style="' . $option_style . '">' . $aU["display_name"] . '</option>';
                                                    }
                                                }

                                                if($UserTyp == 2){ echo '<option value="999999" style="background-color: #FFFF99;" selected>Ganze GoodOne Plattform</option>'; }

                                            }
                                            ?>
                                        </select>
                                        </div>
                                    <?php //$UserList; ?>
                                </div>
                            <?php } ?>

                            <!-- Button -->
                            <div class="col-md-4">
                                <div id="btn-show-umsatz" class="btn btn-primary" onclick="showUmsatz(true,<?=$current_user_id?>);" >
                                    Umsatz anzeigen
                                </div>
                            </div>


                        </div>
                        <div id="result-panel" class="row umsatzstatistik-result-panel">
                            <div id="result-chart" class="col-md-12">
                            </div>
                            <div id="umsatz-result-total-table-wrap" class="col-md-12">
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>

    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->


    <script>
        /**
         * 根据屏幕的宽度，决定默认天数
         */
        var winAvaWidth = window.screen.availWidth;
        var startDate_param1 = Math.round(winAvaWidth / 30);
        var startDate_param2 = 'days';
        /*
        if(winAvaWidth < 800){
            startDate_param1 = 15;
            startDate_param2 = 'days';
        }else if(winAvaWidth < 1024){
            startDate_param1 = 1;
            startDate_param2 = 'month';
        }else{
            startDate_param1 = 2;
            startDate_param2 = 'month';
        }
        */
        //Date range picker
        $('#start-end-date').daterangepicker({
            //"autoUpdateInput": true,
            "singleDatePicker": false,
            "showDropdowns": false,
            "language": "de-DE",
            //"autoclose": true,
            "maxDate": new Date(),
            "minDate": moment().subtract(90, 'days').format("DD.MM.YYYY"),
            "startDate": moment().subtract(startDate_param1, startDate_param2).format("DD.MM.YYYY"),
            "endDate": moment().format("DD.MM.YYYY"),
            "ranges": {
                'Heute': [moment(), moment()],
                'Gestern': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Letzte 7 Tage': [moment().subtract(6, 'days'), moment()],
                'Letzte 31 Tage': [moment().subtract(30, 'days'), moment()],
                'Letzte 90 Tage': [moment().subtract(89, 'days'), moment()],
                'In dieser Woche': [moment().startOf('week'), moment().endOf('week')],
                'In diesem Monat': [moment().startOf('month'), moment().endOf('month')]
            },
            "locale": {
                "format": 'DD.MM.YYYY',
                "separator": "  bis  ",
                "applyLabel": "Ja, wählen",
                "cancelLabel": "Abbrechen",
                "fromLabel": "Von",
                "toLabel": "Bis",
                "customRangeLabel": "Anderer Zeitraum [Kalender anzeigen]",
                "daysOfWeek": [
                    "So.",
                    "Mo.",
                    "Di.",
                    "Mi.",
                    "Do.",
                    "Fr.",
                    "Sa."
                ],
                "monthNames": [
                    "Januar",
                    "Februar",
                    "März",
                    "April",
                    "Mai",
                    "Juni",
                    "Juli",
                    "August",
                    "September",
                    "Oktober",
                    "November",
                    "Dezember"
                ],
                "firstDay": 1
            }
        });

        showUmsatz(false,<?=$current_user_id?>);
    </script>

<?php get_footer();
