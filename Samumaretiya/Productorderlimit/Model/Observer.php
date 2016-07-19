<?php
/* @category    Samumaretiya
 * @package     Samumaretiya_Productorderlimit
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Samumaretiya_Productorderlimit_Model_Observer
{
    public function addStopSelling($observer)
    {
        if(Mage::getStoreconfig('productorderlimit/orderlimitgroups/active')){
            
            $product_id = $observer->getEvent()->getProduct()->getId();
            
            $read = Mage::getSingleton('core/resource')->getConnection('core_read'); 
            
            $results = $read->fetchAll("SELECT sum(qty_ordered)  as `total`, DATE( created_at ) 
            FROM  `sales_flat_order_item` 
            WHERE DATE( created_at ) =  '".date('Y-m-d')."' and product_id = ".$product_id." 
            GROUP BY DATE( created_at ) "); 
            

            $soldOutQuantity = (int)$results[0]['total'];

            $orderedQuantity = Mage::app()->getRequest()->getParams();

            if (isset($orderedQuantity['qty'])) {

                $orderedQuantity = (int)$orderedQuantity['qty'];

            }else{

                $orderedQuantity = 1;
            }

            $IndividualLimit = Mage::getModel('catalog/product')->load($product_id)->getProductorderlimit();

            if(!empty(trim($IndividualLimit))){

                $this->checkproductSaleable($IndividualLimit,$soldOutQuantity,$orderedQuantity);

            }else{
               
                $isGlobalimitapply = Mage::getStoreconfig('productorderlimit/orderlimitgroups/globalproductlimit');

                if($isGlobalimitapply){

                    $globallimit = Mage::getStoreconfig('productorderlimit/orderlimitgroups/allowproductqty');

                    $this->checkproductSaleable((int)$globallimit,$soldOutQuantity,$orderedQuantity);

                }

            }
        }
    }
    public function checkproductSaleable($IndividualLimit,$soldOutQuantity,$orderedQuantity){

        $availableForPurchase = $IndividualLimit - $soldOutQuantity;

        $finalProductLimit = $soldOutQuantity + $orderedQuantity;

        if($finalProductLimit > $IndividualLimit){

            if($availableForPurchase){

                Mage::throwException('Sorry!..There is no stock available for this product in ordered quantity '.$orderedQuantity.'. You can only buy '.$availableForPurchase.' quantity for today.');

            }else{

                Mage::throwException('Sorry!..There is no more stock available for today. You may try by tomorrow or after 24 hours.');
           
            }
        }
    }
    public function checkproductSaleableCheckout($IndividualLimit,$soldOutQuantity,$orderedQuantity,$product_name){

        $availableForPurchase = $IndividualLimit - $soldOutQuantity;

        $finalProductLimit = $soldOutQuantity + $orderedQuantity;

        if($finalProductLimit > $IndividualLimit){

            if($availableForPurchase){

                Mage::throwException('Sorry!..There is no stock available for '.strtoupper($product_name).' product in ordered quantity '.$orderedQuantity.'. You can only buy '.$availableForPurchase.' quantity for today.');

            }else{

                Mage::throwException('Sorry!..There is no enough stock available for some of products in your cart. You may try by tomorrow or after 24 hours.');
           
            }
        }
    }
    public function updateStopSelling($observer)
    {
        if(Mage::getStoreconfig('productorderlimit/orderlimitgroups/active')){
           
            $data = $observer->getEvent()->getInfo();
            
            $cart = Mage::getSingleton('checkout/cart');
            
            foreach ($data as $itemId => $itemInfo) {
                
                $item = $cart->getQuote()->getItemById($itemId);
                
                if (!$item) {
                    continue;
                }

                if (!empty($itemInfo['remove']) || (isset($itemInfo['qty']) && $itemInfo['qty']=='0')) {
                   
                    $this->removeItem($itemId);
                 
                    continue;
                }
              
                $qty = isset($itemInfo['qty']) ? (float) $itemInfo['qty'] : false;

                if ($qty > 0) {

                    $product_id = $item->getData('product_id');
            
                    $read = Mage::getSingleton('core/resource')->getConnection('core_read'); 
                    
                    $results = $read->fetchAll("SELECT sum(qty_ordered)  as `total`, DATE( created_at ) 
                    FROM  `sales_flat_order_item` 
                    WHERE DATE( created_at ) =  '".date('Y-m-d')."' and product_id = ".$product_id." 
                    GROUP BY DATE( created_at ) "); 
                    
                    $soldOutQuantity = (int)$results[0]['total'];


                    $orderedQuantity = $qty;

                    if (isset($orderedQuantity)) {

                        $orderedQuantity = (int)$orderedQuantity;

                    }else{

                        $orderedQuantity = 0;
                    }

                    $IndividualLimit = Mage::getModel('catalog/product')->load($product_id)->getProductorderlimit();

                    if(!empty(trim($IndividualLimit))){
                        
                        $this->checkproductSaleable($IndividualLimit,$soldOutQuantity,$orderedQuantity);

                    }else{

                        $isGlobalimitapply = Mage::getStoreconfig('productorderlimit/orderlimitgroups/globalproductlimit');

                        if($isGlobalimitapply){

                            $globallimit = Mage::getStoreconfig('productorderlimit/orderlimitgroups/allowproductqty');

                            $this->checkproductSaleable((int)$globallimit,$soldOutQuantity,$orderedQuantity);

                        }

                    }
                }
            }
        }
    }
    public function syncOrderStopSelling($observer){

        $cart = Mage::getModel('checkout/cart')->getQuote();

        foreach ($cart->getAllItems() as $item) {

            $product_id = $item->getProduct()->getstockItem()->getProductId();

            $product_name = $item->getProduct()->getstockItem()->getProductName();
            
            $read = Mage::getSingleton('core/resource')->getConnection('core_read'); 
            
            $results = $read->fetchAll("SELECT sum(qty_ordered)  as `total`, DATE( created_at ) 
            FROM  `sales_flat_order_item` 
            WHERE DATE( created_at ) =  '".date('Y-m-d')."' and product_id = ".(int)$product_id." 
            GROUP BY DATE( created_at ) "); 


            $soldOutQuantity = (int)$results[0]['total'];

            $orderedQuantity = $item->getProduct()->getstockItem()->getOrderedItems();

            if (isset($orderedQuantity)) {

                $orderedQuantity = (int)$orderedQuantity;

            }else{

                $orderedQuantity = 1;
            }

            $IndividualLimit = Mage::getModel('catalog/product')->load($product_id)->getProductorderlimit();

            if(!empty(trim($IndividualLimit))){

                $this->checkproductSaleableCheckout($IndividualLimit,$soldOutQuantity,$orderedQuantity,$product_name);

            }else{
               
                $isGlobalimitapply = Mage::getStoreconfig('productorderlimit/orderlimitgroups/globalproductlimit');

                if($isGlobalimitapply){

                    $globallimit = Mage::getStoreconfig('productorderlimit/orderlimitgroups/allowproductqty');

                    $this->checkproductSaleableCheckout((int)$globallimit,$soldOutQuantity,$orderedQuantity,$product_name);

                }

            }
        }
    }
}
