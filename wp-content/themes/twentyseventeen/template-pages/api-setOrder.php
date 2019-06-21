<?php
/* Template Name: Idealhit Api SetOrder */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/4/25
 * Time: 16:24
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

    $action_name = 'SetOrder';
    $isSuccess = false;
    $msg = '';
    $data = array();

    if(isset($_POST["order_details"])){

        $helper = new Helper();

        global $wpdb;
        global $current_user;
        get_current_user();

        $db_table_orders = "ihattach_orders";
        $db_table_positions = "ihattach_positions";
        $db_table_document_nrs = "ihattach_document_nrs";

        $orderDetails = json_decode(rawurldecode($_POST["order_details"]));
        $orderDetails->mediate = $helper->getMediate();

        /**
         * 通用数据
         */
        $create_at = time() + ( $_settings_data['server-info']['gmt_offset'] * 3600 );
        $update_at = $create_at;
        $create_by = $current_user->ID;
        $update_by = 0;

        /**
         * 存储订单主表
         */
        $order_id_e2 = $orderDetails->order_id_e2;
        $order_id_ab = $orderDetails->order_id_ab;
        $discount = $orderDetails->discount;
        $discount_abholung = $orderDetails->discount_abholung;
        $customer_id = $orderDetails->customer_id;
        $goodone_customer_mail = $orderDetails->customerMail;
        $goodone_customer_firstName = $orderDetails->goodoneCustomerFirstname;
        $goodone_customer_lastName = $orderDetails->goodoneCustomerSurname;
        $customer_company = $orderDetails->customerCompany;
        $customer_city = $orderDetails->customerCity;
        $customer_countryISO = $orderDetails->customerCountry;
        $customer_country = $orderDetails->customerCountryName;
        $customer_fax = $orderDetails->customer_fax;
        $customer_title = $orderDetails->customer_title;
        $customer_firstName = $orderDetails->customerFirstname;
        $customer_lastName = $orderDetails->customerSurname;
        $customer_phone = $orderDetails->customerTelephone;
        $customer_postalCode = $orderDetails->customerPostcode;
        $customer_street = $orderDetails->goodoneCustomerStreet;
        $customer_street1 = $orderDetails->goodoneCustomerStreet1;
        $customer_shipping_company = $orderDetails->customerShippingCompany;
        $customer_shipping_city = $orderDetails->customerShippingCity;
        $customer_shipping_countryISO = $orderDetails->customerShippingCountry;
        $customer_shipping_country = $orderDetails->customerShippingCountryName;
        $customer_shipping_fax = $orderDetails->customer_shipping_fax;
        $customer_shipping_title = $orderDetails->customer_shipping_title;
        $customer_shipping_firstName = $orderDetails->customerShippingFirstname;
        $customer_shipping_lastName = $orderDetails->customerShippingSurname;
        $customer_shipping_phone = $orderDetails->customerShippingTelephone;
        $customer_shipping_postalCode = $orderDetails->customerShippingPostcode;
        $customer_shipping_street = $orderDetails->goodoneCustomerShippingStreet;
        $customer_shipping_street1 = $orderDetails->goodoneCustomerShippingStreet1;
        $customer_userIdPlattform = $orderDetails->customer_userIdPlattform;
        $zahlungsmethode = $orderDetails->paymentMethod;
        $versandart = $orderDetails->shippingMethod;
        $expectedDeliveryDate = $orderDetails->expectedDeliveryDate;
        $mediate = $orderDetails->mediate;
        $memo = $orderDetails->memo;
        $memo_big_account = $orderDetails->memo_big_account;
        $paidSum = $orderDetails->paidSum;
        $status = $orderDetails->status;
        $status_quote = $orderDetails->status_quote;
        $deal_with = $orderDetails->deal_with;
        $first_deal_with = $orderDetails->first_deal_with;
        $vat_nr = $orderDetails->vat_nr;
        $tax = $orderDetails->tax;
        $subtract_from_inventory = $orderDetails->subtract_from_inventory;
        $show_customer_name_in_doc = $orderDetails->show_customer_name_in_doc;
        $afterbuy_account = $orderDetails->afterbuyAccount;

        $sql_order = "INSERT INTO `" . $db_table_orders . "` (`create_at`, `update_at`, `create_by`, `update_by`, `order_id_e2`, `order_id_ab`, `discount`, `discount_abholung`, `customer_id`, `goodone_customer_mail`, `goodone_customer_firstName`, `goodone_customer_lastName`, `customer_company`, `customer_city`, `customer_countryISO`, `customer_country`, `customer_fax`, `customer_title`, `customer_firstName`, `customer_lastName`, `customer_phone`, `customer_postalCode`, `customer_street`, `customer_street1`, `customer_shipping_company`, `customer_shipping_city`, `customer_shipping_countryISO`, `customer_shipping_country`, `customer_shipping_fax`, `customer_shipping_title`, `customer_shipping_firstName`, `customer_shipping_lastName`, `customer_shipping_phone`, `customer_shipping_postalCode`, `customer_shipping_street`, `customer_shipping_street1`, `customer_userIdPlattform`, `payment_method`, `shipping_method`, `expected_delivery_date`, `mediate`, `memo`, `memo_big_account`, `paidSum`, `status`, `status_quote`, `deal_with`, `first_deal_with`, `vat_nr`,  `tax`, `subtract_from_inventory`, `show_customer_name_in_doc`, `afterbuy_account`) values (%f, %f, %d, %d, %s, %s, %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %f, %s, %s, %s, %s, %s, %d, %s, %s, %s)";

        $wpdb->query($wpdb->prepare($sql_order,
            $create_at, $update_at, $create_by, $update_by, $order_id_e2, $order_id_ab, $discount, $discount_abholung, $customer_id, $goodone_customer_mail, $goodone_customer_firstName, $goodone_customer_lastName,
            $customer_company, $customer_city, $customer_countryISO, $customer_country, $customer_fax, $customer_title, $customer_firstName, $customer_lastName,
            $customer_phone, $customer_postalCode, $customer_street, $customer_street1, $customer_shipping_company, $customer_shipping_city, $customer_shipping_countryISO,
            $customer_shipping_country, $customer_shipping_fax, $customer_shipping_title, $customer_shipping_firstName, $customer_shipping_lastName,
            $customer_shipping_phone, $customer_shipping_postalCode, $customer_shipping_street, $customer_shipping_street1, $customer_userIdPlattform,
            $zahlungsmethode, $versandart, $expectedDeliveryDate,
            $mediate, $memo, $memo_big_account, $paidSum, $status, $status_quote, $deal_with, $first_deal_with, $vat_nr, $tax, $subtract_from_inventory, $show_customer_name_in_doc, $afterbuy_account));
        $order_id = $wpdb->insert_id;

        /**
         * 预测好订单ID,就可以写入日志了
         */
        $order_id_in_AB = $order_id_ab;
        $docType = 0;
        $docNameInLog = 'Die Bestellung';
        switch ($deal_with){
            case 'order':
                $docType = 0;
                $docNameInLog = 'Die Bestellung';
                break;
            case 'quote':
                $docType = 1;
                $docNameInLog = 'Das Angebot';
                break;
            case 'ersatzteil':
                $docType = 4;
                $docNameInLog = 'Die Ersatzteilbestellung';
                break;

        }

        $helper->setOperationHistory($order_id, $docNameInLog . ' #' . (intval($order_id) + 3000000) . ' wurde erfolgreich erstellt.', $docType, 0);


        /**
         * 存储 RechnungsID
         */
        $invoiceNr = $orderDetails->invoiceNr;
        $sql_invoiceNr = "INSERT INTO `" . $db_table_document_nrs . "` (`create_at`, `update_at`, `create_by`, `update_by`, `order_id`, `type`, `number`) values (%f, %f, %d, %d, %d, %s, %s)";
        $wpdb->query($wpdb->prepare($sql_invoiceNr, $create_at, $update_at, $create_by, $update_by, $order_id, "Rechnung", $invoiceNr));

        /**
         * 存储Positions
         */
        $soldItems = $orderDetails->soldItems->items;
        $sequence = 0;
        foreach ($soldItems as $sItm){

            $sequence++;

            $ean = $sItm->ean;
            $title = $sItm->title;
            $price = $sItm->price;
            $shipping_cost = $sItm->shipping_cost;
            $quantity_want = $sItm->qInCart;
            $quantity_in_stock = $sItm->quantity;
            $tax = $sItm->tax;
            $reasons = $sItm->reasons;

            $sql_pos = "INSERT INTO `" . $db_table_positions . "` (`create_at`, `update_at`, `create_by`, `update_by`, `order_id`, `sequence`, `ean`, `title`, `price`, `shipping_cost`, `quantity_want`, `quantity_in_stock`, `tax`, `reasons`) values (%f, %f, %d, %d, %d, %d, %s, %s, %f, %f, %d, %d, %d, %s)";
            $wpdb->query($wpdb->prepare($sql_pos, $create_at, $update_at, $create_by, $update_by, $order_id, $sequence, $ean, $title, $price, $shipping_cost, $quantity_want, $quantity_in_stock, $tax, $reasons));

        }

        $isSuccess = true;
        $msg = "Die Bestellung wurde erfolgreich gespeichert.";
        $data = array(
            "create_at" => date("Y-m-d H:i:s", $create_at),
            "order_id" => $order_id
        );

    }else{

        $isSuccess = false;
        $msg = 'Missing parameters!';

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