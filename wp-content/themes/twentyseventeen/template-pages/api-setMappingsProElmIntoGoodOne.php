<?php
/* Template Name: Idealhit Api SetMappingsProElmIntoGoodOne */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/4/25
 * Time: 16:24
 */

/**
 * Test
 * https://goodone.maimai24.de/api/setmappingsproelmintogoodone/?token=3eDFppVBxSXjhZd5vuuS2hjBsz3jxUQg
 */

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

    $action_name = 'SetMappingsProElmIntoGoodOne';
    $isSuccess = false;
    $msg = '';
    $data = array();

    error_log("-----> DUPA: api-setMappingsProElmIntoGoodOne");

    if(isset($_GET["token"]) && $_GET["token"] === '3eDFppVBxSXjhZd5vuuS2hjBsz3jxUQg'){

        $url = $_settings_data["urls"]["Api_Url_Get_ProElm_Relations"];
        $authorization = base64_encode($_settings_data["server-info"]["Entelliship_UserID"] . ":" . $_settings_data["server-info"]["Entelliship_Password"]);
        $postFields = array();
        $CURLOPT_HTTPHEADER_LIST = array(
            'Authorization: Basic ' . $authorization,
            'Accept: application/xml'
        );

        $helper = new Helper();
        $products = $helper->requestHttpApi($url, $postFields, $CURLOPT_HTTPHEADER_LIST);

        $results = json_decode($helper->xmlToJson($products), true);

        if($results['name'] === 'successful'){

            global $wpdb;

            $db_table_name = "ihmapping_products_elements";

            $ng_relations = $results['ng_relations'];

            $sql = "INSERT INTO `" . $db_table_name . "`(`ean_pro`, `ean_elm`) VALUES ";

            $counter = 0;
            foreach ($ng_relations AS $ngr)
            {
                $productEAN = $ngr['productEAN'];
                $elementEAN = $ngr['elementEAN'];

                if($counter > 0)
                {
                    $sql .= ", ";
                }

                $sql .= "('" . $productEAN . "', '" . $elementEAN . "')";

                $counter++;

            }

            /**
             * 开始事务
             */
            $wpdb->query('START TRANSACTION');
            $result_delete = $wpdb->query("truncate table " . $db_table_name . ";");
            $result_insert = $wpdb->query($sql);

            if($result_delete && $result_insert){
                $wpdb->query('COMMIT');
            }else{
                $wpdb->query('ROLLBACK');
            }

            $isSuccess = true;
            $msg = 'OK';

        }else{

            $isSuccess = false;
            $msg = 'E2 API failure.';

        }

    }else{

        $isSuccess = false;
        $msg = 'There is no parameter.';

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