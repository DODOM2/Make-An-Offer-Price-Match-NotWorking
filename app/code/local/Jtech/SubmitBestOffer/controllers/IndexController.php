<?php
/************************************************************************
 * 
 * jtechextensions @ J-Tech LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.jtechextensions.com/LICENSE-M1.txt
 *
 * @package    Best Offer
 * @copyright  Copyright (c) 2012-2013 jtechextensions @ J-Tech LLC. (http://www.jtechextensions.com)
 * @license    http://www.jtechextensions.com/LICENSE-M1.txt
************************************************************************/

// class Jtech_SubmitBestOffer_IndexController extends Mage_Core_Controller_Front_Action{
require_once("Mage/Checkout/controllers/CartController.php");
class Jtech_SubmitBestOffer_IndexController extends Mage_Checkout_CartController{

	protected $_product;
	protected $_productId;
	protected $_dataHelper;
	
	protected function getCurrentProductId(){
		if(!$this->_productId){
			$this->_productId = $this->getRequest()->getParam('product_id');
		}
		return $this->_productId;
	}
	
	protected function getCurrentProduct(){
		if(!$this->_product){
			$this->_product = Mage::getModel('catalog/product')->load($this->getCurrentProductId());
		}
		return $this->_product;
	}
	
	public function validateCustomerLogin(){
		if(!Mage::getSingleton('customer/session')->isLoggedIn()){
			$return['success'] = false;
			$return['message'] = $this->getDataHelper()->__('You need to logged in to submit your offer. Please <a href="%s">click here</a> to login.', Mage::getUrl('customer/account/login'));
			// $return['offers_list'] = $this->getOffersListhtml();
			$return['offers_list'] = "";
			echo json_encode($return);
			die();
		}
	}
	
