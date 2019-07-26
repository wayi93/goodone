<?php
/* Template Name: Idealhit Api Export CSV ErsatzteilReason */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/4/25
 * Time: 16:24
 */



include_once ( GET_STYLESHEET_DIRECTORY() . '/Util/Helper.php');
use SoGood\Support\Util\Helper;
$helper = new Helper();

set_time_limit(600);



/**
 * 判断，如果没有登陆，那么直接跳转到 Login 页面
 */
if(!is_user_logged_in())
{
    header("Location: /do-login/");exit;
} else
{



    /**
     * return value
     */
    $action_name = 'Export-CSV-ErsatzteilReason';
    $isSuccess = false;
    $msg = '';
    $datas = array();



    /**
     * URL Parameters
     * data_format 'csv' or 'json'
     */
    if(isset($_POST["data_format"]))
    {

        $dataFormat = $_POST["data_format"];



        /**
         * initial db conn info
         */
        global $wpdb;
        $db_table_orders = "ihattach_orders";
        $db_table_positions = "ihattach_positions";
        $db_table_reasons = "ihattach_ersatzteil_reasons";
        $db_table_users = "ih_users";



        /**
         * define sql for transaction
         */
        $wpdb->query('START TRANSACTION');
        $orderTyp = 'ersatzteil';

        // load all positions
        $sql = "SELECT ihp.ean, ihp.mapping, ihp.title AS name, ihp.quantity_want AS quantity, iho.customer_shipping_postalCode, iho.customer_shipping_country, iho.afterbuy_account, iho.order_id_ab, ihp.order_id, ihp.create_at, ihp.reasons, ihu.display_name AS create_by 
              FROM `" . $db_table_positions . "` AS ihp 
              LEFT JOIN `" . $db_table_orders . "` AS iho ON ihp.order_id = iho.meta_id
              LEFT JOIN `" . $db_table_users . "` AS ihu ON ihp.create_by = ihu.ID
              WHERE iho.deal_with = %s AND iho.status <> 'Storniert'";
        $ersatzteilList = $wpdb->get_results($wpdb->prepare($sql, $orderTyp));

        // load all reason text
        $sql_rs = "SELECT `meta_id`, `reason` FROM `" . $db_table_reasons . "` WHERE `type`= '" . $orderTyp . "'";
        $reasonList = $wpdb->get_results($sql_rs);



        /**
         * finish transaction
         */
        if($ersatzteilList && $reasonList) {
            $wpdb->query('COMMIT'); // if you come here then well done
            $isSuccess = true;
            $msg = 'Daten wurden erfolgreich geladen.';
        }
        else {
            $wpdb->query('ROLLBACK'); // something went wrong, Rollback
            $isSuccess = false;
            $msg = 'TRANSACTION Fehler beim Daten-Laden.';
        }



        /**
         * test
         */
        //var_dump($ersatzteilList);
        //var_dump($reasonList);
        //die('test...');



        /**
         * prepare the array $datas
         */
        //$datas["positions"] = $ersatzteilList;
        //$datas["reasons"] = $reasonList;



        switch ($dataFormat)
        {
            case 'csv':


                /**
                 * write csv file to local
                 */
                $datas["csv_info"] = array(
                    'name' => 'ersatzteil_reasons_' . date("Y.m.d_H.i.s", time() + ( $_settings_data['server-info']['gmt_offset'] * 3600 )),
                    'extension' => '.csv',
                    'path' => dirname(dirname(dirname(dirname(__FILE__)))) . '/uploads/export/'
                );
                $csv_file = fopen($datas["csv_info"]["path"] . $datas["csv_info"]["name"] . $datas["csv_info"]["extension"], 'w');

                // set title line
                $titleList = array('EAN', 'Menge', 'Name', 'Vom_Produkt', 'Attachment_Quantity', 'Shipping_Country', 'Shipping_PostalCode', 'Order_ID_GoodOne', 'Order_ID_Afterbuy', 'Afterbuy_Konto', 'Erstelldatum', 'User');
                foreach ($reasonList AS $record_rs){
                    array_push($titleList, '[' . $record_rs->meta_id . ']' . $helper->escapeHtmlValue($helper->removeComma($record_rs->reason)));
                }
                fputcsv($csv_file, $titleList);

                // set records
                foreach ($ersatzteilList AS $record_el){

                    $afterbuyAccount = $record_el->afterbuy_account;
                    switch ($afterbuyAccount)
                    {
                        case 'sogood':
                            $afterbuyAccount = 'Sogood';
                            break;
                        default:
                            $afterbuyAccount = 'Mai&Mai';
                    }

                    $ean_vom_product = (strlen($record_el->mapping) > 13) ? substr($record_el->mapping, 0, 13) : $record_el->mapping;

                    $orderID_GoodOne = intval($record_el->order_id) + 3000000;

                    $recordList = array(
                        $helper->get4250ean($record_el->ean),
                        $record_el->quantity,
                        $helper->escapeHtmlValue($record_el->name),
                        $helper->get4250ean($ean_vom_product),
                        $helper->getFileQuantity($orderID_GoodOne),
                        $record_el->customer_shipping_country,
                        $record_el->customer_shipping_postalCode,
                        $orderID_GoodOne,
                        $record_el->order_id_ab,
                        $afterbuyAccount,
                        $helper->getFormatDateByDate(date("Y-m-d H:i:s", $record_el->create_at), 'DE-NO-TIME'),
                        $helper->escapeHtmlValue($record_el->create_by)
                    );
                    foreach ($reasonList AS $record_rs){
                        if(in_array($record_rs->meta_id, explode(',', $record_el->reasons))){
                            array_push($recordList, 1);
                        }else{
                            array_push($recordList, 0);
                        }
                    }
                    fputcsv($csv_file, $recordList);
                }

                // close file
                fclose($csv_file);


                break;
            case 'json':

                $startYear = 0;
                $startMonth = 0;
                $thisYear = intval(date('Y'));
                $thisMonth = intval(date('m'));
                $showYear = 0;
                $showMonth = 0;

                if(isset($_POST["start_year"]) && isset($_POST["start_month"])){
                    $startYear = intval($_POST["start_year"]);
                    $startMonth = intval($_POST["start_month"]);
                }else if(isset($_POST["show_year"]) && isset($_POST["show_month"])){
                    $showYear = intval($_POST["show_year"]);
                    $showMonth = intval($_POST["show_month"]);
                }


                $dateKeyList = array();
                if($startYear !== 0 && $startMonth !== 0){
                    if($startYear === $thisYear){
                        // only this year
                        for($sm_j = $startMonth; $sm_j <= $thisMonth; ++$sm_j){
                            array_push($dateKeyList, $thisYear . '-' . (($sm_j < 10) ? ('0' . $sm_j) : $sm_j) );
                        }
                    }else{
                        // more than 1 year
                        for($sy_i = $startYear; $sy_i <= $thisYear; ++$sy_i){
                            if($sy_i === $startYear){
                                // first year
                                for($sm_j = $startMonth; $sm_j <= 12; ++$sm_j){
                                    array_push($dateKeyList, $sy_i . '-' . (($sm_j < 10) ? ('0' . $sm_j) : $sm_j) );
                                }
                            }else if($sy_i === $thisYear){
                                // last year
                                for($sm_j = 1; $sm_j <= $thisMonth; ++$sm_j){
                                    array_push($dateKeyList, $sy_i . '-' . (($sm_j < 10) ? ('0' . $sm_j) : $sm_j) );
                                }
                            }else{
                                for($sm_j = 1; $sm_j <= 12; ++$sm_j){
                                    array_push($dateKeyList, $sy_i . '-' . (($sm_j < 10) ? ('0' . $sm_j) : $sm_j) );
                                }
                            }
                        }
                    }
                }
                if($showYear !== 0 && $showMonth !== 0){
                    array_push($dateKeyList, $showYear . '-' . (($showMonth < 10) ? ('0' . $showMonth) : $showMonth) );
                }


                $datas["date_keys"] = $dateKeyList;
                $datas["reasons"] = $reasonList;
                $datas["positions"] = array();


                foreach ($dateKeyList AS $dateKey){
                    foreach ($ersatzteilList AS $record_el2){

                        $createAt = $helper->getFormatDateByDate(date("Y-m-d H:i:s", $record_el2->create_at), 'DE-NO-TIME');

                        if($dateKey === (substr($createAt, 6, 4) . '-' . substr($createAt, 3, 2)))
                        {

                            if(!array_key_exists($dateKey, $datas["positions"])){
                                $datas["positions"][$dateKey] = array();
                            }

                            if(strlen($record_el2->mapping) > 0){
                                array_push($datas["positions"][$dateKey], array(
                                    'ean' => $record_el2->ean,
                                    'quantity' => $record_el2->quantity,
                                    'name' => $helper->escapeHtmlValue($record_el2->name),
                                    'mapping' => $record_el2->mapping,
                                    'order_id' => intval($record_el2->order_id) + 3000000,
                                    'order_id_ab' => $record_el2->order_id_ab,
                                    'afterbuy_account' => ($record_el2->afterbuy_account === 'sogood') ? 'Sogood' : 'Mai&Mai',
                                    'create_at' => $createAt,
                                    'create_by' => $record_el2->create_by,
                                    'reasons' => $record_el2->reasons
                                ));
                            }

                        }

                    }
                }

                break;
            default:
                //

        }



    }else{

        $isSuccess = false;
        $msg = 'Parameters not found!';

    }



    /**
     * export return value
     */
    $results = array(
        'action' => $action_name,
        'isSuccess' => $isSuccess,
        'msg' => $msg,
        'datas' => $datas
    );
    echo json_encode($results);



}