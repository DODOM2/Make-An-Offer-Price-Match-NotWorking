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
class Jtech_SubmitBestOffer_Model_Mysql4_Submitbestoffer_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Declare base table and mapping of some fields
     */
    protected function _construct()
    {
        $this->_init('submitbestoffer/submitbestoffer');
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('offer_id', 'offered_price');
    }	
}