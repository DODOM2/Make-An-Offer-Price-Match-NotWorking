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
class Jtech_SubmitBestOffer_Block_Submitbestoffer extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
		$this->_controller = 'submitbestoffer';
		$this->_blockGroup = 'submitbestoffer';
		$this->_headerText = Mage::helper('submitbestoffer')->__('Offers');
        $this->_addButtonLabel = Mage::helper('submitbestoffer')->__('Add Offer');
		parent::__construct();
		//$this->_removeButton('add'); 
    }

}