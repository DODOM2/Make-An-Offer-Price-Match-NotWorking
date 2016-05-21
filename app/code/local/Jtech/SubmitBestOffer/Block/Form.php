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
class Jtech_SubmitBestOffer_Block_Form extends Mage_Catalog_Block_Product_View
{	
	protected $_submitBestOfferModel;
	protected $_customerId;
	protected $_loggedInCustomer;
	protected $_dataHelper;
	
	protected function getDataHelper(){
		if(!$this->_dataHelper){
			$this->_dataHelper = Mage::helper('submitbestoffer');
		}
		return $this->_dataHelper;
	}
	
	public function isExtensionEnabled(){
		return $this->getDataHelper()->getStoreConfig('active');
	}
	
	public function getPlacedOffers($_productId){
		$_submitBestOfferModelCollection = $this->getSubmitBestOfferModel()
			->getCollection()
			->addFieldToFilter('customer_id', $this->getCustomerId())
			->addFieldToFilter('product_id', $_productId)
		;
		return $_submitBestOfferModelCollection;
	}
	
	public function getSubmitBestOfferModel(){
		if(!$this->_submitBestOfferModel){
			$this->_submitBestOfferModel = Mage::getModel('submitbestoffer/submitbestoffer');
		}
		return $this->_submitBestOfferModel;
	}
	
	protected function getCustomerId(){
		if(!$this->_customerId){
			// $this->_customerId = $this->getLoggedInCustomer()->getId();
			$this->_customerId = $this->getLoggedInCustomer()->getId();
		}
		return $this->_customerId;
	}
	
	protected function getLoggedInCustomer(){
		if(!$this->_loggedInCustomer){
			// $this->_loggedInCustomer = Mage::getSingleton('customer/session')->getCustomer();
			$this->_loggedInCustomer = $this->getDataHelper()->getLoggedInCustomer();
		}
		return $this->_loggedInCustomer;
	}
	
	public function getFormatedPrice($price){
		return Mage::helper('core')->formatCurrency($price);
	}
	
	public function showSubmitOfferForm($product){
		if($this->getSubmitBestOfferModel()->getApprovedOffer($product->getId())){
			return false;
		}
		return true;
	}
}