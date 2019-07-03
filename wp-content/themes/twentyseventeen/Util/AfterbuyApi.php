<?php

namespace SoGood\Support\Util;

interface iAfterbuyApi{
	
    public function __construct(); 
    public function setParams($apiUrl, $partnerId, $partnerPw, $userId, $userPw);
	
}

final class AfterbuyApi implements iAfterbuyApi{
	
    protected $apiUrl; 
    protected $partnerId; 
    protected $partnerPw; 
    protected $userId; 
    protected $userPw;
    protected $rawXml; 
	
    public function __construct(){
		
        $this->apiUrl = null; 
        $this->partnerId = null; 
        $this->partnerPw = null; 
        $this->userId = null; 
        $this->userPw = null;
        $this->rawXml = "<?xml version=\"1.0\" encoding=\"utf-8\"?><Request></Request>";
		
    } 
	
    public function setParams($apiUrl, $partnerId, $partnerPw, $userId, $userPw){
		
        $this->apiUrl = $apiUrl;
        $this->partnerId = $this->_toUtf($partnerId);
        $this->partnerPw = $this->_toUtf($partnerPw);
        $this->userId = $this->_toUtf($userId);
        $this->userPw = $this->_toUtf($userPw);
        return true;
		
    }
	
    public function getSoldItems($detailLevel, $filterVals){
		
        //Erstmal erstellen wir den Standard bereich 
        $xml = $this->_createGlobal("GetSoldItems", $detailLevel);
		
		$xml->addChild("RequestAllItems", "0");
		$xml->addChild("MaxSoldItems", "1");
		$xml->addChild("OrderDirection", "0");
		
		$xml->addChild("DataFilter");
		
		foreach ($filterVals as $itm){
			$tag_Filter = $xml->DataFilter->addChild("Filter");
			$tag_Filter->addChild("FilterName", $itm["FilterName"]);
			$tag_FilterValues = $tag_Filter->addChild("FilterValues");

			// 有可能只有一个 OrderID
			if(!is_array($itm["FilterValues"])){

                $tag_FilterValues->addChild("FilterValue", $itm["FilterValues"]);

            }else{

                foreach ($itm["FilterValues"] as $k => $v){
                    if(!is_array($v)){
                        $tag_FilterValues->addChild($k, $v);
                    }else{
                        foreach ($v as $itm1){
                            $tag_FilterValues->addChild("FilterValue", $itm1);
                        }
                    }
                }

            }
			
		}
		
        //Jetzt absenden und antwort erhalten 
        return $this->_makeCall($xml->asXml());
		
        //TESTAUSGABE VON DER ANTWORT 
        //var_dump($response);
		
		//Test: Pruefe mal, ob der Inhalt von XML-Request richtig ist.
		//print_r($xml->asXml());
		
    }

    public function updateInvoiceInfo($data){
        $xmlStr = '<?xml version="1.0" encoding="utf-8"?>'.
            '<Request>'.
                '<AfterbuyGlobal>'.
                    '<PartnerID>' . $this->partnerId . '</PartnerID>'.
                    '<PartnerPassword>' . $this->partnerPw . '</PartnerPassword>'.
                    '<UserID>' . $this->userId . '</UserID>'.
                    '<UserPassword>' . $this->userPw . '</UserPassword>'.
                    '<CallName>UpdateSoldItems</CallName>'.
                    '<DetailLevel>0</DetailLevel>'.
                    '<ErrorLanguage>DE</ErrorLanguage>'.
                '</AfterbuyGlobal>'.
                '<Orders>'.
                    '<Order>'.
                        '<OrderID>' . $data['afterbuy_order_id'] . '</OrderID>'.
                        '<InvoiceMemo>' . $data['invoice_comment'] . '</InvoiceMemo>'.
                        '<InvoiceNumber>' . $data['invoice_nr'] . '</InvoiceNumber>'.
                    '</Order>'.
                '</Orders>'.
            '</Request>';
        return $this->_makeCall($xmlStr);
    }

    private function _makeCall($xml){
        $context = $this->_createContext($xml);
        $stream = fopen($this->apiUrl, "r", false, $context);
        $contents = stream_get_contents($stream);
        fclose($stream); 
        $response = simplexml_load_string($contents);
        if ($response->CallStatus == "Error") { 
            $this->_throwError($response);
        }
        if ($response->CallStatus == "Warning") { 
            $this->_throwWarning($response);
        } 
        return $response;
		
    }
	
