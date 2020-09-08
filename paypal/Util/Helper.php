<?php
/**
 * Created by PhpStorm.
 * User: YingWang
 * Date: 2017/7/24
 * Time: 16:05
 */

namespace SoGood\api\Util;


class Helper
{

    public function __construct()
    {
        //
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
    function getFilePaths($dir, $supportTree){

        $res = array();
        $d = dir($dir);
        while ($fileName = $d->read()) {

            if($supportTree){

                // 下一级文件夹里面的内容也读取
                // ...

            }else{

                // 遇到下一级文件夹就跳过
                if ((is_dir($dir . "/" . $fileName)) OR ($fileName == ".") OR ($fileName == "..")) {
                    // 遇到文件目录 都跳过
                } else {
                    array_push($res, $dir . "/" . $fileName);
                }

            }

        }
        return $res;

    }

    function getPageCustomIDByLink($u){
        $pageLinks = array(
            "0" => "/dashboard/",
            "1" => "/product-overview/",
            "2" => "/product-list/",
            "3" => "/order-overview/",
            "4" => "/order-create/",
            "5" => "/my-account/",
            "6" => "/order-list/",
            "7" => "/order-list-onhold/",
            "8" => "/quote-create/",
            "9" => "/quote-list/",
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
    function checkStrInString($str, $string){
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
    function formatPrice($p, $currency){
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
    function getMediate(){
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
    function getZhanWeiFuByFieldName($fieldName){
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
    function getPageDealWith(){
        $pT = "order";

        /**
         * 获得当前页面地址url
         */
        $current_url = home_url(add_query_arg(array()));

        if($this->checkContainStr($current_url, 'order-create')){
            $pT = "order";
        }else if($this->checkContainStr($current_url, 'order-list')){
            $pT = "order";
        }else if($this->checkContainStr($current_url, 'order-list-onhold')){
            $pT = "order";
        }else if($this->checkContainStr($current_url, 'quote-create')){
            $pT = "quote";
        }else if($this->checkContainStr($current_url, 'quote-list')){
            $pT = "quote";
        }

        return $pT;
    }

    /**
     * 取括号里面的字符
     * ！！！目前只支持一组括号。
     * @param $str
     * @return mixed
     */
    function getTextInKuoHao($str)
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
    function getFileQuantity($orderId){
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
    function encrypt($string, $operation, $key='')
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
    function isValueInArray($str, $arr){
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
    function getMessagePageHtml($msg){
        $htmlTxt = '<!DOCTYPE html>
                <html>
                <head>
                <title>Nachricht - Mai & Mai GmbH</title>
                <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <link href="css/massage-page.css" rel="stylesheet" type="text/css" />
                <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,300,600,700" rel="stylesheet" type="text/css" />
                </head>
                <body>
                <div id="logo-wrap">
                <div>
                <img src="images/logo_maimai_211_115.png" />
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
     * Create Paypal landing page
     * @param $msg
     * @return string
     */
    function getPaypalPageHtml($id, $firstname, $lastname, $paidSum){
        $htmlTxt = '<!DOCTYPE html>
                <html>
                <head>
                    <title>Nachricht - Mai & Mai GmbH</title>
                    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
                    <!-- Ensures optimal rendering on mobile devices. -->
                    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                    <!-- Optimal Internet Explorer compatibility -->
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    <link href="css/massage-page.css" rel="stylesheet" type="text/css" />
                    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,300,600,700" rel="stylesheet" type="text/css" />
                </head>
                <body>
                    <script 
                        src="https://www.paypal.com/sdk/js?currency=EUR&client-id=AWV5t36dvirznEOxapK6t3uxYNwOtK0TNMehDZ12d44P0x2qDc6VQIqiDU6d4RXNWiKbKlyUxMKBlCif">
                        // Required. Replace SB_CLIENT_ID with your sandbox client ID.
                    </script>

                    <script>
                        paypal.Buttons(
                                {
                                    createOrder : function(data, actions) {
                                        // This function sets up the details of the transaction, including the amount and line item details.
                                        return actions.order.create({
                                            purchase_units : [ {
                                                amount : {
                                                    value : '.$paidSum.'
                                                }
                                            } ]
                                        });
                                    },
                                    onApprove : function(data, actions) {
                                        // This function captures the funds from the transaction.
                                        return actions.order.capture().then(
                                                function(details) {
                                                    // This function shows a transaction success message to your buyer.
                                                    // alert("Die Zahlung ist von "
                                                    //         + details.payer.name.given_name + " erfolgt.");
                                                    window.location = "payment-successful.php?&orderID="+data.orderID
                                                                       +"&goodoneID="+'.$id.';
                                                });
                                    },
                                    onCancel : function(data, actions) {
                                        // This function captures the funds from the transaction.
                                        return actions.order.capture().then(
                                                function(details) {
                                                    // This function shows a transaction success message to your buyer.
                                                    // alert("Die Zahlung ist von "
                                                    //         + details.payer.name.given_name + " erfolgt.");
                                                    window.location = "payment-cancelled.php?&orderID="+data.orderID;
                                                });
                                    },
                                    onError : function(data, actions) {
                                        // This function captures the funds from the transaction.
                                        return actions.order.capture().then(
                                                function(details) {
                                                    // This function shows a transaction success message to your buyer.
                                                    // alert("Die Zahlung ist von "
                                                    //         + details.payer.name.given_name + " erfolgt.");
                                                    window.location = "payment-cancelled.php?&orderID="+data.orderID;
                                                });
                                    }
                                }).render("#paypal-button-container");
                        //This function displays Smart Payment Buttons on your web page.
                    </script>

                    <div id="logo-wrap">
                        <div>
                            <img src="images/logo_maimai_211_115.png" />
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
                                    Lieber '.$firstname.' '.$lastname.', <br/> <br/>
                                    Bitte Ihre Bestellung '.$id.' in einer Summe '.$paidSum.' &euro; per Paypal zahlen.
                                </div>

                                <div id="paypal-button-container"></div>

                                <div id="submit-btn" onclick="location.href=\'https://www.sogood.de\'">
                                    zu unserem Onlineshop
                                </div>
                            </div>
                        </div>
                    </div>
                </body>
                </html>';
        return $htmlTxt;
    }
}