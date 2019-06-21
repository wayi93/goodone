<?php
/* Template Name: Idealhit Api GetUmsatzstatistik */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/11/02
 * Time: 16:24
 *
 * 参数1: $_POST["u_user_ids"]
 * 20,6,13,19
 * 13
 * 999999     代表整个平台全部
 *
 * 参数2: $_POST["zeitraum"]
 * 02.10.2018  bis  02.11.2018
 *
 * 返回的数据
 * 每天一个值
 * 当天一个总销量
 * Letzte 7 Tage总销量
 * Letzte 31 Tage总销量
 * Letzte 90 Tage总销量
 *
 */

include_once ( GET_STYLESHEET_DIRECTORY() . '/Util/Helper.php');
use SoGood\Support\Util\Helper;


/**
 * 判断，如果没有登陆，那么直接跳转到 Login 页面
 */
if(!is_user_logged_in()){
    //重定向浏览器
    header("Location: /do-login/");
    //确保重定向后，后续代码不会被执行
    exit;
}else{

    global $wpdb;
    $helper = new Helper();

    $action_name = 'GetUmsatzstatistik';
    $action_time = date("d.m.Y H:i:s", strtotime(' +' . $_settings_data['server-info']['gmt_offset'] . ' hour'));
    $isSuccess = false;
    $msg = '';
    $data = array();

    if(isset($_POST["u_user_ids"]) && isset($_POST["zeitraum"])){

        $user_ids = $_POST["u_user_ids"];
        $zeitraum = $_POST["zeitraum"];

        $umsatzList = array();
        $umsatzTotal = array(
            'UmsatzToday' => array(),
            'UmsatzYesterday' => array(),
            'Umsatz7Days_noToday' => array(),
            'Umsatz31Days_noToday' => array(),
            'Umsatz90Days_noToday' => array(),
            'UmsatzThisWeek' => array(),
            'UmsatzThisMonth' => array()
        );


        /**
         * 根据user-id的列表，直接先取出100天的销售数据
         */
        $dayBefore = date("Y-m-d 00:00:00", strtotime(' -100 day'));
        $dayBeforeInt = strtotime($dayBefore);

        $create_by_condition = " AND `create_by` IN (" . $user_ids .") ";
        if($user_ids == '999999'){
            $create_by_condition = "";
        }
        $sql = "SELECT `meta_id`, `create_at`, `create_by`, `paidSum`, `status` FROM `ihattach_orders` WHERE `deal_with` = 'order' AND `status` NOT LIKE '%Storniert%' " . $create_by_condition . " AND `create_at` >= $dayBeforeInt ORDER BY `create_at` DESC";
        $resultsInDB = $wpdb->get_results($sql);


        /**
         * 根据用户选择的时间区域，创造一个【日期的DATE数组】
         */
        $zeitraumList = explode("bis", $zeitraum);
        $startDate = trim($zeitraumList[0]);
        $endDate = trim($zeitraumList[1]);
        $startDateForDB = substr($startDate, 6, 4) . '-' . substr($startDate, 3, 2) . '-' . substr($startDate, 0, 2);
        $endDateForDB = substr($endDate, 6, 4) . '-' . substr($endDate, 3, 2) . '-' . substr($endDate, 0, 2);

        // 计算两个日期之间相差多少天
        $startDateForDB_dt = new DateTime($startDateForDB);
        $endDateForDB_dt = new DateTime($endDateForDB);
        $startEndDateDiff = $startDateForDB_dt->diff($endDateForDB_dt);

        for($i = 0; $i < intval($startEndDateDiff->days + 1); ++$i){

            $date_db_this = date("Y-m-d", strtotime($startDateForDB . " +" . $i . " day"));
            $date_de_this = substr($date_db_this, 8, 2) . '.' . substr($date_db_this, 5, 2) . '.' . substr($date_db_this, 0, 4);

            /**
             * 计算每一天的Umsatz 开始
             */
            $umsatzInOneDay = 0;
            $umsatzUnpaidInOneDay = 0;
            $time_start_float = strtotime($date_db_this . ' 00:00:00');
            $time_end_float = strtotime($date_db_this . '23:59:59');
            if(COUNT($resultsInDB) > 0){
                foreach ($resultsInDB as $itm){
                    $create_at_float = floatval($itm->create_at);
                    if($create_at_float >= floatval($time_start_float) && $create_at_float <= floatval($time_end_float)){
                        $umsatzInOneDay = $umsatzInOneDay + floatval($itm->paidSum);

                        // 把没有付款的Umsatz都加起来
                        if($helper->checkStrInString('Unbezahlt', $itm->status)){
                            $umsatzUnpaidInOneDay = $umsatzUnpaidInOneDay + floatval($itm->paidSum);
                        }
                    }
                }
            }
            /**
             * 计算每一天的Umsatz 结束
             */

            $umsatz_paid_float = $umsatzInOneDay - $umsatzUnpaidInOneDay;
            $aUmsatz = array(
                'date_de' => $date_de_this,
                'date_db' => $date_db_this,
                'umsatz_float' => $umsatzInOneDay,
                'umsatz_format_DE' => $helper->formatPrice($umsatzInOneDay, 'EUR'),
                'umsatz_unpaid_float' => $umsatzUnpaidInOneDay,
                'umsatz_unpaid_format_DE' => $helper->formatPrice($umsatzUnpaidInOneDay, 'EUR'),
                'umsatz_paid_float' => $umsatz_paid_float,
                'umsatz_paid_format_DE' => $helper->formatPrice($umsatz_paid_float, 'EUR')
            );
            array_push($umsatzList, $aUmsatz);

        }


        /**
         * 求出各种的总和
         */
        $umsatzSelected = 0;
        $umsatzSelected_unpaid = 0;
        $umsatzToday = 0;
        $umsatzToday_unpaid = 0;
        $umsatzYesterday = 0;
        $umsatzYesterday_unpaid = 0;
        $umsatz7Days_noToday = 0;
        $umsatz7Days_noToday_unpaid = 0;
        $umsatz31Days_noToday = 0;
        $umsatz31Days_noToday_unpaid = 0;
        $umsatz90Days_noToday = 0;
        $umsatz90Days_noToday_unpaid = 0;
        $umsatzThisWeek = 0;
        $umsatzThisWeek_unpaid = 0;
        $umsatzThisMonth = 0;
        $umsatzThisMonth_unpaid = 0;

        $Start_Selected = '';
        $end_Selected = '';
        $Start_01 = '';
        $Start_02 = '';
        $end_02 = '';
        $Start_03 = '';
        $end_03 = '';
        $Start_04 = '';
        $end_04 = '';
        $Start_05 = '';
        $end_05 = '';
        $Start_06 = '';
        $Start_07 = '';

        if(COUNT($resultsInDB) > 0){
            foreach ($resultsInDB as $aItm){
                $create_at_float = floatval($aItm->create_at);
                $paidSum_float = floatval($aItm->paidSum);

                // $umsatzSelected
                $Start_Selected = $startDateForDB . ' 00:00:00';
                $Start_Selected_f = strtotime($Start_Selected);
                $end_Selected = $endDateForDB . ' 23:59:59';
                $end_Selected_f = strtotime($end_Selected);
                if($create_at_float >= $Start_Selected_f && $create_at_float <= $end_Selected_f){
                    $umsatzSelected = $umsatzSelected + $paidSum_float;
                    // 把没有付款的Umsatz都加起来
                    if($helper->checkStrInString('Unbezahlt', $aItm->status)){
                        $umsatzSelected_unpaid = $umsatzSelected_unpaid + $paidSum_float;
                    }
                }

                // $umsatzToday
                $Start_01 = date("Y-m-d 00:00:00");
                $Start_01_f = strtotime($Start_01);
                if($create_at_float >= $Start_01_f){
                    $umsatzToday = $umsatzToday + $paidSum_float;
                    // 把没有付款的Umsatz都加起来
                    if($helper->checkStrInString('Unbezahlt', $aItm->status)){
                        $umsatzToday_unpaid = $umsatzToday_unpaid + $paidSum_float;
                    }
                }

                // $umsatzYesterday
                $Start_02 = date("Y-m-d 00:00:00", strtotime(' -1 day'));
                $Start_02_f = strtotime($Start_02);
                $end_02 = date("Y-m-d 23:59:59", strtotime(' -1 day'));
                $end_02_f = strtotime($end_02);
                if($create_at_float >= $Start_02_f && $create_at_float <= $end_02_f){
                    $umsatzYesterday = $umsatzYesterday + $paidSum_float;
                    // 把没有付款的Umsatz都加起来
                    if($helper->checkStrInString('Unbezahlt', $aItm->status)){
                        $umsatzYesterday_unpaid = $umsatzYesterday_unpaid + $paidSum_float;
                    }
                }

                // $umsatz7Days_noToday
                $Start_03 = date("Y-m-d 00:00:00", strtotime(' -7 day'));
                $Start_03_f = strtotime($Start_03);
                $end_03 = date("Y-m-d 23:59:59", strtotime(' -1 day'));
                $end_03_f = strtotime($end_03);
                if($create_at_float >= $Start_03_f && $create_at_float <= $end_03_f){
                    $umsatz7Days_noToday = $umsatz7Days_noToday + $paidSum_float;
                    // 把没有付款的Umsatz都加起来
                    if($helper->checkStrInString('Unbezahlt', $aItm->status)){
                        $umsatz7Days_noToday_unpaid = $umsatz7Days_noToday_unpaid + $paidSum_float;
                    }
                }

                // $umsatz31Days_noToday
                $Start_04 = date("Y-m-d 00:00:00", strtotime(' -31 day'));
                $Start_04_f = strtotime($Start_04);
                $end_04 = date("Y-m-d 23:59:59", strtotime(' -1 day'));
                $end_04_f = strtotime($end_04);
                if($create_at_float >= $Start_04_f && $create_at_float <= $end_04_f){
                    $umsatz31Days_noToday = $umsatz31Days_noToday + $paidSum_float;
                    // 把没有付款的Umsatz都加起来
                    if($helper->checkStrInString('Unbezahlt', $aItm->status)){
                        $umsatz31Days_noToday_unpaid = $umsatz31Days_noToday_unpaid + $paidSum_float;
                    }
                }

                // $umsatz90Days_noToday
                $Start_05 = date("Y-m-d 00:00:00", strtotime(' -90 day'));
                $Start_05_f = strtotime($Start_05);
                $end_05 = date("Y-m-d 23:59:59", strtotime(' -1 day'));
                $end_05_f = strtotime($end_05);
                if($create_at_float >= $Start_05_f && $create_at_float <= $end_05_f){
                    $umsatz90Days_noToday = $umsatz90Days_noToday + $paidSum_float;
                    // 把没有付款的Umsatz都加起来
                    if($helper->checkStrInString('Unbezahlt', $aItm->status)){
                        $umsatz90Days_noToday_unpaid = $umsatz90Days_noToday_unpaid + $paidSum_float;
                    }
                }

                // $umsatzThisWeek
                $Start_06 = date('Y-m-d 00:00:00', strtotime('-1 monday', time()));
                $Start_06_f = strtotime($Start_06);
                if($create_at_float >= $Start_06_f){
                    $umsatzThisWeek = $umsatzThisWeek + $paidSum_float;
                    // 把没有付款的Umsatz都加起来
                    if($helper->checkStrInString('Unbezahlt', $aItm->status)){
                        $umsatzThisWeek_unpaid = $umsatzThisWeek_unpaid + $paidSum_float;
                    }
                }

                // $umsatzThisMonth
                $Start_07 = date("Y-m-") . '01 00:00:00';
                $Start_07_f = strtotime($Start_07);
                if($create_at_float >= $Start_07_f){
                    $umsatzThisMonth = $umsatzThisMonth + $paidSum_float;
                    // 把没有付款的Umsatz都加起来
                    if($helper->checkStrInString('Unbezahlt', $aItm->status)){
                        $umsatzThisMonth_unpaid = $umsatzThisMonth_unpaid + $paidSum_float;
                    }
                }

            }
        }

        $umsatzTotal["UmsatzSelected"] = array(
            'total' => $umsatzSelected,
            'total_format_DE' => $helper->formatPrice($umsatzSelected, 'EUR'),
            'unpaid' => $umsatzSelected_unpaid,
            'unpaid_format_DE' => $helper->formatPrice($umsatzSelected_unpaid, 'EUR'),
            'paid' => ($umsatzSelected - $umsatzSelected_unpaid),
            'paid_format_DE' => $helper->formatPrice(($umsatzSelected - $umsatzSelected_unpaid), 'EUR'),
            'start_time' => $helper->getFormatDateByDate($Start_Selected, 'DE'),
            'end_time' => $helper->getFormatDateByDate($end_Selected, 'DE')
        );
        $umsatzTotal["UmsatzToday"] = array(
            'total' => $umsatzToday,
            'total_format_DE' => $helper->formatPrice($umsatzToday, 'EUR'),
            'unpaid' => $umsatzToday_unpaid,
            'unpaid_format_DE' => $helper->formatPrice($umsatzToday_unpaid, 'EUR'),
            'paid' => ($umsatzToday - $umsatzToday_unpaid),
            'paid_format_DE' => $helper->formatPrice(($umsatzToday - $umsatzToday_unpaid), 'EUR'),
            'start_time' => $helper->getFormatDateByDate($Start_01, 'DE'),
            'end_time' => $action_time
        );
        $umsatzTotal["UmsatzYesterday"] = array(
            'total' => $umsatzYesterday,
            'total_format_DE' => $helper->formatPrice($umsatzYesterday, 'EUR'),
            'unpaid' => $umsatzYesterday_unpaid,
            'unpaid_format_DE' => $helper->formatPrice($umsatzYesterday_unpaid, 'EUR'),
            'paid' => ($umsatzYesterday - $umsatzYesterday_unpaid),
            'paid_format_DE' => $helper->formatPrice(($umsatzYesterday - $umsatzYesterday_unpaid), 'EUR'),
            'start_time' => $helper->getFormatDateByDate($Start_02, 'DE'),
            'end_time' => $helper->getFormatDateByDate($end_02, 'DE')
        );
        $umsatzTotal["Umsatz7Days_noToday"] = array(
            'total' => $umsatz7Days_noToday,
            'total_format_DE' => $helper->formatPrice($umsatz7Days_noToday, 'EUR'),
            'unpaid' => $umsatz7Days_noToday_unpaid,
            'unpaid_format_DE' => $helper->formatPrice($umsatz7Days_noToday_unpaid, 'EUR'),
            'paid' => ($umsatz7Days_noToday - $umsatz7Days_noToday_unpaid),
            'paid_format_DE' => $helper->formatPrice(($umsatz7Days_noToday - $umsatz7Days_noToday_unpaid), 'EUR'),
            'start_time' => $helper->getFormatDateByDate($Start_03, 'DE'),
            'end_time' => $helper->getFormatDateByDate($end_03, 'DE')
        );
        $umsatzTotal["Umsatz31Days_noToday"] = array(
            'total' => $umsatz31Days_noToday,
            'total_format_DE' => $helper->formatPrice($umsatz31Days_noToday, 'EUR'),
            'unpaid' => $umsatz31Days_noToday_unpaid,
            'unpaid_format_DE' => $helper->formatPrice($umsatz31Days_noToday_unpaid, 'EUR'),
            'paid' => ($umsatz31Days_noToday - $umsatz31Days_noToday_unpaid),
            'paid_format_DE' => $helper->formatPrice(($umsatz31Days_noToday - $umsatz31Days_noToday_unpaid), 'EUR'),
            'start_time' => $helper->getFormatDateByDate($Start_04, 'DE'),
            'end_time' => $helper->getFormatDateByDate($end_04, 'DE')
        );
        $umsatzTotal["Umsatz90Days_noToday"] = array(
            'total' => $umsatz90Days_noToday,
            'total_format_DE' => $helper->formatPrice($umsatz90Days_noToday, 'EUR'),
            'unpaid' => $umsatz90Days_noToday_unpaid,
            'unpaid_format_DE' => $helper->formatPrice($umsatz90Days_noToday_unpaid, 'EUR'),
            'paid' => ($umsatz90Days_noToday - $umsatz90Days_noToday_unpaid),
            'paid_format_DE' => $helper->formatPrice(($umsatz90Days_noToday - $umsatz90Days_noToday_unpaid), 'EUR'),
            'start_time' => $helper->getFormatDateByDate($Start_05, 'DE'),
            'end_time' => $helper->getFormatDateByDate($end_05, 'DE')
        );
        $umsatzTotal["UmsatzThisWeek"] = array(
            'total' => $umsatzThisWeek,
            'total_format_DE' => $helper->formatPrice($umsatzThisWeek, 'EUR'),
            'unpaid' => $umsatzThisWeek_unpaid,
            'unpaid_format_DE' => $helper->formatPrice($umsatzThisWeek_unpaid, 'EUR'),
            'paid' => ($umsatzThisWeek - $umsatzThisWeek_unpaid),
            'paid_format_DE' => $helper->formatPrice(($umsatzThisWeek - $umsatzThisWeek_unpaid), 'EUR'),
            'start_time' => $helper->getFormatDateByDate($Start_06, 'DE'),
            'end_time' => $action_time
        );
        $umsatzTotal["UmsatzThisMonth"] = array(
            'total' => $umsatzThisMonth,
            'total_format_DE' => $helper->formatPrice($umsatzThisMonth, 'EUR'),
            'unpaid' => $umsatzThisMonth_unpaid,
            'unpaid_format_DE' => $helper->formatPrice($umsatzThisMonth_unpaid, 'EUR'),
            'paid' => ($umsatzThisMonth - $umsatzThisMonth_unpaid),
            'paid_format_DE' => $helper->formatPrice(($umsatzThisMonth - $umsatzThisMonth_unpaid), 'EUR'),
            'start_time' => $helper->getFormatDateByDate($Start_07, 'DE'),
            'end_time' => $action_time
        );


        /**
         * 输出UmsatzList
         */
        $data["Info"] = array(
            'User-IDs' => $user_ids,
            'StartDate' => $startDate,
            'EndDate' => $endDate,
        );
        $data["UmsatzTotal"] = $umsatzTotal;
        $data["UmsatzList"] = $umsatzList;

        $isSuccess = true;
        $msg = 'Umsatz Daten wurde erfolgreich geladen.';


    }else{

        $isSuccess = false;
        $msg = 'There is no parameter!';

    }

    $results = array(
        'action' => $action_name,
        'time' => $action_time,
        'isSuccess' => $isSuccess,
        'msg' => $msg,
        'data_quantity' => COUNT($data),
        'data' => $data
    );

    echo json_encode($results);


}