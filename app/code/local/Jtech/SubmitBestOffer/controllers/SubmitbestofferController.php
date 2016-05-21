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
class Jtech_SubmitBestOffer_SubmitbestofferController extends Mage_Adminhtml_Controller_Action
{
	protected $_customerId;
	protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('di_submitbestoffer/submitbestoffer')
            ->_addBreadcrumb(Mage::helper('submitbestoffer')->__('Best Offer'), Mage::helper('submitbestoffer')->__('Best Offer'))
            ->_addBreadcrumb(Mage::helper('submitbestoffer')->__('Submit Best Offer'), Mage::helper('submitbestoffer')->__('Submit Best Offer'))
        ;
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {	
        $this->_title($this->__('Best Offer'))->_title($this->__('Submit Best Offer'));
        $this->_initAction();
        $this->renderLayout();
    }
	public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }
	public function editAction()
    {
		$this->_title($this->__('Best Offer'))->_title($this->__('Submit Best Offer'));
		$id = $this->getRequest()->getParam('offer_id');
        $model = Mage::getModel('submitbestoffer/submitbestoffer');

		if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('submitbestoffer')->__('This flie no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }
		$this->_title($model->getId() ? $model->getTitle() : $this->__('New Offer'));

        // 3. Set entered data if was error when we do save
		// echo "3<br/>";
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
		//echo "4<br/>";
        Mage::register('submitbestoffer_submitbestoffer', $model);

        // 5. Build edit form
		//echo "5<br/>";
		 $this->_initAction()
            ->_addBreadcrumb($id ? Mage::helper('submitbestoffer')->__('Edit Offer') : Mage::helper('submitbestoffer')->__('New Offer'), $id ? Mage::helper('submitbestoffer')->__('Edit Offer') : Mage::helper('submitbestoffer')->__('New Offer'))
            ->renderLayout();
	}
	 protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
	public function saveAction()
    {	
		$data = $this->getRequest()->getPost();
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// // echo "<pre>";
		// // print_r($_FILES);
		// // echo "</pre>";
		// die();
		
			// $id = $this->getRequest()->getParam('petinfo_id');
			$model = Mage::getModel('submitbestoffer/submitbestoffer')
				//->load($id)
			;
			
			if($this->getRequest()->getPost('offer_id')){
				$model->load($this->getRequest()->getPost('offer_id'));
			}else{
				$data['created_at'] = date('Y-m-d');
			}
			
			// echo "<pre>";
			// print_r($data);
			// echo "</pre>";
			// die();
			$model->setData($data);
			// echo "<pre>";
			// print_r($model->getData());
			// echo "</pre>";
			// die();
			try {
				// $model->checkForAutomateProcess();
                $model->save();
				// echo "<pre>";
				// print_r($model->getData());
				// echo "</pre>";
				if($model->getStatus()=="approved"){
					$this->_customerId = $model->getCustomerId();
					$this->addProductToCartWithApprovedPrice($model);
				}
				
                // display success message
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('submitbestoffer')->__('Offer has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('core/session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('offer_id' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('core/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('core/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('offer_id' => $this->getRequest()->getParam('offer_id')));
                return;
            }
			$this->_redirect('*/*/');
	}
	public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('offer_id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('submitbestoffer/submitbestoffer');
                $model->load($id);
                /*$title = $model->getGalleryName();*/
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('submitbestoffer')->__('The Offer has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('offer_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('submitbestoffer')->__('Unable to find an offer to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }
	
	
	public function massDeleteAction()
    {
        $offer_ids = $this->getRequest()->getParam('offer_ids');
        if (!is_array($offer_ids)) {
            $this->_getSession()->addError($this->__('Please select offer(s).'));
        } else {
            if (!empty($offer_ids)) {
                try {
                    foreach ($offer_ids as $offer_id) {
                        $submitbestoffer = Mage::getModel('submitbestoffer/submitbestoffer')->load($offer_id);
                        // Mage::dispatchEvent('catalog_controller_product_delete', array('product' => $product));
                        $submitbestoffer->delete();
                    }
                    $this->_getSession()->addSuccess(
                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('submitbestoffer')->__('Total of %d record(s) have been deleted.', count($offer_ids)))
                    );
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }
	
	public function addProductToCartWithApprovedPrice($offeredPrice){
		// die('addProductToCartWithApprovedPrice');
		// $cart = Mage::getSingleton('checkout/cart');
		// $product = Mage::getModel('catalog/product')->load($offeredPrice->getProductId());
		$product = Mage::getModel('catalog/product')->load($offeredPrice->getProductId());
		
		$params['product'] = $offeredPrice->getProductId(); //$product->getId();
		$params['qty'] = ($offeredPrice->getQty())?$offeredPrice->getQty():1;
		// $cart->addProduct($product, $params);
		// $cart->save();
        // Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
		if($product->getTypeId() == "configurable") {
			 //$option = array(92 => 128);
			 $offeredPriceModel = Mage::getModel('submitbestoffer/submitbestoffer')->load($offeredPrice->getOfferId());
			 $params['super_attribute'] = json_decode($offeredPriceModel->getData('super_attribute'), true);
		}
		
		$request = $this->_getProductRequest($params);
		
		$productId = $product->getId();
		#echo $productId."<-- productId <br/>";

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
				// echo $offeredPrice->getCustomerId()."<-- customer id";
				$customer = Mage::getModel('customer/customer')->load($offeredPrice->getCustomerId());
				$quote = $this->getQuote();
				// echo $quote->getId()."<-- quote id<br/>";
				$quote->setIgnoreOldQty(true);
				$quote->setIsSuperMode(true);
				$quote->assignCustomer($customer);
				
                $result = $quote->addProduct($product, $request);
				
				if($product->getTypeId() == "configurable") {
					if ($result->getParentItem()) {
						$result = $result->getParentItem();
					}
				}
				
				$result->setCustomPrice($offeredPrice->getoffered_price());
				$result->setOriginalCustomPrice($offeredPrice->getoffered_price());
				$result->getProduct()->setIsSuperMode(true);
				$result->save();
				
				$quote->collectTotals()->save();
				
            } catch (Mage_Core_Exception $e) {
                // $this->getCheckoutSession()->setUseNotice(false);
                // $result = $e->getMessage();
            }
		}
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
		// return $this->getCheckoutSession()->getQuote();
		// $quote = Mage::getModel('sales/quote')->load($this->_customerId, 'customer_id');
		// $quote = Mage::getModel('sales/quote');
		// $customer = Mage::getModel('customer/customer')->load($this->_customerId);
		// // echo $customer->getId()."<-- customer id <br/>";
        // // $quote->loadByCustomer($customer);
        // // $quote->load($this->_customerId, 'customer_id');
		// // $quote = Mage::getModel('sales/quote')->loadByCustomer($customer);
		// $quote = Mage::getResourceModel('sales/quote')->loadByCustomerId($quote, $this->_customerId);
		// echo $quote->getId()."<-- quote id<br/>";
		
		$quote = Mage::getModel('sales/quote')->getCollection()
			->addFieldToFilter('customer_id', $this->_customerId)
			->addFieldToFilter('is_active', 1)
			->getFirstItem()
			;
			// foreach($quoteC as $quote){
				// echo $quoteC->getId()."<-- quote Id <br/>";
			// }
		// die();
		return $quote;
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
	
	public function productsGridAction(){
		$id = $this->getRequest()->getParam('offer_id');
        $model = Mage::getModel('submitbestoffer/submitbestoffer');

        if ($id) {
            $model->load($id);
        }

        Mage::register('submitbestoffer_submitbestoffer', $model);
        $this->getResponse()->setBody($this->getLayout()->createBlock('submitbestoffer/submitbestoffer_edit_tab_product')->toHtml());
	}
	
	public function customersGridAction(){
		$id = $this->getRequest()->getParam('offer_id');
        $model = Mage::getModel('submitbestoffer/submitbestoffer');

        if ($id) {
            $model->load($id);
        }

        Mage::register('submitbestoffer_submitbestoffer', $model);
        $this->getResponse()->setBody($this->getLayout()->createBlock('submitbestoffer/submitbestoffer_edit_tab_customer')->toHtml());
	}
}