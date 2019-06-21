<?php
/* Template Name: Idealhit Api UpdateMappingShelfAndElementList */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/4/25
 * Time: 16:24
 */

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
    $db_table_name = "ihmapping_shelf_elements";

    $action_name = 'UpdateMappingShelfAndElementList';
    $isSuccess = false;
    $msg = '';
    $data = array();


    /**
     * 参数说明
     * $type
     * 1: Element EAN 是一个，Ersatzteil EAN 是多个
     * 2: Ersatzteil EAN 是一个，Element EAN 是多个
     */
    $type = -1;

    if(isset($_POST["type"]) && isset($_POST["shelfid"]) && isset($_POST["elm"])){

        $type = intval($_POST["type"]);

        $dataRecords = array();
        switch ($type){
            case 1:
                /**
                 * Element EAN 是一个，Ersatzteil EAN 是多个
                 */
                $shelfid = $_POST["shelfid"];
                $elm = explode(',', $_POST["elm"]);
                foreach ($elm as $val){
                    array_push($dataRecords, array(
                        'shelf_id' => $shelfid,
                        'ean_elm' => $val,
                    ));
                }
                break;
            default:
                //
        }


        /**
         * 删除相应的旧数据
         */
        $sql_del = "DELETE FROM " . $db_table_name . " WHERE";
        $ean_del = '';
        switch ($type){
            case 1:
                $sql_del .= " `shelf_id` = %s;";
                $ean_del = $_POST["shelfid"];
                break;
            case 2:
            default:
                $sql_del .= " 1 = 2;";
                $ean_del = '';
        }
        $wpdb->query($wpdb->prepare($sql_del, $ean_del));

        /**
         * 插入 $dataRecords 里面的数据到数据库中
         */
        $dataRecordsQty = COUNT($dataRecords);
        if($dataRecordsQty > 0){
            $sql_insert = "INSERT INTO `" . $db_table_name . "`(`shelf_id`, `ean_elm`) VALUES ";
            for($i = 0; $i < $dataRecordsQty; $i++){
                $sql_insert .= "(%s,%s)";
                if($i < $dataRecordsQty - 1){
                    $sql_insert .= ",";
                }else{
                    $sql_insert .= ";";
                }
            }
            $dataPrepared = array();
            for($i = 0; $i < $dataRecordsQty; $i++){
                array_push($dataPrepared, $dataRecords[$i]["shelf_id"]);
                array_push($dataPrepared, $dataRecords[$i]["ean_elm"]);
            }
            $wpdb->query($wpdb->prepare($sql_insert, $dataPrepared));

            $isSuccess = true;
            switch ($type){
                case 1:
                    $msg = 'Mapping des Regals [' . $_POST["shelfid"] . '] wurde erfolgreich aktualisiert.';
                    break;
                default:
                    //
            }

        }else{
            $isSuccess = false;
            $msg = 'No data need be inserted into database!';
        }

    }else{
        $isSuccess = false;
        $msg = 'Parameters not found!';
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