<?php
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2019/7/1
 * Time: 14:43
 */

namespace SoGood\Support\Model;

$root_path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
require_once ( $root_path . '/wp-content/themes/twentyseventeen/Util/Helper.php');
require_once ( $root_path . '/wp-content/themes/twentyseventeen/Util/AfterbuyApi.php');
use SoGood\Support\Util\Helper;
use SoGood\Support\Util\AfterbuyApi;

class AfterbuyOrderManager
{

    /**
     * afterbuy authentication credentials
     */
    private $endpointUrl;
    private $partnerId;
    private $partnerPw;
    private $userId;
    private $userPw;
    private $_settings_data;

    private $_helper;

    /**
     * order details
     */
    private $mediator;
    private $referenceId;
    private $isAddressSame;             // true or false
    private $customerCompany;               // $orderDetails["KFirma"]
    private $customerSurname;               // $orderDetails["KNachname"]
    private $customerFirstname;             // $orderDetails["KVorname"]
    private $customerStreet;                // $orderDetails["KStrasse"]
    private $customerPostcode;              // $orderDetails["KPLZ"]
    private $customerCity;                  // $orderDetails["KOrt"]
    private $customerCountry;               // $orderDetails["KLand"]
    private $customerCountryName;           // $orderDetails["KBundesland"]
    private $customerMail;                  // $orderDetails["Kbenutzername"] 和 $orderDetails["Kemail"]
    private $customerTelephone;             // $orderDetails["Ktelefon"]
    private $customerShippingCompany;       // $orderDetails["KLFirma"]
    private $customerShippingSurname;       // $orderDetails["KLNachname"]
    private $customerShippingFirstname;     // $orderDetails["KLVorname"]
    private $customerShippingStreet;        // $orderDetails["KLStrasse"]
    private $customerShippingPostcode;      // $orderDetails["KLPLZ"]
    private $customerShippingCity;          // $orderDetails["KLOrt"]
    private $customerShippingCountry;       // $orderDetails["KLLand"]
    private $customerShippingCountryName;// 弃用
    private $customerShippingTelephone;     // $orderDetails["KLTelefon"]
    private $paymentMethod;                 // $orderDetails["Zahlart"]
    private $paymentDetails;            // 弃用
    private $paidSum;                   // 弃用
    private $shippingMethod;                // $orderDetails["Versandart"]
    private $shippingDetails;           // 弃用
    private $soldItems;                     // 解析Json
    private $comment;                       // $orderDetails["Kommentar"]
    private $billNr;                        // $orderDetails["InvoiceNumber"]
    private $invoiceComment;                // $orderDetails["InvoiceMemo"]
    private $afterbuyAccount;               // sogood or maimai
    private $kundenerkennung;               // 2 = Eigene Kundennummer (EKundenNr)
    private $eKundenNr;                     // $orderDetails["EKundenNr"]
    private $kBenutzername;                 // $orderDetails["Kbenutzername"]

    /**
     * AfterbuyOrderManager constructor.
     */
    public function __construct($afterbuyAccount)
    {
        $this->afterbuyAccount = $afterbuyAccount;
        $this->_settings_data = parse_ini_file(WP_CONTENT_DIR . DIRECTORY_SEPARATOR. ".." . DIRECTORY_SEPARATOR . "sogood_settings.ini",true);
        $this->_helper = new Helper();
    }

