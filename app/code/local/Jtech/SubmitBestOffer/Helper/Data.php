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
class Jtech_SubmitBestOffer_Helper_Data extends Mage_Core_Helper_Abstract{
	
	protected $_storeConfigMain = "submitbestoffer";
	protected $_storeConfigMiddle = "basic_settings";
	protected $_customerId;
	protected $_loggedInCustomer;
	
	public function getStoreConfig($node, $_storeConfigMiddle=""){
		// $_storeConfigPath = "submitbestoffer/basic_settings/";
		// return Mage::getStoreConfig($_storeConfigPath.$node);
		if($_storeConfigMiddle==""){
			$_storeConfigMiddle = $this->_storeConfigMiddle;
		}
		// return Mage::getStoreConfig($this->_storeConfigPath.$node);
		return Mage::getStoreConfig($this->_storeConfigMain."/".$_storeConfigMiddle."/".$node);
	}
	
	public function getCustomerId(){
		if(!$this->_customerId){
			$this->_customerId = $this->getLoggedInCustomer()->getId();
		}
		return $this->_customerId;
	}
	
	public function getLoggedInCustomer(){
		if(!$this->_loggedInCustomer){
			$this->_loggedInCustomer = Mage::getSingleton('customer/session')->getCustomer();
		}
		return $this->_loggedInCustomer;
	}
	
	public function loadProduct($product_id){
		return Mage::getModel('catalog/product')->load($product_id);
	}
}
?>