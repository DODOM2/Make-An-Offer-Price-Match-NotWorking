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
class Jtech_SubmitBestOffer_Model_Source_Status
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'pending', 'label'=>Mage::helper('submitbestoffer')->__('Pending')),
            array('value' => 'approved', 'label'=>Mage::helper('submitbestoffer')->__('Approved')),
            array('value' => 'declined', 'label'=>Mage::helper('submitbestoffer')->__('Declined')),
        );
    }
	public function getOptionArray()
    {
        return array(
            'pending' => Mage::helper('submitbestoffer')->__('Pending'),
			'approved' => Mage::helper('submitbestoffer')->__('Approved'),
			'declined' => Mage::helper('submitbestoffer')->__('Declined')
        );
    }

}
?>