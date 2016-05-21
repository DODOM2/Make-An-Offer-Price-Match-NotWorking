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
class Jtech_SubmitBestOffer_Controller_Observer
{
	protected $_dataHelper;
	protected function getDataHelper(){
		if(!$this->_dataHelper){
			$this->_dataHelper = Mage::helper('submitbestoffer');
		}
		return $this->_dataHelper;
	}
	
	public function diApprovedOfferPriceSet($observer){
		$event = $observer->getEvent();
		$quote_item = $observer->getquote_item();
		$product = $observer->getproduct();
		// echo get_class($quote_item);
		// echo "<pre>";
		// print_r($quote_item->getData());
		// echo "</pre>";
		
		// $quote_item->setcustom_price('10.00')->save();
		// $quote_item->setprice(10.00)
			// ->setbase_price(10.00)
			// ->save();
		$approvedOfferPrice = $this->getApprovedOfferPrice($product->getId());	
		if($approvedOfferPrice){
			$quote_item->setCustomPrice($approvedOfferPrice);
			$quote_item->setOriginalCustomPrice($approvedOfferPrice);
			$quote_item->getProduct()->setIsSuperMode(true);
		}
		// die(' in observer ');
	}
	
	protected function getApprovedOfferPrice($product_id){
		$submitbestoffer = Mage::getModel('submitbestoffer/submitbestoffer')->getApprovedOffer($product_id);
		if($submitbestoffer){
			return $submitbestoffer->getoffered_price();
		}
		return false;
	}
	
	public function tradeezRemoveUsedOffers($observer){
		$event = $observer->getEvent();
		$order = $observer->getorder();
		$quote = $observer->getquote();
		$product_ids = array();
		$customer_id = $this->getDataHelper()->getCustomerId();
		foreach($order->getAllItems() as $item){
			$product_id = $item->getProductId();
			$product_ids[] = $product_id;
			// echo $product_id."<-- product_id and ".$customer_id."<-- customer_id <br/>";
			// Mage::getModel('submitbestoffer/submitbestoffer')->getCollection()
				// ->addFieldToFilter('customer_id', $customer_id)
				// ->addFieldToFilter('product_id', $product_id)
				// ->delete()
			// ;
		}
		
		$coreResource = Mage::getSingleton('core/resource');
		$tableName = $coreResource->getTableName('tez_submitbestoffer');  
		$query = "DELETE FROM ".$tableName." WHERE customer_id='".$customer_id."' AND product_id IN (".implode(",", $product_ids).")";
		
		$write = $coreResource->getConnection('core_write');
		$write->query($query);
		// echo $query."<-- query <br/>";
		// die('in observer');
		return;
	}
}