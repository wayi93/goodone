<?php
/* Template Name: Idealhit Api UpdateMappingElementAndErsatzteilList */
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
    $db_table_name = "ihmapping_elements_ersatzteile";

    $action_name = 'UpdateMappingElementAndErsatzteilList';
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

    if(isset($_POST["type"]) && isset($_POST["elm"]) && isset($_POST["est"])){

        $type = intval($_POST["type"]);

        $dataRecords = array();
        switch ($type){
            case 1:
                /**
                 * Element EAN 是一个，Ersatzteil EAN 是多个
                 */
                $elm = $_POST["elm"];
                $est = explode(',', $_POST["est"]);
                foreach ($est as $val){
                    array_push($dataRecords, array(
                        'ean_elm' => $elm,
                        'ean_est' => $val,
                    ));
                }
                break;
            case 2:
                /**
                 * Ersatzteil EAN 是一个，Element EAN 是多个
                 */
                $elm = explode(',', $_POST["elm"]);
                $est = $_POST["est"];
                foreach ($elm as $val){
                    array_push($dataRecords, array(
                        'ean_elm' => $val,
                        'ean_est' => $est,
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
                /**
                 * Element EAN 是一个，Ersatzteil EAN 是多个
                 * 删除所有 ean_elm = $elm 的记录
                 */
                $sql_del .= " `ean_elm` = %s;";
                $ean_del = $_POST["elm"];
                break;
            case 2:
                /**
                 * Ersatzteil EAN 是一个，Element EAN 是多个
                 * 删除所有 ean_est = $est 的记录
                 */
                $sql_del .= " `ean_est` = %s;";
                $ean_del = $_POST["est"];
                break;
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
            $sql_insert = "INSERT INTO `ihmapping_elements_ersatzteile`(`ean_elm`, `ean_est`) VALUES ";
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
                array_push($dataPrepared, $dataRecords[$i]["ean_elm"]);
                array_push($dataPrepared, $dataRecords[$i]["ean_est"]);
            }
            $wpdb->query($wpdb->prepare($sql_insert, $dataPrepared));

            $isSuccess = true;
            switch ($type){
                case 1:
                    /**
                     * Element EAN 是一个，Ersatzteil EAN 是多个
                     */
                    $msg = 'Mapping des Elements [' . $_POST["elm"] . '] wurde erfolgreich aktualisiert.';
                    break;
                case 2:
                    /**
                     * Ersatzteil EAN 是一个，Element EAN 是多个
                     */
                    $msg = 'Mapping des Ersatzteils [' . $_POST["est"] .  '] wurde erfolgreich aktualisiert.';
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