    /**
     * get afterbuy authentication credentials from config file
     * set into the instance of AfterbuyOrderManager
     */
    public function setAuthenticationCredentials($afterbuyApiType)
    {
        switch ($afterbuyApiType)
        {
            case 'Shop':
                $this->partnerId = $this->_settings_data["afterbuy-konto"][$this->afterbuyAccount."_partnerId_order"];
                $this->partnerPw = $this->_settings_data["afterbuy-konto"][$this->afterbuyAccount."_partnerPw_order"];
                $this->userId = $this->_settings_data["afterbuy-konto"][$this->afterbuyAccount."_userId"];
                $this->userPw = $this->_settings_data["afterbuy-konto"][$this->afterbuyAccount."_userPw"];
                break;
            case 'XML':
                $this->partnerId = $this->_settings_data["afterbuy-konto"][$this->afterbuyAccount."_partnerId"];
                $this->partnerPw = $this->_settings_data["afterbuy-konto"][$this->afterbuyAccount."_partnerPw"];
                $this->userId = $this->_settings_data["afterbuy-konto"][$this->afterbuyAccount."_userId"];
                $this->userPw = $this->_settings_data["afterbuy-konto"][$this->afterbuyAccount."_userPw"];
                break;
            default:
                //
        }
    }

    public function create()
    {

        $isSuccess = false;
        $msg = array();

        $this->setAuthenticationCredentials('Shop');
        if($this->partnerId !== null){

            $isSuccess = true;
            array_push($msg, 'Load afterbuy authentication credentials successfully.');

            /**
             * prepare Order Details
             */
            $orderDetails = array(
                'Action' => 'new',
                'PartnerID' => $this->partnerId,
                'PartnerPass' => $this->partnerPw,
                'UserID' => $this->userId
            );

            // Geburtsdatum des Käufers
            $orderDetails["KBirthday"] = null;

            // Setzt im Käuferdatensatz den Händlerstatus
            $orderDetails["Haendler"] = null;

            // versand
            $orderDetails["Versandart"] = $this->shippingMethod;
            $orderDetails["NoVersandCalc"] = "1";

            // Payment
            $orderDetails["Zahlart"] = $this->paymentMethod;

            // 0 = kein Bezahlt-Status setzen
            // 1 = Bezahlt-Status setzen
            $orderDetails["SetPay"] = "1";

            // Bestandart
            $orderDetails["Bestandart"] = "auktion";

            // Erkennen durch Artikel Nummer
            $orderDetails["Artikelerkennung"] = 1;

            // Versandkosten
            //$orderDetails["Versandkosten"] = '0,00';

            // memo
            $orderDetails["VMemo"] = $this->comment;

            // Invoice info
            //$orderDetails["InvoiceNumber"] = $this->billNr;
            //$orderDetails["InvoiceMemo"] = $this->invoiceComment;

            // 客户在SoGood商城的用户名   例如：96994_sogood-de-ying.wang@gmx.de
            global $current_user;
            get_current_user();

            //$orderDetails["Kbenutzername"] = 'Kd.-Nr.:' . $this->eKundenNr . '@' . $current_user->user_login . '.' . $this->mediator;
            $orderDetails["Kbenutzername"] = $current_user->user_login . '@' . $this->mediator;
            $orderDetails["EKundenNr"] = $this->eKundenNr;
            $orderDetails["Kundenerkennung"] = 2;

            // 客户帐单地址信息
            //$orderDetails["Kanrede"] = 'Herr';
            $orderDetails["KFirma"] = $this->customerCompany;
            $orderDetails["KVorname"] = $this->customerFirstname;
            $orderDetails["KNachname"] = $this->customerSurname;
            $orderDetails["KStrasse"] = $this->customerStreet;
            //$orderDetails["KStrasse2"] = '3. Etage';
            $orderDetails["KPLZ"] = $this->customerPostcode;
            $orderDetails["KOrt"] = $this->customerCity;
            $orderDetails["KBundesland"] = $this->customerCountryName;
            $orderDetails["Ktelefon"] = $this->customerTelephone;
            //$orderDetails["Kfax"] = $orderBillingAddress["fax"];
            $orderDetails["Kemail"] = $this->customerMail;
            $orderDetails["KLand"] = $this->customerCountry;

            // 0: same
            // 1: not same
            if($this->isAddressSame){
                $orderDetails["Lieferanschrift"] = 0;
                // Lieferanschrift des Käufers
                $orderDetails["KLFirma"] = $this->customerCompany;
                $orderDetails["KLVorname"] = $this->customerFirstname;
                $orderDetails["KLNachname"] = $this->customerSurname;
                $orderDetails["KLStrasse"] = $this->customerStreet;
                //$orderDetails["KLStrasse2"] = '3. Etage';
                $orderDetails["KLPLZ"] = $this->customerPostcode;
                $orderDetails["KLOrt"] = $this->customerCity;
                $orderDetails["KLLand"] = $this->customerCountry;
                $orderDetails["KLTelefon"] = $this->customerTelephone;
            }else{
                $orderDetails["Lieferanschrift"] = 1;
                // Lieferanschrift des Käufers
                $orderDetails["KLFirma"] = $this->customerShippingCompany;
                $orderDetails["KLVorname"] = $this->customerShippingFirstname;
                $orderDetails["KLNachname"] = $this->customerShippingSurname;
                $orderDetails["KLStrasse"] = $this->customerShippingStreet;
                //$orderDetails["KLStrasse2"] = '3. Etage';
                $orderDetails["KLPLZ"] = $this->customerShippingPostcode;
                $orderDetails["KLOrt"] = $this->customerShippingCity;
                $orderDetails["KLLand"] = $this->customerShippingCountry;
                $orderDetails["KLTelefon"] = $this->customerShippingTelephone;
            }

            // Positions
            $soldItemsArr = json_decode(json_encode($this->soldItems), true);
            $positions = $soldItemsArr["items"];
            $orderDetails["PosAnz"] = COUNT($positions);
            for($i = 0; $i < $orderDetails["PosAnz"]; ++$i)
            {
                $orderDetails["Artikelnr"."_".($i+1)] = $positions[$i]['ean'];
                $orderDetails["Artikelname"."_".($i+1)] = $positions[$i]['title'];
                $orderDetails["ArtikelEpreis"."_".($i+1)] = number_format(intval($positions[$i]['price']), 2, ',' , $thousands_sep = '');
                $orderDetails["ArtikelMwSt"."_".($i+1)] = number_format((intval($positions[$i]['price']) * intval($positions[$i]['tax'])), 2, ',' , $thousands_sep = '');
                $orderDetails["ArtikelMenge"."_".($i+1)] = $positions[$i]['qInCart'];
                $orderDetails["ArtikelGewicht"."_".($i+1)] = '0,01';
                $orderDetails["ArtikelLink"."_".($i+1)] = null;
                $orderDetails["Attribute"."_".($i+1)] = null;
                $orderDetails["ArtikelStammID"."_".($i+1)] = $positions[$i]['ean'];
            }

            $resultXML = $this->_helper->requestHttpApi(
                $this->_settings_data["urls"]["Api_Url_Afterbuy_CreateOrder_UTF8"],
                $orderDetails,
                array(
                    //'Authorization: Basic ' . $authorization,
                    //'Accept: application/xml;'
            ));
            $result = $this->_helper->xml_parser($resultXML);


            // update Invoice info
            // $this->billNr & $this->invoiceComment;
            if($result['success'] == 1){
                $ab_oid = $result['data']['AID'];
                $this->update($ab_oid, 0);
            }


            return $result;

        }else{

            $isSuccess = false;
            array_push($msg, 'Can not load afterbuy authentication credentials. Please check the afterbuy account in parameters.');

        }

        return array(
            'isSuccess' => $isSuccess,
            'msg' => $msg
        );

    }

