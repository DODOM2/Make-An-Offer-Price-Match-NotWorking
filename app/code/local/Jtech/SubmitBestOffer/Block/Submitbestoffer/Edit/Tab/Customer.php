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
class Jtech_SubmitBestOffer_Block_Submitbestoffer_Edit_Tab_Customer extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('SubmitbestofferCustomerGrid');
        $this->setDefaultSort('customer_id');
        $this->setDefaultDir('DESC');
		$this->setUseAjax(true);
        $this->setDefaultFilter(array('entity_id'=>1));
    }

	protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'entity_id') {
            $selectedEntityId = $this->getSelectedCustomerId();
            if (empty($selectedEntityId)) {
                $selectedEntityId = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$selectedEntityId));
            }
            else {
                if($selectedEntityId) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$selectedEntityId));
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
        $collection = Mage::getResourceModel('customer/customer_collection')
					->addNameToSelect();
			   
				// $collection = Mage::getModel('customer/customer')
					// ->getCollection()
					// // ->addAttributeToSelect('1')
					// ->addNameToSelect()
				// ;
               // ->addAttributeToSelect('email')
               // ->addAttributeToSelect('created_at')
               // ->addAttributeToSelect('group_id')
			   // ->addAttributeToSelect('customer_id')
			   // ;
				// echo "<pre>";
				// print_r($collection->getData());
				// echo "</pre>";
				// die("die");
				
				// foreach($collection as $c){
					// echo "<pre>";
					// print_r($c->getData());
					// echo "</pre>";
					// // $c->setCustomerId($c->getEntityId());
					// // echo "<pre>";
					// // print_r($c->getData());
					// // echo "</pre>";
				// }
		
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
			'html_name' => 'customer_id',
            'values'    => $this->getSelectedCustomerId(),
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
		
		$this->addColumn('email', array(
            'header'    => Mage::helper('submitbestoffer')->__('Email'),
            'align'     => 'left',
            'index'     => 'email'
        ));
		
		
        return parent::_prepareColumns();
    }
    /**
     * Row click url
     *
     * @return string
     */
    // public function getRowUrl($row)
    // {
        // return $this->getUrl('*/*/edit', array('customer_id' => $row->getCustomerId()));
    // }
	
	public function getGridUrl()
    {
        return $this->getUrl('*/*/customersGrid', array('offer_id' => Mage::registry('submitbestoffer_submitbestoffer')->getoffer_id()));
    }
	
	
	public function getSelectedCustomerId(){
		$model = Mage::registry('submitbestoffer_submitbestoffer');
		$_customer_id=$model->getCustomerId();
		return array('0'=>$_customer_id);
	}

}