<?php
/* Template Name: Idealhit Api Export CSV StockQuantity */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/4/25
 * Time: 16:24
 */

include_once ( GET_STYLESHEET_DIRECTORY() . DIRECTORY_SEPARATOR .'Util' . DIRECTORY_SEPARATOR . 'Helper.php');
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

    $action_name = 'Export CSV StockQuantity';
    $isSuccess = false;
    $msg = '';
    $csv_name = '';

    $product_stock_dir = GET_STYLESHEET_DIRECTORY() .
        DIRECTORY_SEPARATOR . '..' .
        DIRECTORY_SEPARATOR . '..' .
        DIRECTORY_SEPARATOR . 'uploads' .
        DIRECTORY_SEPARATOR . 'data' .
        DIRECTORY_SEPARATOR . 'lagerbestand'.
        DIRECTORY_SEPARATOR;
    $csv_save_path = GET_STYLESHEET_DIRECTORY() .
        DIRECTORY_SEPARATOR . '..' .
        DIRECTORY_SEPARATOR . '..' .
        DIRECTORY_SEPARATOR . 'uploads' .
        DIRECTORY_SEPARATOR . 'export'.
        DIRECTORY_SEPARATOR;
    $all_products_stock_datas = array();

    $fulfillmentList = array();

    // 先读取数据，再整理分析
    if(isset($_POST["zr"])){

        /**
         * 导出csv之前需要整理的数据
         */
        $datas = array();
        $datas['dates'] = array();
        $datas['eans'] = array();

        $zr = $_POST["zr"];
        $erstelldatum = time() + ( $_settings_data['server-info']['gmt_offset'] * 3600 );
        $csv_name = 'StockQuantity_' . $zr . 'days_' . date("Y.m.d_H.i.s", $erstelldatum) . '.csv';

        $product_stock_history_csv_paths = $helper->getFilePaths($product_stock_dir, false);
        foreach ($product_stock_history_csv_paths as $val){

            // 根据CSV文件的名字，获取日期信息
            $linShiArr = explode("dump_element.", $val);
            $linShiArr1 = explode("-06.", $linShiArr[1]);
            $datum = $linShiArr1[0];

            array_push($datas['dates'], $datum);

            /**
             * 读取一个库存csv文件内的所有EAN的库存信息
             */
            if($helper->isDateActived($datum, $zr)){
                $file = fopen($val,"r");
                while(! feof($file))
                {

                    $aRowInCsv = fgetcsv($file)[0];
                    $aRowInCsvArr = explode("\t", $aRowInCsv);
                    if($aRowInCsvArr[0] != 'id'){
                        $ean = trim($aRowInCsvArr[1]);
                        $quantity = intval($aRowInCsvArr[3]);
                        $datas['eans'][$ean][$datum] = $quantity < 0 ? 0 : $quantity;
                    }

                }
                fclose($file);
            }

        }

        /**
         * 根据 $datas 写 CSV 文件
         */
        $csv_file = fopen($csv_save_path . $csv_name, 'w');
        fputcsv($csv_file, array_merge(array('EAN'), $datas['dates']));
        foreach ($datas['eans'] AS $key => $val){
            fputcsv($csv_file, array_merge(array($key), $val));
        }
        fclose($csv_file);

        $isSuccess = true;
        $msg = 'CSV File was successfully exported.';

    }else{
        $isSuccess = false;
        $msg = 'Missing parameters!';
    }

    $results = array(
        'action' => $action_name,
        'isSuccess' => $isSuccess,
        'msg' => $msg,
        'fileName' => $csv_name
    );

    echo json_encode($results);


}