    /**
     * @param $type 0: update invoice info
     * @param $type 1: update customer address
     */
    public function update($ab_oid, $type)
    {

        $isSuccess = false;
        $msg = array();
        $response = array();

        $this->setAuthenticationCredentials('XML');
        if($this->partnerId !== null){

            $afterbuyApi = new AfterbuyApi();
            $afterbuyApi->setParams(
                $this->_settings_data["urls"]["Api_Url_Afterbuy"],
                $this->partnerId,
                $this->partnerPw,
                $this->userId,
                $this->userPw
            );

            switch ($type)
            {
                case 0:
                    $response = $afterbuyApi->updateInvoiceInfo(array(
                        'afterbuy_order_id' => $ab_oid,
                        'invoice_comment' => $this->invoiceComment,
                        'invoice_nr' => $this->billNr,
                    ));
                    break;
                case 1:
                    $response = $afterbuyApi->updateOrderAddress(array(
                        'afterbuy_order_id' => $ab_oid,
                        'is_address_same' => $this->isAddressSame,
                        'customer_company' => $this->customerCompany,
                        'customer_surname' => $this->customerSurname,
                        'customer_firstname' => $this->customerFirstname,
                        'customer_street' => $this->customerStreet,
                        'customer_postcode' => $this->customerPostcode,
                        'customer_city' => $this->customerCity,
                        'customer_country' => $this->customerCountry,
                        'customer_country_name' => $this->customerCountryName,
                        'customer_telephone' => $this->customerTelephone,
                        'customer_shipping_company' => $this->customerShippingCompany,
                        'customer_shipping_surname' => $this->customerShippingSurname,
                        'customer_shipping_firstname' => $this->customerShippingFirstname,
                        'customer_shipping_street' => $this->customerShippingStreet,
                        'customer_shipping_postcode' => $this->customerShippingPostcode,
                        'customer_shipping_city' => $this->customerShippingCity,
                        'customer_shipping_country' => $this->customerShippingCountry,
                        'customer_shipping_country_name' => $this->customerShippingCountryName,
                        'customer_shipping_telephone' => $this->customerShippingTelephone
                    ));
                    break;
                default:
                    //
            }

        }else{

            $isSuccess = false;
            array_push($msg, 'Can not load afterbuy authentication credentials. Please check the afterbuy account in parameters.');

        }

        return array(
            'isSuccess' => $isSuccess,
            'msg' => $msg,
            'response' => $response
        );


    }

