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
class Jtech_SubmitBestOffer_Block_Submitbestoffer_Grid_Renderer_Customereditlink extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
		$customer_id = $row->getCustomerId();
		$customer = Mage::getModel('customer/customer')->load($customer_id);
		$output = "<a target='_blank' href='".Mage::helper("adminhtml")->getUrl("adminhtml/customer/edit", array("id"=>$customer_id))."'>".$customer->getFirstname()." ".$customer->getLastname()."</a>";
		return $output;
    }
}
