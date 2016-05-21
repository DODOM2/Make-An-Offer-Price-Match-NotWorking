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
class Jtech_SubmitBestOffer_Model_Submitbestoffer extends Mage_Core_Model_Abstract
{
    const CACHE_TAG     = 'submitbestoffer';
    protected $_cacheTag= 'submitbestoffer';
	protected $_dataHelper;
	const XML_PATH_PENDING_EMAIL_TEMPLATE = "submitbestoffer/email/pending";
	const XML_PATH_APPROVED_EMAIL_TEMPLATE = "submitbestoffer/email/approved";
	const XML_PATH_DECLINED_EMAIL_TEMPLATE = "submitbestoffer/email/declined";
	
    protected function _construct()
    {
        $this->_init('submitbestoffer/submitbestoffer');
    }
	
	protected function getDataHelper(){
		if(!$this->_dataHelper){
			$this->_dataHelper = Mage::helper('submitbestoffer');
		}
		return $this->_dataHelper;
	}
	
	public function getApprovedOffer($product_id){
		// echo $product_id."<-- product_id <br/>";
		$collection = $this->getCollection()
			->addFieldToFilter('customer_id', $this->getDataHelper()->getCustomerId())
			->addFieldToFilter('product_id', $product_id)
			->addFieldToFilter('status', 'approved')
		;
		if($collection->count()){
			return $collection->getFirstItem();
		}
		return false;
	}
	
	protected function _beforeSave(){
		// $this->checkForAutomateProcess();
		return parent::_beforeSave();
	}
	
	public function checkForAutomateProcess(){
		$product = $this->getDataHelper()->loadProduct($this->getProductId());
		$product_price = ($product->getSpecialPrice()?$product->getSpecialPrice():$product->getPrice());		
		$accept_offer_price = $product->getData('accept_offer_price');
		$decline_offer_price = $product->getData('decline_offer_price');
		$allowed_difference = $this->getDataHelper()->getStoreConfig('automate_difference');
		$offered_price = $this->getData('offered_price');
		// $ignore_allowed_difference = false;
		
		
		/*** 
		$accept_declined_offer_price_difference = number_format($accept_offer_price - $decline_offer_price, 2);
		if($accept_declined_offer_price_difference<0){
			$accept_declined_offer_price_difference = -$accept_declined_offer_price_difference;
		}
		
		// echo $accept_declined_offer_price_difference."==".$allowed_difference."<-- accept_declined_offer_price_difference==allowed_difference";
		if($accept_declined_offer_price_difference==$allowed_difference){
			$allowed_difference = 999999999;
		} ***/
		$checkForDecline = true;
		// echo $product_price."<-- product_price<br/>";
		// echo $accept_offer_price."<-- accept_offer_price<br/>";
		// echo $decline_offer_price."<-- decline_offer_price<br/>";
		// echo $allowed_difference."<-- allowed_difference<br/>";
		
		if($accept_offer_price){
			$accept_offer_price_with_diff = $accept_offer_price - $allowed_difference;
			if($offered_price>=$accept_offer_price_with_diff && $offered_price!=$decline_offer_price){
				$this->setData('status', 'approved');
				$checkForDecline = false;
			}
		/*** 
			$difference = number_format($offered_price - $accept_offer_price, 2);
			// if($difference < 0){
				// $difference = -$difference;
			// }
			// echo "in accept_offer_price <br/>";
			// echo $difference."<-- difference <br/>";
			// if($difference=="0.00" || $difference==$allowed_difference){
			if($difference>="0.00" || $difference==$allowed_difference){
				$this->setData('status', 'approved');
				$checkForDecline = false;
			}
			// echo $this->getData('status')."<-- status <br/>";
			***/
		}
		
		if($decline_offer_price && $checkForDecline){
			$decline_offer_price_with_diff = $decline_offer_price + $allowed_difference;
			if($offered_price<=$decline_offer_price_with_diff){
				$this->setData('status', 'declined');
			}
			
			/**** $difference = number_format($offered_price - $decline_offer_price, 2);
			// if($difference < 0){
				// $difference = -$difference;
			// }
			// echo "in decline_offer_price <br/>";
			// echo $difference."<-- difference <br/>";
			// if($difference=="0.00" || $difference==$allowed_difference){
			if($difference<"0.00" || $difference==$allowed_difference){
				$this->setData('status', 'declined');
			}
			// echo $this->getData('status')."<-- status <br/>";
			****/
		}
		return;
	}
	
	protected function _afterSave(){
		$this->sendNotificationEmail();
		return parent::_afterSave();
	}
	