    /**
     * @return String
     */
    public function getMediator()
    {
        return $this->mediator;
    }

    /**
     * @param String $mediator
     */
    public function setMediator($v)
    {
        $this->mediator = trim($v);
    }

    /**
     * @return String
     */
    public function getReferenceId()
    {
        return $this->referenceId;
    }

    /**
     * @param String $referenceId
     */
    public function setReferenceId($v)
    {
        $this->referenceId = trim($v);
    }

    /**
     * @return boolean
     */
    public function getIsAddressSame()
    {
        return $this->isAddressSame;
    }

    /**
     * @param boolean $isAddressSame
     */
    public function setIsAddressSame($v)
    {
        $this->isAddressSame = trim($v);
    }

    /**
     * @return String
     */
    public function getCustomerCompany()
    {
        return $this->customerCompany;
    }

    /**
     * @param String $customerCompany
     */
    public function setCustomerCompany($v)
    {
        $this->customerCompany = trim($v);
    }

    /**
     * @return String
     */
    public function getCustomerSurname()
    {
        return $this->customerSurname;
    }

    /**
     * @param String $customerSurname
     */
    public function setCustomerSurname($v)
    {
        $this->customerSurname = trim($v);
    }

    /**
     * @return String
     */
    public function getCustomerFirstname()
    {
        return $this->customerFirstname;
    }

    /**
     * @param String $customerFirstname
     */
    public function setCustomerFirstname($v)
    {
        $this->customerFirstname = trim($v);
    }

    /**
     * @return String
     */
    public function getCustomerStreet()
    {
        return $this->customerStreet;
    }

    /**
     * @param String $customerStreet
     */
    public function setCustomerStreet($v)
    {
        $this->customerStreet = trim($v);
    }

    /**
     * @return String
     */
    public function getCustomerPostcode()
    {
        return $this->customerPostcode;
    }

    /**
     * @param String $customerPostcode
     */
    public function setCustomerPostcode($v)
    {
        $this->customerPostcode = trim($v);
    }

    /**
     * @return String
     */
    public function getCustomerCity()
    {
        return $this->customerCity;
    }