    private function _createGlobal($callName, $detailLvl){
		
        $xml = new \SimpleXMLElement($this->rawXml); 
        $xml->addChild("AfterbuyGlobal");
        $xml->AfterbuyGlobal->addChild("PartnerID", $this->partnerId); 
        $xml->AfterbuyGlobal->addChild("PartnerPassword", $this->partnerPw); 
        $xml->AfterbuyGlobal->addChild("UserID", $this->userId); 
        $xml->AfterbuyGlobal->addChild("UserPassword", $this->userPw); 
        $xml->AfterbuyGlobal->addChild("CallName", $callName); 
        $xml->AfterbuyGlobal->addChild("DetailLevel", $detailLvl);
        $xml->AfterbuyGlobal->addChild("ErrorLanguage", "DE");
        return $xml;
		
    }
	
    private function _createContext($data) {
		
        $context = array (); 
        $context["http"] = array(); 
        $context["http"]["method"] = "POST"; 
        $context["http"]["header"] = "Content-type: application/x-www-form-urlencoded\r\n"; 
        $context["http"]["header"].= "Content-Length: ".mb_strlen($data)."\r\n"; 
        $context["http"]["content"] = $data; 
        $context = stream_context_create($context); 
        return $context;
		
    }
	
    private function _toUtf($content){
        return mb_convert_encoding($content, "UTF-8", "auto");
    }
	
    private function _toAscii($content){
        return mb_convert_encoding($content, "ASCII", "UTF-8");
    }
	
    private function _throwError($response){
		
        $code = (int) $response->Result->ErrorList->Error->ErrorCode; 
        $desc = (string) $this->_toAscii($response->Result->ErrorList->Error->ErrorDescription); 
        $long = (string) $this->_toAscii($response->Result->ErrorList->Error->ErrorLongDescription); 
        if(PHP_SAPI == "cli") { 
            $error = "Fatal Error\nCode:\t\t".$code."\nDescription:\t".$desc."\nDetails:\t".$long; 
        } 
        else { 
            $error = "<table>\n\t<tr>\n\t\t<th colspan=\"2\">Fatal Error</th>\n\t</tr>\n"; 
            $error.= "\t<tr>\n\t\t<td>Code:</td>\n\t\t<td>".$code."</td>\n\t</tr>\n"; 
            $error.= "\t<tr>\n\t\t<td>Description</td>\n\t\t<td>".$desc."</td>\n\t</tr>\n"; 
            $error.= "\t<tr>\n\t\t<td>Details:</td>\n\t\t<td>".$long."</td>\n\t</tr>\n</table>"; 
        } 
        trigger_error($error, E_USER_ERROR);
		
    }
	
    private function _throwWarning($response){
		
        $code = (int) $response->Result->WarningList->Warning->WarningCode; 
        $desc = (string) $this->_toAscii($response->Result->WarningList->Warning->WarningDescription); 
        $long = (string) $this->_toAscii($response->Result->WarningList->Warning->WarningLongDescription); 
        if(PHP_SAPI == "cli") { 
            $error = "Warning\nCode:\t\t".$code."\nDescription:\t".$desc."\nDetails:\t".$long; 
        } 
        else { 
            $error = "<table>\n\t<tr>\n\t\t<th colspan=\"2\">Warning</th>\n\t</tr>\n"; 
            $error.= "\t<tr>\n\t\t<td>Code:</td>\n\t\t<td>".$code."</td>\n\t</tr>\n"; 
            $error.= "\t<tr>\n\t\t<td>Description</td>\n\t\t<td>".$desc."</td>\n\t</tr>\n"; 
            $error.= "\t<tr>\n\t\t<td>Details:</td>\n\t\t<td>".$long."</td>\n\t</tr>\n</table>"; 
        } 
        trigger_error($error, E_USER_WARNING);
		
    }
	
	// 目前只支持一维数组
	public function _SimpleXML2Array($simpleXML){
		
		$arrayOfsXML = array();
		foreach($simpleXML as $sXMLk => $sXMLv){
			$arrayOfsXML[$sXMLk] = $sXMLv;
		}
		return $arrayOfsXML;
		
	}

}
