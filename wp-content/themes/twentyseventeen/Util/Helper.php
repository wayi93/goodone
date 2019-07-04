<?php
/**
 * Created by PhpStorm.
 * User: YingWang
 * Date: 2017/7/24
 * Time: 16:05
 */

namespace SoGood\Support\Util;


class Helper
{

    /**
     * @var
     */
    protected $settings_data;

    public function __construct
    (
        $settings_data = array()
    )
    {
        $this->settings_data = parse_ini_file(WP_CONTENT_DIR . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "sogood_settings.ini",true);
    }

    public function escapeHtmlValue($value)
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    public function removeComma($value)
    {
        return str_replace(',','', $value);
    }

    function get4250ean($ean)
    {
        return str_replace('42512429','42507553', $ean);
    }

    function get4251ean($ean)
    {
        return str_replace('42507553','42512429', $ean);
    }

    function getCountryDataByID($id)
    {
        $data = array(
            'ID' => $id,
            'Kennzeichen' => $id,
            'Land' => '',
        );

        switch($id)
        {
            case 'DE':
                $data['Land'] = 'Deutschland';
                break;
            case 'FR':
                $data['Land'] = 'Frankreich';
                break;
            case 'AT':
                $data['Land'] = 'Österreich';
                break;
            case 'IT':
                $data['Land'] = 'Italien';
                break;
            case 'NL':
                $data['Land'] = 'Niederlande';
                break;
            case 'BE':
                $data['Land'] = 'Belgien';
                break;
            case 'LU':
                $data['Land'] = 'Luxemburg';
                break;
            default:
                //
        }

        return $data;
    }

    public function getMonthTitle($id, $isAbbr = true)
    {
        $title = '';
        switch(intval($id)){
            case 1:
                if($isAbbr){
                    $title = 'Jan';
                }else{
                    $title = 'Januar';
                }
                break;
            case 2:
                if($isAbbr){
                    $title = 'Feb';
                }else{
                    $title = 'Februar';
                }
                break;
            case 3:
                if($isAbbr){
                    $title = 'Mär';
                }else{
                    $title = 'März';
                }
                break;
            case 4:
                if($isAbbr){
                    $title = 'Apr';
                }else{
                    $title = 'April';
                }
                break;
            case 5:
                if($isAbbr){
                    $title = 'Mai';
                }else{
                    $title = 'Mai';
                }
                break;
            case 6:
                if($isAbbr){
                    $title = 'Jun';
                }else{
                    $title = 'Juni';
                }
                break;
            case 7:
                if($isAbbr){
                    $title = 'Jul';
                }else{
                    $title = 'Juli';
                }
                break;
            case 8:
                if($isAbbr){
                    $title = 'Aug';
                }else{
                    $title = 'August';
                }
                break;
            case 9:
                if($isAbbr){
                    $title = 'Sep';
                }else{
                    $title = 'September';
                }
                break;
            case 10:
                if($isAbbr){
                    $title = 'Okt';
                }else{
                    $title = 'Oktober';
                }
                break;
            case 11:
                if($isAbbr){
                    $title = 'Nov';
                }else{
                    $title = 'November';
                }
                break;
            case 12:
                if($isAbbr){
                    $title = 'Dez';
                }else{
                    $title = 'Dezember';
                }
                break;
            default:
                //
        }
        return $title;
    }

