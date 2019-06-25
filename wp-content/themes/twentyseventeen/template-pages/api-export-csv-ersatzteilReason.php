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
        $sql = "SELECT ihp.ean, ihp.title AS name, iho.afterbuy_account, iho.order_id_ab, ihp.order_id, ihp.create_at, ihp.reasons, ihu.display_name AS create_by 
              FROM `" . $db_table_positions . "` AS ihp 
              LEFT JOIN `" . $db_table_orders . "` AS iho ON ihp.order_id = iho.meta_id
              LEFT JOIN `" . $db_table_users . "` AS ihu ON ihp.create_by = ihu.ID
              WHERE iho.deal_with = %s";
        $ersatzteilList = $wpdb->get_results($wpdb->prepare($sql, $orderTyp));

        // load all reason text
        $sql_rs = "SELECT `meta_id`, `reason` FROM `" . $db_table_reasons . "` WHERE 1=1";
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
                $titleList = array('EAN', 'Name', 'Order_ID_GoodOne', 'Order_ID_Afterbuy', 'Afterbuy_Konto', 'Erstelldatum', 'User');
                foreach ($reasonList AS $record_rs){
                    array_push($titleList, $record_rs->reason);
                }
                fputcsv($csv_file, $titleList);

                // set records
                foreach ($ersatzteilList AS $record_el){
                    $recordList = array(
                        $record_el->ean,
                        $record_el->name,
                        intval($record_el->order_id) + 3000000,
                        $record_el->order_id_ab,
                        $record_el->afterbuy_account,
                        $record_el->create_at,
                        $record_el->create_by
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

                $datas["positions"] = $ersatzteilList;
                $datas["reasons"] = $reasonList;

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