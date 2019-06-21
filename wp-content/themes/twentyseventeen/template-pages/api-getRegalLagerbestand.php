<?php
/* Template Name: Idealhit Api CSV getRegalLagerbestand */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/4/25
 * Time: 16:24
 */

$root_path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
include_once ( GET_STYLESHEET_DIRECTORY() . '/Util/Helper.php');
use SoGood\Support\Util\Helper;
$helper = new Helper();

/**
 * 判断，如果没有登陆，那么直接跳转到 Login 页面
 */
if(!is_user_logged_in()){
    //重定向浏览器
    header("Location: /do-login/");
    //确保重定向后，后续代码不会被执行
    exit;
}else{

    $action_name = 'getRegalLagerbestand';
    $isSuccess = false;
    $msg = '';
    $datas = array();

    if(isset($_POST["rid"]) && isset($_POST["cat"])){

        $rid = $_POST["rid"];
        $cat = $_POST["cat"];

        /**
         * 获得文件路径
         */
        $datas["date"] = date('d.m.Y');
        $csvFileName = 'dump_element.' . date('Y.m.d') . '-06.00.01.csv';
        $csvFilePath = $root_path . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR. 'lagerbestand' . DIRECTORY_SEPARATOR;
        /**
         * 判断文件是否存在
         * 若不存在，就读前一天的数据
         */
        if(!file_exists($csvFilePath . $csvFileName)){
            $datas["date"] = date('d.m.Y', strtotime('-1 days'));
            $csvFileName = 'dump_element.' . date('Y.m.d', strtotime('-1 days')) . '-06.00.01.csv';
        }
        $csvFileFullPath = $csvFilePath . $csvFileName;

        /**
         * 如果能找到csv文件，就执行
         */
        if(file_exists($csvFileFullPath)){

            /**
             * 查询数据库，根据所有的货架，提取出所有需要的ean
             */
            global $wpdb;
            $db_table_name = "ihmapping_shelf_elements";
            $sql = "SELECT `shelf_id`, `ean_elm` FROM `".$db_table_name."` ";
            //这里还是读取全部记录吧，因为前台需要全部的货架id
            /*
            if($rid !== 'all'){
                $rid_arr = explode(",", $rid);
                $sql .= " WHERE `shelf_id` IN ( ";
                for($rid_arr_i = 0; $rid_arr_i < COUNT($rid_arr); ++$rid_arr_i){
                    if($rid_arr_i > 0){
                        $sql .= ",";
                    }
                    $sql .= "'" . $rid_arr[$rid_arr_i] . "'";
                }
                $sql .= " ) ";
            }else{
                $sql .= " WHERE 1=1 ";
            }
            */
            $sql .= " ORDER BY `shelf_id` ASC, `ean_elm` ASC";
            $resultsInDB = $wpdb->get_results($sql);
            $shelf_data = array();
            $shelf_id_array = array();
            if(COUNT($resultsInDB) > 0){
                foreach ($resultsInDB as $itm){

                    $isRecordNeed = false;
                    if($rid === 'all'){
                        $isRecordNeed = true;
                    }else if($helper->checkStrInString($itm->shelf_id, $rid)){
                        $isRecordNeed = true;
                    }else{
                        $isRecordNeed = false;
                    }

                    if($isRecordNeed){
                        $one_data = array(
                            'shelf_id' => $itm->shelf_id,
                            'ean_elm' => $itm->ean_elm,
                            'real_quantity' => 0,
                            'category' => '',
                            'name' => ''
                        );
                        $datas["shelf_data"][$itm->ean_elm] = $one_data;
                    }
                    array_push($shelf_id_array, $itm->shelf_id);
                }
                $isSuccess = true;
            }
            $datas["shelf_id_array"] = array_unique($shelf_id_array);


            /**
             * 寻找并打开csv文件
             * 排查哪些ean是需要的
             */
            $csvFile = fopen($csvFileFullPath,"r");
            $category_array = array();
            while(!feof($csvFile)){
                $aRowInCsv = fgetcsv($csvFile)[0];
                $aRowInCsvArr = explode("\t", $aRowInCsv);
                if($aRowInCsvArr[0] != 'id'){
                    $csv_ean = $aRowInCsvArr[1];
                    if(isset($datas["shelf_data"][$csv_ean])){
                        $csv_category = $aRowInCsvArr[2];

                        $isRecordNeed = false;
                        if($cat === 'all'){
                            $isRecordNeed = true;
                        }else if($cat === $csv_category){
                            $isRecordNeed = true;
                        }else{
                            $isRecordNeed = false;
                        }

                        if($isRecordNeed){
                            $csv_quantity = $aRowInCsvArr[3];
                            $csv_notsent = $aRowInCsvArr[4];
                            $csv_quantity_toshow = intval($csv_quantity) + intval($csv_notsent);
                            $csv_name = $aRowInCsvArr[5];
                            $datas["shelf_data"][$csv_ean]["real_quantity"] = $csv_quantity_toshow;
                            $datas["shelf_data"][$csv_ean]["category"] = $csv_category;
                            $datas["shelf_data"][$csv_ean]["name"] = $csv_name;
                        }else{
                            unset($datas["shelf_data"][$csv_ean]);
                        }

                        array_push($category_array, $csv_category);
                    }
                }
            }
            $datas["category_array"] = array_unique($category_array);


            /**
             * 创建可以下载的csv文件
             */
            $download_file_csv = (__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR. 'uploads' . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR . 'RegalLagerbestand.csv');
            if(file_exists($download_file_csv)){
                unlink($download_file_csv);
            }

            // 头部标题
            $csv_header = ['Regal-ID','EAN','Name','Kategorie','Wirkliche-Menge'];
            // 内容
            $csv_body = [];
            foreach ($datas["shelf_data"] AS $ean => $values){
                array_push($csv_body, [$values["shelf_id"], $ean, $values["name"], $values["category"], $values["real_quantity"]]);
            }

            // 打开文件资源，不存在则创建
            $fp = fopen($download_file_csv,'a');
            // 处理头部标题
            $header = implode(',', $csv_header) . PHP_EOL;
            // 处理内容
            $content = '';
            foreach ($csv_body as $k => $v) {
                $content .= implode(',', $v) . PHP_EOL;
            }
            // 拼接
            $csv = $header.$content;
            // 写入并关闭资源
            fwrite($fp, $csv);
            fclose($fp);



            $msg = 'Regal Lagerbestand wurde erfolgreich geladen.';
            $isSuccess = true;

        }else{

            $datas["date"] = date('d.m.Y');
            $msg = 'CSV Lagerbestand Datei kann nicht gefunden werden.';
            $isSuccess = false;

        }

    }else{

        $isSuccess = false;
        $msg = 'There is no parameter!';

    }

    $results = array(
        'action' => $action_name,
        'isSuccess' => $isSuccess,
        'msg' => $msg,
        'datas' => $datas
    );

    echo json_encode($results);


}