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
class Jtech_SubmitBestOffer_Block_Submitbestoffer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('SubmitbestofferGrid');
        $this->setDefaultSort('offer_id');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('submitbestoffer/submitbestoffer')->getCollection();
		$this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        
		$baseUrl = $this->getUrl();

		$this->addColumn('offer_id', array(
            'header'    => Mage::helper('submitbestoffer')->__('ID'),
            'align'     => 'left',
            'index'     => 'offer_id'
        ));
		
		$this->addColumn('customer_name_link', array(
            'header'    => Mage::helper('submitbestoffer')->__('Customer'),
            'sortable'  => false,
            'filter'    => false,
            // 'renderer'  => 'filesubmission/filesubmission_grid_renderer_downloadfile',
            'renderer'  => 'submitbestoffer/submitbestoffer_grid_renderer_customereditlink',
        ));
		
		$this->addColumn('product_name_link', array(
            'header'    => Mage::helper('submitbestoffer')->__('Product'),
            'sortable'  => false,
            'filter'    => false,
            // 'renderer'  => 'filesubmission/filesubmission_grid_renderer_downloadfile',
            'renderer'  => 'submitbestoffer/submitbestoffer_grid_renderer_producteditlink',
        ));
		
		$this->addColumn('offered_price', array(
            'header'    => Mage::helper('submitbestoffer')->__('Offered Price'),
            'align'     => 'left',
            'index'     => 'offered_price'
        ));
		
		$this->addColumn('created_at', array(
            'header'    => Mage::helper('submitbestoffer')->__('Offer Placed On'),
            'align'     => 'left',
			'type'      => 'datetime',
            'index'     => 'created_at'
        ));
		
		$this->addColumn('status', array(
            'header'    => Mage::helper('submitbestoffer')->__('Status'),
            'align'     => 'left',
			'type'		=> 'options',
			/*'options'   => array(
                'pending' => Mage::helper('submitbestoffer')->__('Pending'),
                'approved' => Mage::helper('submitbestoffer')->__('Approved'),
                'declined' => Mage::helper('submitbestoffer')->__('Declined')
            ),*/
			'options'	=> Mage::getModel('submitbestoffer/source_status')->getOptionArray(),
            'index'     => 'status'
        ));
		
		$this->addColumn('action',
            array(
                'header'    => Mage::helper('submitbestoffer')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('submitbestoffer')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'offer_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));	
    }
	
	protected function _prepareMassaction(){
        $this->setMassactionIdField('offer_id');
        $this->getMassactionBlock()->setFormFieldName('offer_ids');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('submitbestoffer')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('submitbestoffer')->__('Are you sure?')
        ));

        return $this;
    }
	
    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('offer_id' => $row->getOfferId()));
    }

}