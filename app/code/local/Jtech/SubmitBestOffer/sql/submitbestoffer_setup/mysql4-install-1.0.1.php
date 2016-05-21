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
$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup->addAttribute('catalog_product', 'accept_offer_price', array(
	'group'     	=> 'Prices',
	'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Accept Offer Price',
	'backend'       => '',
	'visible'       => 1,
	'required'		=> 0,
	'user_defined' => 0,
	'searchable' => 0,
	'filterable' => 0,
	'comparable'	=> 0,
	'visible_on_front' => 0,
	'visible_in_advanced_search'  => 0,
	'is_html_allowed_on_front' => 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$setup->addAttribute('catalog_product', 'decline_offer_price', array(
	'group'     	=> 'Prices',
	'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Decline Offer Price',
	'backend'       => '',
	'visible'       => 1,
	'required'		=> 0,
	'user_defined' => 0,
	'searchable' => 0,
	'filterable' => 0,
	'comparable'	=> 0,
	'visible_on_front' => 0,
	'visible_in_advanced_search'  => 0,
	'is_html_allowed_on_front' => 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('di_submitbestoffer')};
CREATE TABLE {$this->getTable('di_submitbestoffer')}(
	  `offer_id` int(10) unsigned NOT NULL auto_increment,
	  `customer_id` int(10) unsigned NOT NULL,
	  `product_id` int(10) unsigned NOT NULL,
	  `offered_price` double NOT NULL,
	  `qty` int(5) NOT NULL default 1,
	  `super_attribute` varchar(255) NULL,
	  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
	  `status` varchar(50) NULL,
	   PRIMARY KEY  (`offer_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	");
$installer->endSetup();
?>