    public function requestHttpApi($url, $postFields = null, $CURLOPT_HTTPHEADER_LIST)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $browser = array(
            'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50',
            'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)',
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)',
            'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:2.0.1) Gecko/20100101 Firefox/4.0.1',
            'Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1',
            'Opera/9.80 (Macintosh; Intel Mac OS X 10.6.8; U; en) Presto/2.8.131 Version/11.11',
            'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.8.131 Version/11.11',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11',
            'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.36',
        );
        $default_browser = $browser[array_rand($browser)];
        curl_setopt($ch, CURLOPT_USERAGENT, $default_browser);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        if (is_array($CURLOPT_HTTPHEADER_LIST) && 0 < count($CURLOPT_HTTPHEADER_LIST)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $CURLOPT_HTTPHEADER_LIST);
        }
        if (is_array($postFields) && 0 < count($postFields)) {
            $postBodyString = "";
            $postMultipart = false;
            foreach ($postFields as $k => $v) {
                if ("@" != substr($v, 0, 1)) {
                    $postBodyString .= "$k=" . urlencode($v) . "&";
                } else {
                    $postMultipart = true;
                }
            }
            unset($k, $v);
            curl_setopt($ch, CURLOPT_POST, true);
            if ($postMultipart) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
            }
        }

        $reponse = curl_exec($ch);

        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpStatusCode != 200) {
            $reponse = '<?xml version="1.0" encoding="utf-8" standalone="yes"?><result><name>failure</name><httpStatusCode>'.$httpStatusCode.'</httpStatusCode></result>';
        }
        curl_close($ch);
        return $reponse;

    }

    public function requestHttpApi_Json($url, $postFields = null, $CURLOPT_HTTPHEADER_LIST)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $browser = array(
            'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50',
            'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)',
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)',
            'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:2.0.1) Gecko/20100101 Firefox/4.0.1',
            'Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1',
            'Opera/9.80 (Macintosh; Intel Mac OS X 10.6.8; U; en) Presto/2.8.131 Version/11.11',
            'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.8.131 Version/11.11',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11',
            'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.36',
        );
        $default_browser = $browser[array_rand($browser)];
        curl_setopt($ch, CURLOPT_USERAGENT, $default_browser);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        if (is_array($CURLOPT_HTTPHEADER_LIST) && 0 < count($CURLOPT_HTTPHEADER_LIST)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $CURLOPT_HTTPHEADER_LIST);
        }
        if (is_array($postFields) && 0 < count($postFields)) {
            $postBodyString = "{";
            $isFirst = true;
            foreach ($postFields as $k => $v) {
                if(!$isFirst){
                    $postBodyString .= ',';
                }
                $postBodyString = $postBodyString . '"' . $k . '":"' . $v . '"';
                $isFirst = false;
            }
            $postBodyString .= "}";

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postBodyString);
        }

        $reponse = curl_exec($ch);

        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpStatusCode != 200) {
            $reponse = '<?xml version="1.0" encoding="utf-8" standalone="yes"?><result><name>failure</name><httpStatusCode>'.$httpStatusCode.'</httpStatusCode></result>';
        }
        curl_close($ch);
        return $reponse;

    }

    public function xml_parser($str)
    {
        $xml_parser = xml_parser_create();
        if (!xml_parse($xml_parser, $str, true)) {
            xml_parser_free($xml_parser);
            return false;
        } else {
            return (json_decode(json_encode(simplexml_load_string($str)), true));
        }
    }

    public function xmlToJson($xmlStr)
    {
        $xmlObj = simplexml_load_string($xmlStr);
        $json = json_encode($xmlObj);
        return $json;
    }

    /**
     * 判断字符串$str1是否包含$str2
     * @param $str1 "ABCDEFG"
     * @param $str2 "C"
     * @return bool true
     */
    public function checkContainStr($str1, $str2)
    {
        $needle = $str2; //判断是否包含a这个字符
        $tmparray = explode($needle, $str1);
        if (count($tmparray) > 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 遍历并拿到目录下的全部文件
     * 用dir返回对象
     * @param $dir
     * @param $supportTree //是否读取树形结构，是否读取下一级别文件夹里的内容
     * @return array
     */
    public function getFilePaths($dir, $supportTree){

        $res = array();
        $d = dir($dir);
        while ($fileName = $d->read()) {

            if($supportTree){

                // 下一级文件夹里面的内容也读取
                // ...

            }else{

                // 遇到下一级文件夹就跳过
                if ((is_dir($dir . $fileName)) OR ($fileName == ".") OR ($fileName == "..")) {
                    // 遇到文件目录 都跳过
                } else {
                    array_push($res, $dir . $fileName);
                }

            }

        }
        return $res;

    }

    /**
     * 遍历并拿到目录下的全部文件的文件名
     * 用dir返回对象
     * @param $dir
     * @param $supportTree //是否读取树形结构，是否读取下一级别文件夹里的内容
     * @return array
     */
    public function getFileNames($dir, $supportTree){

        $res = array();
        $d = dir($dir);
        while ($fileName = $d->read()) {

            if($supportTree){

                // 下一级文件夹里面的内容也读取
                // ...

            }else{

                // 遇到下一级文件夹就跳过
                if ((is_dir($dir . $fileName)) OR ($fileName == ".") OR ($fileName == "..")) {
                    // 遇到文件目录 都跳过
                } else {
                    array_push($res, $fileName);
                }

            }

        }
        return $res;

    }

    /**
     * 根据老高发来的CSV文件，读出内容
     * @param $dir
     * @param $zr
     * @return array
     */
    public function getProductStockHistoryCSVContent($dir, $zr){
        $res = array();

        // 根据CSV文件的名字，获取日期信息
        //echo $dir;
        $linShiArr = explode("dump_element.", $dir);
        $linShiArr1 = explode("-06.", $linShiArr[1]);
        $datum = $linShiArr1[0];

        //if($this->isDateActived($datum, $this->settings_data["function-parameters"]["isDateActived_howlong"])){
        if($this->isDateActived($datum, $zr)){
            $file = fopen($dir,"r");
            while(! feof($file))
            {

                $aRowInCsv = fgetcsv($file)[0];
                $aRowInCsvArr = explode("\t", $aRowInCsv);
                if($aRowInCsvArr[0] != 'id'){

                    /**
                     * 清除ean左右的空格
                     */
                    $aRowInCsvArr[1] = trim($aRowInCsvArr[1]);

                    // Ying: 日期永远在 $aRowInCsvArr[7] 新加的字段都在后面
                    //array_push($aRowInCsvArr, $datum);
                    array_splice($aRowInCsvArr,7,0,array($datum));
                    $res[$aRowInCsvArr[1]] = $aRowInCsvArr;
                }

            }
            fclose($file);
        }

        return $res;
    }

    /**
     * 判断一个日期，是否需要做处理
     * @param $d (str) "2017.01.09"
     * @param $long (int) 表示距离今天，多少天是有效的
     * @return mixed
     */
    public function isDateActived($d, $long){
        $res = false;
        $begin_date = strtotime(str_replace(".","-",$d));
        $end_date = strtotime(date("Y-m-d"));
        $days = round(($end_date - $begin_date) / 3600 / 24);
        if($days > $long){
            $res = false;
        }else{
            $res = true;
        }
        return $res;
    }

    /**
     * 根据EAN, 从CSV的文件目录中, 得到这款产品(所有天的)的库存历史记录
     * @param $ean
     * @param $dir //csv存储的地方
     * @param $zr  //使用多少天的数据 Zeitraum
     * @return array
     */
    public function getProductStockHistoryByEAN($ean, $dir, $zr){
        $res = array();

        $product_stock_history_csv_paths = $this->getFilePaths($dir, false);
        foreach ($product_stock_history_csv_paths as $val){
            $c = $this->getProductStockHistoryCSVContent($val, $zr);
            if($c != null && COUNT($c) > 0){
                array_push($res, $c[$ean]);
            }
        }

        return $res;
    }

    /**
     * 检查用户组是否有权限打开这个页面
     * @param $userGroup
     * @param $current_url
     * @return bool
     */
    public function canThisUserGroupAccess($userGroup, $current_url){
        $res = false;
        $zas = $this->settings_data["zugriffsberechtigung-auf-seiten"]["User_Group_".$userGroup];
        $zasArr = explode(",", $zas);
        $pageCustomID = $this->getPageCustomIDByLink($current_url);
        if(in_array($pageCustomID, $zasArr)){
            $res = true;
        }
        return $res;
    }

    /**
     * 检查用户组是否有权限使用这个功能
     * @param $userGroup
     * @param $funcId
     * @return bool
     */
    public function canThisUserGroupUse($userGroup, $funcId){
        $res = false;
        $zas = $this->settings_data["zugriffsberechtigung-auf-funktionen"]["User_Group_".$userGroup];
        $zasArr = explode(",", $zas);
        if(in_array($funcId, $zasArr)){
            $res = true;
        }
        return $res;
    }

    /**
     * 根据用户组得到可以打折的尺度
     * @param $userGroup
     * @return array
     */
    public function getRabattStufenByUserGroup($userGroup){
        $res = array();
        $rs = $this->settings_data["rabattstufen"]["User_Group_".$userGroup];
        $rsArr = explode(",", $rs);
        if(COUNT($rsArr) > 0){
            for($i=0; $i<COUNT($rsArr); ++$i){
                $rsItmArr = explode("-", $rsArr[$i]);
                $res[$rsItmArr[0]] = $rsItmArr[1];
            }
        }
        return $res;
    }

    public function getPageCustomIDByLink($u){
        $pageLinks = array(
            '0' => '/dashboard/',
            '1' => '/product-overview/',
            '2' => '/product-list/',
            '3' => '/order-overview/',
            '4' => '/order-create/',
            '5' => '/my-account/',
            '6' => '/order-list/',
            '7' => '/order-list-onhold/',
            '8' => '/quote-create/',
            '9' => '/quote-list/',
            '10' => '/order-list-notpaid/',
            '11' => '/data-analytics/',
            '12' => '/data-export/',
            '13' => '/delivery-notes-list/',
            '14' => '/data-umsatzstatistik/',
            '15' => '/datenmapping-bearbeiten/',
            '16' => '/datenmapping-list-elm-est/',
            '17' => '/datenmapping-list-pro-elm/',
            '18' => '/ersatzteil-create/',
            '19' => '/ersatzteil-grund-edit/',
            '20' => '/datenmapping-lager-bearbeiten/',
            '21' => '/datenmapping-list-regal-elm/',
            '22' => '/lagerbestand-real/',
            '23' => '/ersatzteil-order-list/',
            '24' => '/ersatzteil-order-onhold/',
            '25' => '/data-analytics/ersatzteil-reason-details/',
        );
        $res = 0;

        foreach ($pageLinks as $key => $value){
            if($this->checkStrInString($value, $u)){
                $res = $key;
                break;
            }
        }

        return $res;
    }

    /**
     * 判断字符串$str是否在$string里面 (即$string是否包含$str)
     * @param $str
     * @param $string
     * @return bool
     */
    public function checkStrInString($str, $string){
        $tmparray = explode($str, $string);
        if(count($tmparray)>1){
            return true;
        } else{
            return false;
        }
    }

    /**
     * @param $p
     * @param $currency
     * @return int|string
     * 整理货币的格式
     */
    public function formatPrice($p, $currency){
        $res = 0;
        switch ($currency){
            case 'EUR':
                $res = number_format($p,2,",",".");
                break;
            case 'USD':
                $res = number_format($p,2,".",",");
                break;
            case 'FLOAT':
                $res = number_format($p,2,".","");
                break;
            default:
                //
        }
        return $res;
    }

    /**
     * 返回Mediate, 目前使用用户组，未来可能加入用户名
     * @return mixed
     */
    public function getMediate(){
        global $current_user;
        get_current_user();
        $us = $current_user->roles[0] . '.goodone';
        return $us;
    }

    /**
     * 根据数据库字段名，获取占位符
     * @param $fieldName
     * @return string
     */
    public function getZhanWeiFuByFieldName($fieldName){
        $res = "%s";
        switch ($fieldName){
            case "meta_id":
                $res = "%d";
                break;
            case "update_at":
                $res = "%f";
                break;
            case "update_by":
                $res = "%d";
                break;
            case "order_id_ab":
                $res = "%s";
                break;
            case "status":
                $res = "%s";
                break;
            default:
                $res = "%s";
        }
        return $res;
    }

    /**
     * 判断页面处理什么样的对象
     * 例如，使用同一个Template的页面，是处理 order 还是 quote
     * @return string
     */
    public function getPageDealWith(){

        $pT = "order";

        /**
         * 获得当前页面地址url
         */
        $current_url = home_url(add_query_arg(array()));

        if($this->checkContainStr($current_url, '/order-create')){
            $pT = "order";
        }else if($this->checkContainStr($current_url, '/order-list')){
            $pT = "order";
        }else if($this->checkContainStr($current_url, '/order-list-onhold')){
            $pT = "order";
        }else if($this->checkContainStr($current_url, '/quote-create')){
            $pT = "quote";
        }else if($this->checkContainStr($current_url, '/quote-list')){
            $pT = "quote";
        }else if($this->checkContainStr($current_url, '/ersatzteil-create')){
            $pT = "ersatzteil";
        }else if($this->checkContainStr($current_url, '/ersatzteil-order-list')){
            $pT = "ersatzteil";
        }else if($this->checkContainStr($current_url, '/ersatzteil-order-onhold')){
            $pT = "ersatzteil";
        }

        return $pT;

    }

    /**
     * 取括号里面的字符
     * ！！！目前只支持一组括号。
     * @param $str
     * @return mixed
     */
    public function getTextInKuoHao($str)
    {
        $result = array();
        preg_match_all("/(?:\()(.*)(?:\))/i",$str, $result);
        return $result[1][0];
    }

    /**
     * 计算订单附件的数量
     * @param $orderId
     * @return int
     */
    public function getFileQuantity($orderId){
        $dir = __DIR__ . '/../../../../wp-content/uploads/order_attachment/' . $orderId . '/';
        if(is_dir($dir)){
            $handle = opendir($dir);
            $i = 0;
            while(false !== $file=(readdir($handle))){
                if($file !== '.' && $file != '..' && $file != 'thumbnail')
                {
                    $i++;
                }
            }
            closedir($handle);
            return $i;
        }else{
            return 0;
        }
    }


    /*********************************************************************
    函数名称:encrypt
    函数作用:加密解密字符串
    使用方法:
    加密     :encrypt('str','E','nowamagic');
    解密     :encrypt('被加密过的字符串','D','nowamagic');
    参数说明:
    $string   :需要加密解密的字符串
    $operation:判断是加密还是解密:E:加密   D:解密
    $key      :加密的钥匙(密匙);
     *********************************************************************/
    public function encrypt($string, $operation, $key='')
    {
        $key=md5($key);
        $key_length=strlen($key);
        $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
        $string_length=strlen($string);
        $rndkey=$box=array();
        $result='';
        for($i=0;$i<=255;$i++)
        {
            $rndkey[$i]=ord($key[$i%$key_length]);
            $box[$i]=$i;
        }
        for($j=$i=0;$i<256;$i++)
        {
            $j=($j+$box[$i]+$rndkey[$i])%256;
            $tmp=$box[$i];
            $box[$i]=$box[$j];
            $box[$j]=$tmp;
        }
        for($a=$j=$i=0;$i<$string_length;$i++)
        {
            $a=($a+1)%256;
            $j=($j+$box[$a])%256;
            $tmp=$box[$a];
            $box[$a]=$box[$j];
            $box[$j]=$tmp;
            $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
        }
        if($operation=='D')
        {
            if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8))
            {
                return substr($result,8);
            }
            else
            {
                return'';
            }
        }
        else
        {
            return str_replace('=','',base64_encode($result));
        }
    }

    /**
     * 判断字符串是否在数组里面
     * @param $str
     * @param $arr
     * @return bool
     */
    public function isValueInArray($str, $arr){
        $res = false;
        for($i = 0; $i < COUNT($arr); ++$i){
            if($arr[$i] == $str){
                $res = true;
            }
        }
        return $res;
    }

    /**
     * 创建简单的适合手机的消息页面
     * @param $msg
     * @return string
     */
    public function getMessagePageHtml($msg){
        $htmlTxt = '<!DOCTYPE html>
                <html>
                <head>
                <title>Nachricht - Mai & Mai GmbH</title>
                <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <link href="/wp-includes/css/massage-page.css" rel="stylesheet" type="text/css" />
                <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,300,600,700" rel="stylesheet" type="text/css" />
                </head>
                <body>
                <div id="logo-wrap">
                <div>
                <img src="http://goodone.maimai24.de/wp-content/uploads/images/logo_maimai_211_115.png" />
                </div>
                <div style="padding: 10px 0;">
                Ihr Sanitärexperte
                </div>
                <div>
                <div class="login">	
                <div class="ribbon-wrapper h2 ribbon-red">
                <div class="ribbon-front">
                <h2>Nachricht für Sie</h2>
                </div>
                <div class="ribbon-edge-topleft2"></div>
                <div class="ribbon-edge-bottomleft"></div>
                </div>
                <div id="msg-wrap">
                '.$msg.'
                </div>
                <div id="submit-btn" onclick="location.href=\'https://www.sogood.de\'">
                zu unserem Onlineshop
                </div>
                </div>
                </body>
                </html>';
        return $htmlTxt;
    }

    /**
     * 为图标准备数据的方法
     */
    /**
     * 根据老高发来的CSV文件，读出内容
     * @param $dir
     * @param $lang
     * @param $ean
     * @return array
     */
    public function getCSVVerkaufMengeContent($dir, $lang, $ean){
        $res = array();

        // 根据CSV文件的名字，获取日期信息
        //echo $dir;
        $linShiArr = explode("dump_element.", $dir);
        $linShiArr1 = explode("-06.", $linShiArr[1]);
        $datum = $linShiArr1[0];

        if($this->isDateActived($datum, $lang)){
            $file = fopen($dir,"r");
            while(! feof($file))
            {

                $aRowInCsv = fgetcsv($file)[0];
                $aRowInCsvArr = explode("\t", $aRowInCsv);

                /**
                 * 清除ean左右的空格
                 */
                $aRowInCsvArr[1] = trim($aRowInCsvArr[1]);

                if($aRowInCsvArr[0] != 'id' && $aRowInCsvArr[1] == $ean){

                    $soldQuantityData = '0|0|0|0|0';
                    if(COUNT($aRowInCsvArr) > 7 && $this->checkContainStr($aRowInCsvArr[7], '|')){
                        $soldQuantityData = $aRowInCsvArr[7];
                    }

                    $res = array(
                        'date' => $datum,
                        'product_name' => $aRowInCsvArr[5],
                        'quantity_data' => $soldQuantityData
                    );
                }

            }
            fclose($file);
        }

        return $res;
    }
    /**
     * 根据EAN, 从CSV的文件目录中, 得到这款产品(所有天的)的 90|30|14|7|3 销量
     * @param $ean
     * @param $lang
     * @param $interval
     * @param $dir //csv存储的地方
     * @return array
     */
    public function getDreiTageVerkauftMenge($ean, $lang, $interval, $dir){
        $res = array();

        $csv_file_paths = $this->getFilePaths($dir, false);
        foreach ($csv_file_paths as $cfp){
            $vm = $this->getCSVVerkaufMengeContent($cfp, $lang, $ean);
            if($vm != null && COUNT($vm) > 0){
                array_push($res, $vm);
            }
        }

        return $res;
    }

    /**
     * 根据订单创建者的ID，拿到他在哪家Showroom工作
     * @param id $
     * @return string
     */
    public function getWorkplaceByUserId($id){
        $result = 'MaiMai-Dietzenbach';
        $user = get_userdata($id);
        $username = $user->user_login;
        switch ($username){
            case 'murat':
                $result = 'Showroom-Gelnhausen';
                break;
            case 'roland':
                $result = 'Showroom-Gelnhausen';
                break;
            case 'kai':
                $result = 'Showroom-Dietzenbach';
                break;
            case 'mahid':
                $result = 'Showroom-Dietzenbach';
                break;
            default:
                $result = 'MaiMai-Dietzenbach';
        }
        return $result;
    }

    /**
     * 服务于 api-getDeliveryNotesList.php
     * @param $n
     * @return string
     */
    public function getDeliveryNoteShowName($n){
        $sn = '';
        $nLst = explode('.', $n);

        $date_o = $nLst[1];
        $date_o_lst = explode('-', $date_o);
        $date = $date_o_lst[2] . '.' . $date_o_lst[1] . '.' . $date_o_lst[0];
        $time_o = $nLst[2];
        $time = substr($time_o, 0, 2) . ':' . substr($time_o, 2,2);

        $sn = 'Erstellt&nbsp;am&nbsp;<b>' . $date . '</b> um&nbsp;<b>' . $time . '</b>';

        return $sn;
    }

    /**
     * 计算时间的int格式
     * 服务于 api-getDeliveryNotesList.php
     * @param $n
     * @return int
     */
    public function getCreateAtTimeInt($n){
        $nLst = explode('.', $n);

        $date = $nLst[1];
        //$date_o_lst = explode('-', $date_o);
        //$date = $date_o_lst[2] . '.' . $date_o_lst[1] . '.' . $date_o_lst[0];
        $time_o = $nLst[2];
        $time = substr($time_o, 0, 2) . ':' . substr($time_o, 2,2) . ':' . substr($time_o, 4,2);

        return strtotime($date . " " . $time);
    }

    /**
     * SetOperationHistory (另外还有个API支持同样的功能)
     * @param $orderId
     * @param $message
     * @param $docType
     */
    public function setOperationHistory($orderId, $message, $docType, $userId){

        global $wpdb;
        global $current_user;
        get_current_user();

        $db_table_operation_history = "ihattach_operation_history";

        /**
         * 通用数据
         * $_settings_data['server-info']['gmt_offset'] 是 NULL
         */
        // 夏令时
        //$create_at = time() + ( 2 * 3600 );
        // 冬令时
        $create_at = time() + ( $this->settings_data['server-info']['gmt_offset'] * 3600 );
        $create_by = $userId;
        if(intval($userId) == 0){
            $create_by = $current_user->ID;
        }

        $sql_log = "INSERT INTO `" . $db_table_operation_history . "` (`create_at`, `create_by`, `order_id`, `message`, `doc_type`) values (%f, %d, %d, %s, %s)";
        $wpdb->query($wpdb->prepare($sql_log, $create_at, $create_by, $orderId, $message, $docType));

        $isSuccess = true;
        $msg = "The operation history was saved.";
        $data = array(
            "create_at" => date("Y-m-d H:i:s", $create_at)
        );

    }

    /**
     * 根据数据库里面的日期，返回带有格式的日期
     * @param $d //例如：2018-11-05 17:18:52
     * @param $f //例如：DE US CN
     * @return string //例如：05.11.2018 17:18:52
     */
    public function getFormatDateByDate($d, $f){
        $res = '';
        switch($f){
            case 'DE':
                $res = substr($d, 8, 2) . '.' . substr($d, 5, 2) . '.' . substr($d, 0, 4) . ' ' . substr($d, 11, 8);
                break;
            case 'DE-NO-TIME':
                $res = substr($d, 8, 2) . '.' . substr($d, 5, 2) . '.' . substr($d, 0, 4);
                break;
            default:
                //
        }
        return $res;
    }

}
