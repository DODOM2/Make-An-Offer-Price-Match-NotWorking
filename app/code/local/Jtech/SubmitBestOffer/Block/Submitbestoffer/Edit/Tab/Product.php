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
class Jtech_SubmitBestOffer_Block_Submitbestoffer_Edit_Tab_Product extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('SubmitbestofferProductGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
		$this->setUseAjax(true);
        $this->setDefaultFilter(array('entity_id'=>1));
    }
	
	protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'entity_id') {
            $selectedProductId = $this->getSelectedProductId();
            if (empty($selectedProductId)) {
                $selectedProductId = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$selectedProductId));
            }
            else {
                if($selectedProductId) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$selectedProductId));
                }
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id');

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        // if ($store->getId()) {
        if (false) {
            //$collection->setStoreId($store->getId());
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }
		
		$this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
		$baseUrl = $this->getUrl();
		$this->addColumn('entity_id', array(
            
            'header'    => Mage::helper('submitbestoffer')->__('Assigned'),
            'required'  => true,
			'type'      => 'radio',
			'html_name' => 'product_id',
            'values'    => $this->getSelectedProductId(),
			'align'     => 'center',
            'index'     => 'entity_id'
        ));
		
		
		
		// $this->addColumn('assigned_user_role', array(
            // 'header_css_class' => 'a-center',
            // 'header'    => Mage::helper('adminhtml')->__('Assigned'),
            // 'type'      => 'radio',
            // 'html_name' => 'roles[]',
            // 'values'    => $this->_getSelectedRoles(),
            // 'align'     => 'center',
            // 'index'     => 'role_id'
        // ));
		
		$this->addColumn('name', array(
            'header'    => Mage::helper('submitbestoffer')->__('Name'),
            'align'     => 'left',
            'index'     => 'name'
        ));
		
		$this->addColumn('sku', array(
            'header'    => Mage::helper('submitbestoffer')->__('SKU'),
            'align'     => 'left',
            'index'     => 'sku'
        ));
		
		
        return parent::_prepareColumns();
    }
    
	public function getGridUrl()
    {
        return $this->getUrl('*/*/productsGrid', array('offer_id' => Mage::registry('submitbestoffer_submitbestoffer')->getoffer_id()));
    }
	
	public function getSelectedProductId(){
		$model = Mage::registry('submitbestoffer_submitbestoffer');
		$product_id = $model->getProductId();
		return array('0'=>$product_id);
	}

}