    /**
     * @param String $customerCity
     */
    public function setCustomerCity($v)
    {
        $this->customerCity = trim($v);
    }

    /**
     * @return String
     */
    public function getCustomerCountry()
    {
        return $this->customerCountry;
    }

    /**
     * @param String $customerCountry
     */
    public function setCustomerCountry($v)
    {
        $this->customerCountry = trim($v);
    }

    /**
     * @return String
     */
    public function getCustomerCountryName()
    {
        return $this->customerCountryName;
    }

    /**
     * @param String $customerCountryName
     */
    public function setCustomerCountryName($v)
    {
        $this->customerCountryName = trim($v);
    }

    /**
     * @return String
     */
    public function getCustomerMail()
    {
        return $this->customerMail;
    }

    /**
     * @param String $customerMail
     */
    public function setCustomerMail($v)
    {
        $this->customerMail = trim($v);
    }

    /**
     * @return String
     */
    public function getCustomerTelephone()
    {
        return $this->customerTelephone;
    }

    /**
     * @param String $customerTelephone
     */
    public function setCustomerTelephone($v)
    {
        $this->customerTelephone = trim($v);
    }

    /**
     * @return String
     */
    public function getCustomerShippingCompany()
    {
        return $this->customerShippingCompany;
    }

    /**
     * @param String $customerShippingCompany
     */
    public function setCustomerShippingCompany($v)
    {
        $this->customerShippingCompany = trim($v);
    }

    /**
     * @return String
     */
    public function getCustomerShippingSurname()
    {
        return $this->customerShippingSurname;
    }

    /**
     * @param String $customerShippingSurname
     */
    public function setCustomerShippingSurname($v)
    {
        $this->customerShippingSurname = trim($v);
    }

    /**
     * @return String
     */
    public function getCustomerShippingFirstname()
    {
        return $this->customerShippingFirstname;
    }

    /**
     * @param String $customerShippingFirstname
     */
    public function setCustomerShippingFirstname($v)
    {
        $this->customerShippingFirstname = trim($v);
    }

    /**
     * @return String
     */
    public function getCustomerShippingStreet()
    {
        return $this->customerShippingStreet;
    }

    /**
     * @param String $customerShippingStreet
     */
    public function setCustomerShippingStreet($v)
    {
        $this->customerShippingStreet = trim($v);
    }

    /**
     * @return String
     */
    public function getCustomerShippingPostcode()
    {
        return $this->customerShippingPostcode;
    }

    /**
     * @param String $customerShippingPostcode
     */
    public function setCustomerShippingPostcode($v)
    {
        $this->customerShippingPostcode = trim($v);
    }

    /**
     * @return String
     */
    public function getCustomerShippingCity()
    {
        return $this->customerShippingCity;
    }

    /**
     * @param String $customerShippingCity
     */
    public function setCustomerShippingCity($v)
    {
        $this->customerShippingCity = trim($v);
    }

    /**
     * @return String
     */
    public function getCustomerShippingCountry()
    {
        return $this->customerShippingCountry;
    }

    /**
     * @param String $customerShippingCountry
     */
    public function setCustomerShippingCountry($v)
    {
        $this->customerShippingCountry = trim($v);
    }

    /**
     * @return String
     */
    public function getCustomerShippingCountryName()
    {
        return $this->customerShippingCountryName;
    }

    /**
     * @param String $customerShippingCountryName
     */
    public function setCustomerShippingCountryName($v)
    {
        $this->customerShippingCountryName = trim($v);
    }

    /**
     * @return String
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param String $paymentMethod
     */
    public function setPaymentMethod($v)
    {
        $this->paymentMethod = trim($v);
    }

    /**
     * @return String
     */
    public function getPaymentDetails()
    {
        return $this->paymentDetails;
    }

    /**
     * @param String $paymentDetails
     */
    public function setPaymentDetails($v)
    {
        $this->paymentDetails = trim($v);
    }