	public function validateCustomerOfferCount(){
		$this->getRequest()->setParam('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId());
		$countLimit = $this->getDataHelper()->getStoreConfig('number_of_allowed_offers');
		if(!$countLimit){
			$countLimit = 3;
		}
		$data = $this->getRequest()->getParams();
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		$collection = Mage::getModel('submitbestoffer/submitbestoffer')
			->getCollection()
			->addFieldToFilter('customer_id', $data['customer_id'])
			->addFieldToFilter('product_id', $data['product_id'])
		;
		
		$count = $collection->count();
		if($count>=$countLimit){
			$return['success'] = false;
			// $return['message'] = $this->getDataHelper()->__('You already reached to limit for placing an offer.');
			$return['message'] = $this->getDataHelper()->getStoreConfig('limit_reached_message');
			$return['offers_list'] = $this->getOffersListhtml();
			echo json_encode($return);
			die();
		}
	}
	
	public function sendbestofferpriceAction(){
		$this->validateCustomerLogin();
		$this->validateCustomerOfferCount();
	
		$data = $this->getRequest()->getParams();
		// $data['customer_id'] = date('Y-m-d');
		// $data['created_at'] = date('Y-m-d');
		$data['created_at'] = now();
		// die(now()."<-- now");
		$data['status'] = 'pending';
		
		if(!empty($data['super_attribute'])) {
			$data['super_attribute'] = json_encode($data['super_attribute']); 
		}
		
		$model = Mage::getModel('submitbestoffer/submitbestoffer');
		$model->setData($data);
		// header('Content-type: application/json');
		
		try {
			// $model->checkForAutomateProcess();
			$model->checkForAutomateProcess();
			$model->save();
			// $model->sendNotificationEmail();
			// $model->sendNotificationEmail();
			$return['success'] = true;
			$return['message'] = $this->getDataHelper()->__('Offer has been declined.');
			// $return['offers_list'] = $this->getLayout()
			$return['offers_list'] = $this->getOffersListhtml();
			if($model->getStatus()=="approved"){
				$return['hide_form'] = 1;
				$approvedOfferMessage = $this->getDataHelper()->getStoreConfig('approved_offer_message');
				$approvedOfferPrice = Mage::helper('core')->formatCurrency($model->getOfferedPrice(), false);
				// APPROVED_OFFER_PRICE
				$approvedOfferMessage = str_replace("APPROVED_OFFER_PRICE", $approvedOfferPrice, $approvedOfferMessage);
				$return['approved_message'] = "<span class='approved_offer_message'>".$approvedOfferMessage."</span>";
				$this->getRequest()->setParam('uenc', "");
				$this->addProductToCartWithApprovedPrice($model, $data['qty']);
				$return['sidebar_cart'] = $this->getLayout()->createBlock('submitbestoffer/checkout_cart_sidebar')->setTemplate('checkout/cart/sidebar.phtml')->toHtml();
				$return['top_cart_link'] = $this->getUpdatedTopCartLinks();
			}else if($model->getStatus()=="pending"){
				$return['message'] = $this->getDataHelper()->__('Offer has been submitted.');
			}
			echo json_encode($return);
		} catch (Exception $e) {
			$return['success'] = false;
			$return['message'] = $e->getMessage();
			$return['offers_list'] = $this->getOffersListhtml();
			echo json_encode($return); 
		}
		die();
	}
	
	protected function getOffersListhtml(){
		return $this->getLayout()
				->createBlock('submitbestoffer/form')
				->setData('product', $this->getCurrentProduct())
				->setTemplate('submitbestoffer/catalog/product/view/addto/offers_list.phtml')->toHtml();
	}
	
	public function getapprovedoffermessageAction(){
		$approvedOffer = Mage::getModel('submitbestoffer/submitbestoffer')->getApprovedOffer($this->getCurrentProductId());
		if($approvedOffer === false){
			$return['success'] = false;
		}else{
			// if($approvedOffer->getId()){
			// echo "<pre>";
			// print_r($approvedOffer);
			// echo "</pre>";
			
			$approvedOfferMessage = $this->getDataHelper()->getStoreConfig('approved_offer_message');
			$approvedOfferPrice = Mage::helper('core')->formatCurrency($approvedOffer->getOfferedPrice(), false);
			// APPROVED_OFFER_PRICE
			$approvedOfferMessage = str_replace("APPROVED_OFFER_PRICE", $approvedOfferPrice, $approvedOfferMessage);
			$return['success'] = true;
			$return['message'] = "<span class='approved_offer_message'>".$approvedOfferMessage."</span>";
			// $return['offers_list'] = $this->getLayout()
			// $return['offers_list'] = $this->getOffersListhtml();
		}
		echo json_encode($return);
		die();
		
		// echo "<pre>";
		// print_r($approvedOffer);
		// echo "</pre>";
		// die();
	}
	
	public function addProductToCartWithApprovedPrice($offeredPrice, $qty){		
		// die('addProductToCartWithApprovedPrice');
		$cart = Mage::getSingleton('checkout/cart');
		// $product = Mage::getModel('catalog/product')->load($offeredPrice->getProductId());
		$params['product'] = $offeredPrice->getProductId(); //$product->getId();
		$params['qty'] = ($qty)?$qty:1;
		// $cart->addProduct($product, $params);
		// $cart->save();
        // Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
	
		$product = $this->_getProduct($offeredPrice->getProductId());
		$request = $this->_getProductRequest($params);
		
		$productId = $product->getId();

        if ($product->getStockItem()) {
            $minimumQty = $product->getStockItem()->getMinSaleQty();
            //If product was not found in cart and there is set minimal qty for it
            if ($minimumQty && $minimumQty > 0 && $request->getQty() < $minimumQty
                && !$this->getQuote()->hasProductId($productId)
            ){
                $request->setQty($minimumQty);
            }
        }

        if ($productId) {
            try {
				// check for same item in cart. if exist then delete it.
				// $this->checkAndDeleteSameProductFromCart($product);
				// echo "<pre>";
				// print_r($product);
				// echo "</pre>";
				// echo "<pre>";
				// print_r($request);
				// echo "</pre>";
				// die('just before add product of quote');
                $result = $this->getQuote()->addProduct($product, $request);
				
				Mage::dispatchEvent('di_submitbestoffer_checkout_cart_product_add_after', array('quote_item' => $result, 'product' => $product));
				// echo "<pre>";
				// print_r($request);
				// echo "</pre>";
				// die('result');
            } catch (Mage_Core_Exception $e) {
                $this->getCheckoutSession()->setUseNotice(false);
                $result = $e->getMessage();
				$return['success'] = false;
				$return['message'] = $result;
				$offeredPrice->delete();
				$return['offers_list'] = $this->getOffersListhtml();
	
				echo json_encode($return);
				die();
				// echo "<pre>";
				// print_r($result);
				// echo "</pre>";
				// die("exception");
            }
		}
		$cart->save();
	}
	
	protected function checkAndDeleteSameProductFromCart($product){
		foreach($this->getQuote()->getAllItems() as $item){
			// echo $item->getProductId()."<-- item-productid and product id ".$product->getId()."<br/>";
			if($item->getProductId()==$product->getId()){
				$this->getQuote()->removeItem($item->getId());
				break;
			}
		}
		// Mage::getSingleton('checkout/cart')->save();
		return;
	}
	
	/**
     * Get request for product add to cart procedure
     *
     * @param   mixed $requestInfo
     * @return  Varien_Object
     */
    protected function _getProductRequest($requestInfo)
    {
        if ($requestInfo instanceof Varien_Object) {
            $request = $requestInfo;
        } elseif (is_numeric($requestInfo)) {
            $request = new Varien_Object(array('qty' => $requestInfo));
        } else {
            $request = new Varien_Object($requestInfo);
        }

        if (!$request->hasQty()) {
            $request->setQty(1);
        }

        return $request;
    }
	
	protected function getCheckoutSession(){
		return Mage::getSingleton('checkout/session');
	}
	
	protected function getQuote(){
		return $this->getCheckoutSession()->getQuote();
		// $quote = Mage::getModel('sales/quote');
		// $customer = $this->getDataHelper()->getLoggedInCustomer();
        // $quote->loadByCustomer($customer);
		// return $quote;
		// return Mage::getSingleton('checkout/cart')->getQuote();
	}
	
	protected function _getProduct($productInfo)
    {
        $product = null;
        if ($productInfo instanceof Mage_Catalog_Model_Product) {
            $product = $productInfo;
        } elseif (is_int($productInfo) || is_string($productInfo)) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productInfo);
        }
        $currentWebsiteId = Mage::app()->getStore()->getWebsiteId();
        if (!$product
            || !$product->getId()
            || !is_array($product->getWebsiteIds())
            || !in_array($currentWebsiteId, $product->getWebsiteIds())
        ) {
            Mage::throwException(Mage::helper('checkout')->__('The product could not be found.'));
        }
        return $product;
    }
	
	public function getUpdatedTopCartLinks(){
		// update mycart top link
		$count = Mage::helper('checkout/cart')->getSummaryCount();
		$cartUrl = Mage::helper('checkout/cart')->getCartUrl();

		if( $count == 1 ) {
			$text = $this->__('My Cart (%s item)', $count);
		} elseif( $count > 0 ) {
			$text = $this->__('My Cart (%s items)', $count);
		} else {
			$text = $this->__('My Cart');
		}
		return $text;
	}
	
	protected function getDataHelper(){
		if(!$this->_dataHelper){
			$this->_dataHelper = Mage::helper('submitbestoffer');
		}
		return $this->_dataHelper;
	}
}