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
class Jtech_SubmitBestOffer_Block_Submitbestoffer_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('submitbestoffer_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('submitbestoffer')->__('Offer'));
    }
	
	protected function _beforeToHtml()
    {
        $this->addTab('main_section', array(
            'label'     => Mage::helper('submitbestoffer')->__('Offer'),
            'title'     => Mage::helper('submitbestoffer')->__('Offer'),
            'content'   => $this->getLayout()->createBlock('submitbestoffer/submitbestoffer_edit_tab_main')->toHtml(),
            'active'    => true
        ));

        $this->addTab('customer_section', array(
            'label'     => Mage::helper('submitbestoffer')->__('Customer'),
            'title'     => Mage::helper('submitbestoffer')->__('Customer'),
            'content'   => $this->getLayout()->createBlock('submitbestoffer/submitbestoffer_edit_tab_customer')->toHtml(),
        ));
		
		$this->addTab('product_section', array(
            'label'     => Mage::helper('submitbestoffer')->__('Product'),
            'title'     => Mage::helper('submitbestoffer')->__('Product'),
            'content'   => $this->getLayout()->createBlock('submitbestoffer/submitbestoffer_edit_tab_product')->toHtml(),
        ));
		
        return parent::_beforeToHtml();
    }
}