	public function sendNotificationEmail(){
		// echo "<pre>";
		// print_r($this->getData());
		// echo "</pre>";
		// echo "<pre>";
		// print_r($this->getDataHelper()->getLoggedInCustomer());
		// echo "</pre>";
		
		
		
		// die('sendNotificationEmails');
		$status = $this->getStatus();
		
		if($status=="pending"){
			$email_template_path = Mage::getStoreConfig(self::XML_PATH_PENDING_EMAIL_TEMPLATE);
		}
		
		if($status=="approved"){
			$email_template_path = Mage::getStoreConfig(self::XML_PATH_APPROVED_EMAIL_TEMPLATE);
		}
		
		if($status=="declined"){
			$email_template_path = Mage::getStoreConfig(self::XML_PATH_DECLINED_EMAIL_TEMPLATE);
		}
		
		$translate = Mage::getSingleton('core/translate');
		/* @var $translate Mage_Core_Model_Translate */
		$translate->setTranslateInline(false);
		
		$mailTemplate = Mage::getModel('core/email_template');
		
		$offer_sender = $this->getOfferSender();
		if(!$offer_sender){
			return false;
		}
		
		$offer_receivers = $this->getOfferReceivers();
		if(!$offer_receivers){
			return false;
		}
		
		// add customer to $offer_receivers
		// $offer_receivers[] = $this->getDataHelper()->getLoggedInCustomer()->getEmail();
		$offer_receivers[] = $this->getOfferPlacedCustomer()->getEmail();
		
		foreach($offer_receivers as $offer_receiver){
			/* @var $mailTemplate Mage_Core_Model_Email_Template */
			$mailTemplate->setDesignConfig(array('area' => 'frontend'))
				// ->setReplyTo($post['email'])
				->sendTransactional(
					//Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
					$email_template_path,
					$offer_sender,
					$offer_receiver,
					null,
					array(
						'offer' => $this,
						'customer' => $this->getOfferPlacedCustomer(),
						'product' => $this->getDataHelper()->loadProduct($this->getProductId()),
					)
				);
				
				
				
			if (!$mailTemplate->getSentSuccess()) {
				throw new Exception();
			}
			$translate->setTranslateInline(true);
		}

		return true;
	}
	
	protected function getOfferPlacedCustomer(){
		return Mage::getModel('customer/customer')->load($this->getCustomerId());
	}
	
	public function getOfferSender(){
		$name = $this->getDataHelper()->getStoreConfig('offer_sendername', 'email');
		$email = $this->getDataHelper()->getStoreConfig('offer_sender', 'email');
		if(!$email || !$name){
			return false;
		}
		
		return array(
			'name' => $name,
			'email'	=> $email,
		);
	}
	
	public function getOfferReceivers(){
		$offer_receivers = $this->getDataHelper()->getStoreConfig('offer_receivers', 'email');
		if(!$offer_receivers){
			return false;
		}
		$offer_receivers = explode(",", $offer_receivers);
		return $offer_receivers;
	}
	
	/*public function removeAllExpiredOfferesOld($schedule){
		$currentTimeSeconds = strtotime(now());
		$expireOfferDuration = ($this->getDataHelper()->getStoreConfig('offer_expiration_time'))?$this->getDataHelper()->getStoreConfig('offer_expiration_time'):3600; // in seconds
		$timeOnWhichOfferShouldExpire = $currentTimeSeconds - $expireOfferDuration;
		$readableDateOnWhichOfferShouldExpire = date("Y-m-d H:i:s", $timeOnWhichOfferShouldExpire);
		// echo $readableDateOnWhichOfferShouldExpire."<-- readableDateOnWhichOfferShouldExpire<br/>";
		// echo now()."<--- now <br/>";
		
		$coreResource = Mage::getSingleton('core/resource');
		$tableName = $coreResource->getTableName('tez_submitbestoffer');  
		$query = "DELETE FROM ".$tableName." WHERE created_at<='".$readableDateOnWhichOfferShouldExpire."'";
		// echo $query."<-- query <br/>";
		$write = $coreResource->getConnection('core_write');
		$write->query($query);
		die('All Expired Offeres Removed');
	}*/
	
	public function removeAllExpiredOfferes($schedule){
		$currentTimeSeconds = strtotime(now());
		$expireOfferDuration = ($this->getDataHelper()->getStoreConfig('offer_expiration_time'))?$this->getDataHelper()->getStoreConfig('offer_expiration_time'):3600; // in seconds
		$timeOnWhichOfferShouldExpire = $currentTimeSeconds - $expireOfferDuration;
		$readableDateOnWhichOfferShouldExpire = date("Y-m-d H:i:s", $timeOnWhichOfferShouldExpire);
		
		$collection = Mage::getModel('submitbestoffer/submitbestoffer')->getCollection()
			->addFieldToFilter('created_at', array('date'=>true, 'to' => $readableDateOnWhichOfferShouldExpire))
		;
		
		foreach($collection as $submitbestoffer){
			$customerQuote = Mage::getModel('sales/quote')->getCollection()
				->addFieldToFilter('customer_id', $submitbestoffer->getCustomerId())
				->addFieldToFilter('is_active', 1)
				->getFirstItem()
			;
			
			if($submitbestoffer->getStatus()=="approved"){
				foreach($customerQuote->getAllItems() as $item){
					if($item->getProductId()==$submitbestoffer->getProductId()){
						$customerQuote->removeItem($item->getId());
					}
				}
				$customerQuote->collectTotals()->save();
			}
			$customerQuote = "";
			$submitbestoffer->delete();
		}
		die('All Expired Offeres Removed');
	}
}

?>