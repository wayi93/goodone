<?php
/* Template Name: Idealhit Api GetDeliveryNotesList */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/10/12
 * Time: 12:45
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

    $helper = new Helper();

    $results_quantity_max = 7;
    $file_dir = dirname(dirname(dirname(dirname(__FILE__)))) . '/downloads/liferscheine/';

    $action_name = 'GetDeliveryNotesList';
    $isSuccess = false;
    $msg = '';
    $data = array();

    error_log("api-getDeliveryNotesList dupa");
    /**
     * 拿到文件夹内部，所有的文件名
     */
    $pdf_names = $helper->getFileNames($file_dir, false);

    /**
     * 创建所有的文件名
     */
    $dt = time() + ( $_settings_data['server-info']['gmt_offset'] * 3600 );
    $filenames_eki = array();
    $filenames_maimai = array();
    $filenames_sogood = array();
    for($i = 0; $i < $results_quantity_max; ++$i){
        //$date = date("Y-m-d H:i:s", strtotime("-" . (($i * 24) + 22) . " hours"));
        $date_en = date("Y-m-d", strtotime("-" . ((($i - 1) * 24) + 22) . " hours"));
        $date_de = date("d.m.Y", strtotime("-" . ((($i - 1) * 24) + 22) . " hours"));

        foreach ($pdf_names AS $pn_val){
            if($helper->checkStrInString($date_en, $pn_val)){
                /**
                 * 查询数据库，看他是否被标记为已打印了
                 * $pn_val
                 */
                global $wpdb;
                $already_printed = 'no';
                $sql_check = 'SELECT `already_printed` FROM `ihattach_file_print_logs` WHERE `name` = %s limit 1;';
                $check_results_db = $wpdb->get_results($wpdb->prepare($sql_check, $pn_val));
                if(COUNT($check_results_db) > 0){
                    $already_printed_db = intval($check_results_db[0]->already_printed);
                    if($already_printed_db == 1){
                        $already_printed = 'yes';
                    }
                }

                $show_name = $helper->getDeliveryNoteShowName($pn_val);
                $create_at_int = $helper->getCreateAtTimeInt($pn_val);;
                error_log("--> api-getDeliveryNotesList dupa ".$pn_val." ".$show_name);
                if($helper->checkStrInString('eki', $pn_val)){
                    array_push($filenames_eki, array(
                        'name' => $pn_val,
                        'show_name' => $show_name,
                        'pay_date' => $date_de,
                        'create_at_int' => $create_at_int,
                        'already_printed' => $already_printed
                    ));
                }else if($helper->checkStrInString('maimai', $pn_val)){
                    array_push($filenames_maimai, array(
                        'name' => $pn_val,
                        'show_name' => $show_name,
                        'pay_date' => $date_de,
                        'create_at_int' => $create_at_int,
                        'already_printed' => $already_printed
                    ));
                }else if($helper->checkStrInString('sogood', $pn_val)){
                    array_push($filenames_sogood, array(
                        'name' => $pn_val,
                        'show_name' => $show_name,
                        'pay_date' => $date_de,
                        'create_at_int' => $create_at_int,
                        'already_printed' => $already_printed
                    ));
                }
            }

        }

    }
    usort($filenames_eki, function($a, $b){
        $a_cai = intval($a["create_at_int"]);
        $b_cai = intval($b["create_at_int"]);
        if ($a_cai == $b_cai) return 0;
        return ($a_cai > $b_cai)?-1:1;
    });
    $data["filenames_eki"] = $filenames_eki;
    usort($filenames_maimai, function($a, $b){
        $a_cai = intval($a["create_at_int"]);
        $b_cai = intval($b["create_at_int"]);
        if ($a_cai == $b_cai) return 0;
        return ($a_cai > $b_cai)?-1:1;
    });
    $data["filenames_maimai"] = $filenames_maimai;
    usort($filenames_sogood, function($a, $b){
        $a_cai = intval($a["create_at_int"]);
        $b_cai = intval($b["create_at_int"]);
        if ($a_cai == $b_cai) return 0;
        return ($a_cai > $b_cai)?-1:1;
    });
    $data["filenames_sogood"] = $filenames_sogood;

    if(COUNT($data["filenames_eki"]) > 0 || COUNT($data["filenames_maimai"]) > 0 || COUNT($data["filenames_sogood"]) > 0){
        $isSuccess = true;
        $msg = 'Lieferscheine wurden erfolgreich gelistet.';
    }else{
        $isSuccess = false;
        $msg = 'Lieferschein nicht gefunden.';
    }

    $results = array(
        'action' => $action_name,
        'isSuccess' => $isSuccess,
        'msg' => $msg,
        'data_quantity' => COUNT($data),
        'data' => $data
    );

    echo json_encode($results);


}
