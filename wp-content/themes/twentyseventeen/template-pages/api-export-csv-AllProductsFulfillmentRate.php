<?php
/* Template Name: Idealhit Api Export CSV AllProductsFulfillmentRate */
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
if(!is_user_logged_in()){
    //重定向浏览器
    header("Location: /do-login/");
    //确保重定向后，后续代码不会被执行
    exit;
}else{

$action_name = 'Export CSV AllProductsFulfillmentRate';
$isSuccess = false;
$msg = '';
$datas = array();

    $product_stock_dir = dirname(dirname(dirname(dirname(__FILE__)))) . '/uploads/data/lagerbestand/';
    $fulfillment_save_path = dirname(dirname(dirname(dirname(__FILE__)))) . '/uploads/export/';
    $all_products_stock_datas = array();

    $fulfillmentList = array();

    // 先读取数据，再整理分析
    if(isset($_POST["zr"])){

        $zr = $_POST["zr"];
        $erstelldatum = time() + ( $_settings_data['server-info']['gmt_offset'] * 3600 );
        $csv_name = 'AllProductsFulfillmentRate_' . $zr . 'days_' . date("Y.m.d_H.i.s", $erstelldatum) . '.csv';

        $product_stock_history_csv_paths = $helper->getFilePaths($product_stock_dir, false);
        foreach ($product_stock_history_csv_paths as $val){

            // 根据CSV文件的名字，获取日期信息
            $linShiArr = explode("dump_element.", $val);
            $linShiArr1 = explode("-06.", $linShiArr[1]);
            $datum = $linShiArr1[0];

            /**
             * 读取所有EAN的库存信息
             */
            if($helper->isDateActived($datum, $zr)){
                $file = fopen($val,"r");
                while(! feof($file))
                {

                    $aRowInCsv = fgetcsv($file)[0];
                    $aRowInCsvArr = explode("\t", $aRowInCsv);
                    if($aRowInCsvArr[0] != 'id'){
                        $ean = $aRowInCsvArr[1];
                        if($aRowInCsvArr[3] != null){
                            $q = intval($aRowInCsvArr[3]);
                            if($q < 0){
                                $q = 0;
                            }
                            $all_products_stock_datas[$ean]["category"] = $aRowInCsvArr[2];
                            $all_products_stock_datas[$ean]["name"] = $aRowInCsvArr[5];
                            $all_products_stock_datas[$ean]["stocks"][$datum] = $q;
                        }
                    }

                }
                fclose($file);
            }

            /**
             * 根据所有库存信息 $all_products_stock_datas，计算 Fulfillment
             */
            $erstelldatumStr = date("Y-m-d H:i:s", $erstelldatum);
            $erstelldatumStr_de = date("d.m.Y H:i:s", $erstelldatum);
            $datas["erstelldatum_de"] = $erstelldatumStr_de;
            foreach ($all_products_stock_datas AS $ean => $stock_datas){

                // 计算每个ean的fulfillment
                $f_fenzi = 0;
                $f_fenmu = 0;
                foreach ($stock_datas["stocks"] AS $k => $v){
                    ++$f_fenmu;
                    if(intval($v) > 5){
                        ++$f_fenzi;
                    }
                }
                $fulfillment = strval(round($f_fenzi / $f_fenmu * 10000) / 100) . '%';

                $fulfillmentList[$ean]['fulfillment'] = $fulfillment;
                $fulfillmentList[$ean]['erstelldatum'] = $erstelldatumStr;
                $fulfillmentList[$ean]['zeitraum'] = $zr . ' Tage';
                $fulfillmentList[$ean]['category'] = $stock_datas["category"];
                $fulfillmentList[$ean]['name'] = $stock_datas["name"];

            }

            /**
             * $fulfillmentList 写CSV文件
             */
            $csv_file = fopen($fulfillment_save_path . $csv_name, 'w');
            fputcsv($csv_file,array('EAN', 'Erstelldatum', 'Zeitraum', 'Fulfillment', 'Kategorie', 'Name'));
            foreach ($fulfillmentList AS $fK => $fV){
                fputcsv($csv_file,array($fK, $fV["erstelldatum"], $fV["zeitraum"], $fV["fulfillment"], $fV["category"], $fV["name"]));
            }
            fclose($csv_file);


        }

        $isSuccess = true;
        $datas["csv_name"] = $csv_name;
        $datas["fulfillmentList"] = $fulfillmentList;
        $msg = 'CSV File was successfully exported.';

    }else{
        $isSuccess = false;
        $msg = 'Missing parameters!';
    }

$results = array(
    'action' => $action_name,
    'isSuccess' => $isSuccess,
    'msg' => $msg,
    'datas' => $datas
);

echo json_encode($results);


}