<?php
/*******************************************************************************
 * Copyright 2009-2018 Amazon Services. All Rights Reserved.
 * Licensed under the Apache License, Version 2.0 (the "License"); 
 *
 * You may not use this file except in compliance with the License. 
 * You may obtain a copy of the License at: http://aws.amazon.com/apache2.0
 * This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR 
 * CONDITIONS OF ANY KIND, either express or implied. See the License for the 
 * specific language governing permissions and limitations under the License.
 *******************************************************************************
 * PHP Version 5
 * @category Amazon
 * @package  Marketplace Web Service Orders
 * @version  2013-09-01
 * Library Version: 2018-07-11
 * Generated: Mon Jun 25 18:45:53 UTC 2018
 */

/**
 *  @see GoodOne_Model
 */

require_once (dirname(__FILE__) . '/../Model.php');


/**
 * GoodOne_Model_Order
 * 
 * Properties:
 * <ul>
 * 
 * <li>AmazonOrderId: string</li>
 * <li>SellerOrderId: string</li>
 * <li>PurchaseDate: string</li>
 * <li>LastUpdateDate: string</li>
 * <li>OrderStatus: string</li>
 * <li>FulfillmentChannel: string</li>
 * <li>SalesChannel: string</li>
 * <li>OrderChannel: string</li>
 * <li>ShipServiceLevel: string</li>
 * <li>ShippingAddress: MarketplaceWebServiceOrders_Model_Address</li>
 * <li>OrderTotal: MarketplaceWebServiceOrders_Model_Money</li>
 * <li>NumberOfItemsShipped: int</li>
 * <li>NumberOfItemsUnshipped: int</li>
 * <li>PaymentExecutionDetail: array</li>
 * <li>PaymentMethod: string</li>
 * <li>PaymentMethodDetails: array</li>
 * <li>MarketplaceId: string</li>
 * <li>BuyerEmail: string</li>
 * <li>BuyerName: string</li>
 * <li>BuyerCounty: string</li>
 * <li>BuyerTaxInfo: MarketplaceWebServiceOrders_Model_BuyerTaxInfo</li>
 * <li>ShipmentServiceLevelCategory: string</li>
 * <li>ShippedByAmazonTFM: bool</li>
 * <li>TFMShipmentStatus: string</li>
 * <li>CbaDisplayableShippingLabel: string</li>
 * <li>OrderType: string</li>
 * <li>EarliestShipDate: string</li>
 * <li>LatestShipDate: string</li>
 * <li>EarliestDeliveryDate: string</li>
 * <li>LatestDeliveryDate: string</li>
 * <li>IsBusinessOrder: bool</li>
 * <li>PurchaseOrderNumber: string</li>
 * <li>IsPrime: bool</li>
 * <li>IsPremiumOrder: bool</li>
 * <li>ReplacedOrderId: string</li>
 * <li>IsReplacementOrder: bool</li>
 * <li>PromiseResponseDueDate: string</li>
 * <li>IsEstimatedShipDateSet: bool</li>
 *
 * </ul>
 */

 class MarketplaceWebServiceOrders_Model_Order extends GoodOne_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function my_func(){
        //
    }

}
