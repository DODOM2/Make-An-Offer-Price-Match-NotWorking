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
class Jtech_SubmitBestOffer_Block_Submitbestoffer_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

	/**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    protected function _prepareForm()
    {
        /* @var $model Mage_Cms_Model_Page */
        
        // $model = Mage::getModel('core/store')->load($this->getRequest()->getParam('store_id'));
        $model = Mage::registry('submitbestoffer_submitbestoffer');
			
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

		

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('submitbestoffer_');
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('submitbestoffer')->__('Offer Information')));

		// $imageSerializeData = $model->getData('image_data');
		// $imageData = unserialize($imageSerializeData);
		// echo "<pre>";
		// print_r($imageData);
		// echo "</pre>";
        if ($model->getOfferId()) {
            $fieldset->addField('offer_id', 'hidden', array(
                'name' => 'offer_id',
				// 'values' => $this->getRequest()->getParam('store_id')
            ));
        }
		
		// echo "<pre>";
		// print_r($this->getAllAttributes());
		// echo "</pre>";
		
		$fieldset->addField('offered_price', 'text', array(
            'name'      => 'offered_price',
            'label'     => Mage::helper('submitbestoffer')->__('Offered Price'),
            'title'     => Mage::helper('submitbestoffer')->__('Offered Price'),
            'required'  => true,
            'disabled'  => $isElementDisabled,
			
        ));
		
		$fieldset->addField('qty', 'text', array(
            'name'      => 'qty',
            'label'     => Mage::helper('submitbestoffer')->__('Qty'),
            'title'     => Mage::helper('submitbestoffer')->__('Qty'),
            'required'  => true,
            'disabled'  => $isElementDisabled,
			
        ));
		
		
		$fieldset->addField('status', 'select', array(
            'name'      => 'status',
            'label'     => Mage::helper('submitbestoffer')->__('Status'),
            'title'     => Mage::helper('submitbestoffer')->__('Status'),
			'values'	=> Mage::getModel('submitbestoffer/source_status')->toOptionArray(),
			'required'  => true,
            'disabled'  => $isElementDisabled
        ));
		
        if ($model->getOfferId()) {			
			// $model->setCreatedAt(date('Y-m-d H:i:s', strtotime($model->getCreatedAt()));
			// $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
			/*$fieldset->addField('created_at', 'date', array(
				'name'   => 'created_at',
				'label'  => Mage::helper('submitbestoffer')->__('Offer placed on'),
				'title'  => Mage::helper('submitbestoffer')->__('Offer placed on'),
				'image'  => $this->getSkinUrl('images/grid-cal.gif'),
				'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
				'format'       => $dateFormatIso,
			));*/
			$fieldset->addField('created_at', 'label', array(
				'name'   => 'created_at',
				'label'  => Mage::helper('submitbestoffer')->__('Offer placed on'),
				'title'  => Mage::helper('submitbestoffer')->__('Offer placed on'),
			));
		}
		
		$fieldset->addField('super_attribute', 'text', array(
            'name'      => 'super_attribute',
            'label'     => '',
            'title'     => Mage::helper('submitbestoffer')->__('Options'),
            'required'  => false,
            'disabled'  => true,
            'style'  => 'display:none',
			
        ));
		//Mage::dispatchEvent('adminhtml_cms_page_edit_tab_main_prepare_form', array('form' => $form));

        $form->setValues($model);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('petinfo')->__('Petinfo');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('petinfo')->__('Petinfo');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }
	// public function getAllAttributes(){
			// $atttributes= Mage::getModel('catalog/entity_attribute')->getCollection()
					// // ->addFieldToSelect('is_visible_on_front')
					// // ->addFieldToSelect('used_in_product_listing')
				// ; 
			
			// $attributesOptionsArray = array();
			// foreach($atttributes as $atttibute){
				// $atttibute = Mage::getModel('catalog/entity_attribute')->load($atttibute->getId());
				// // echo "<pre>";
				// // print_r($atttibute->getData());
				// // echo "</pre>";						
				// if($atttibute->getis_visible_on_front() || $atttibute->getused_in_product_listing()){			
					// if($atttibute->getfrontend_label()){
						// $attributesOptionsArray[] = array('value' => $atttibute->getAttributeId()."|".$atttibute->getAttributeCode(), 'label'=>$atttibute->getFrontendLabel());
					// }
				// }
			// }
			
			// // echo "<pre>";
			// // print_r($attributesOptionsArray);
			// // echo "</pre>";
			
			// return $attributesOptionsArray;
	// }
	
    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
		return true;
        //return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
    }
}