    /**
     * @return String
     */
    public function getPaidSum()
    {
        return $this->paidSum;
    }

    /**
     * @param String $paidSum
     */
    public function setPaidSum($v)
    {
        $this->paidSum = trim($v);
    }

    /**
     * @return String
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

    /**
     * @param String $shippingMethod
     */
    public function setShippingMethod($v)
    {
        $this->shippingMethod = trim($v);
    }

    /**
     * @return String
     */
    public function getShippingDetails()
    {
        return $this->shippingDetails;
    }

    /**
     * @param String $shippingDetails
     */
    public function setShippingDetails($v)
    {
        $this->shippingDetails = trim($v);
    }

    /**
     * @return array
     */
    public function getSoldItems()
    {
        return $this->soldItems;
    }

    /**
     * @param array $soldItems
     */
    public function setSoldItems($v)
    {
        $this->soldItems = $v;
    }

    /**
     * @return String
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param String $comment
     */
    public function setComment($v)
    {
        $this->comment = trim($v);
    }

    /**
     * @return String
     */
    public function getBillNr()
    {
        return $this->billNr;
    }

    /**
     * @param String $billNr
     */
    public function setBillNr($v)
    {
        $this->billNr = trim($v);
    }

    /**
     * @return String
     */
    public function getCustomerShippingTelephone()
    {
        return $this->customerShippingTelephone;
    }

    /**
     * @param String $customerShippingTelephone
     */
    public function setCustomerShippingTelephone($v)
    {
        $this->customerShippingTelephone = trim($v);
    }

    /**
     * @return String
     */
    public function getInvoiceComment()
    {
        return $this->invoiceComment;
    }

    /**
     * @param String $invoiceComment
     */
    public function setInvoiceComment($v)
    {
        $this->invoiceComment = trim($v);
    }

    /**
     * @return String
     */
    /*
    public function getAfterbuyAccount()
    {
        return $this->afterbuyAccount;
    }
    */

    /**
     * @param String $afterbuyAccount
     */
    /*
    public function setAfterbuyAccount($v)
    {
        $this->afterbuyAccount = trim($v);
    }
    */

    /**
     * @return String
     */
    public function getKundenerkennung()
    {
        return $this->kundenerkennung;
    }

    /**
     * @param String $kundenerkennung
     */
    public function setKundenerkennung($v)
    {
        $this->kundenerkennung = trim($v);
    }

    /**
     * @return String
     */
    public function getEKundenNr()
    {
        return $this->eKundenNr;
    }

    /**
     * @param String $eKundenNr
     */
    public function setEKundenNr($v)
    {
        $this->eKundenNr = trim($v);
    }

    /**
     * @return String
     */
    public function getEndpointUrl()
    {
        return $this->endpointUrl;
    }

    /**
     * @param String $endpointUrl
     */
    public function setEndpointUrl($v)
    {
        $this->endpointUrl = trim($v);
    }

    /**
     * @return String
     */
    public function getPartnerId()
    {
        return $this->partnerId;
    }

    /**
     * @param String $partnerId
     */
    public function setPartnerId($v)
    {
        $this->partnerId = trim($v);
    }

    /**
     * @return String
     */
    public function getPartnerPw()
    {
        return $this->partnerPw;
    }

    /**
     * @param String $partnerPw
     */
    public function setPartnerPw($v)
    {
        $this->partnerPw = trim($v);
    }

    /**
     * @return String
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param String $userId
     */
    public function setUserId($v)
    {
        $this->userId = trim($v);
    }

    /**
     * @return String
     */
    public function getUserPw()
    {
        return $this->userPw;
    }

    /**
     * @param String $userPw
     */
    public function setUserPw($v)
    {
        $this->userPw = trim($v);
    }

    /**
     * @return String
     */
    public function getKBenutzername()
    {
        return $this->kBenutzername;
    }

    /**
     * @param String $kBenutzername
     */
    public function setKBenutzername($v)
    {
        $this->kBenutzername = trim($v);
    }

}