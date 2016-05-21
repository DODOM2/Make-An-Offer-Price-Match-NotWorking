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
class Jtech_SubmitBestOffer_Block_Submitbestoffer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        
		$this->_objectId = 'offer_id';
        $this->_controller = 'submitbestoffer';
		$this->_blockGroup = 'submitbestoffer';
		//$this->_controller = 'adminhtml_userfiles';

        parent::__construct();

        if ($this->_isAllowedAction('save')) {
            $this->_updateButton('save', 'label', Mage::helper('submitbestoffer')->__('Save offer'));
            $this->_addButton('saveandcontinue', array(
                'label'     => Mage::helper('submitbestoffer')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit(\''.$this->_getSaveAndContinueUrl().'\')',
                'class'     => 'save',
            ), -100);
        } else {
            $this->_removeButton('save');
        }
		
		 if ($this->_isAllowedAction('delete')) {
            $this->_updateButton('delete', 'label', Mage::helper('submitbestoffer')->__('Delete Offer'));
        } else {
            $this->_removeButton('delete');
        }

    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('submitbestoffer_submitbestoffer')->getOfferId()) {
            return Mage::helper('submitbestoffer')->__("Edit Offer #: '%s'", Mage::registry('submitbestoffer_submitbestoffer')->getOfferId());
        }
        else {
            return Mage::helper('submitbestoffer')->__('New Offer');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
		return true;
        // return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'  => true,
            'back'      => 'edit',
            'active_tab'       => '{{tab_id}}'
        ));
    }

    /**
     * @see Mage_Adminhtml_Block_Widget_Container::_prepareLayout()
     */
    protected function _prepareLayout()
    {
        $tabsBlock = $this->getLayout()->getBlock('submitbestoffer_submitbestoffer_edit_tabs');
        if ($tabsBlock) {
			//echo "In if";
            $tabsBlockJsObject = $tabsBlock->getJsObjectName();
            $tabsBlockPrefix = $tabsBlock->getId() . '_';
        } else {
			//echo "In else";
            $tabsBlockJsObject = 'submitbestoffer_tabsJsTabs';
            $tabsBlockPrefix = 'submitbestoffer_tabs_';
        }

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('submitbestoffer_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'submitbestoffer_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'submitbestoffer_content');
                }
            }

            function saveAndContinueEdit(urlTemplate) {
                var tabsIdValue = " . $tabsBlockJsObject . ".activeTab.id;
                var tabsBlockPrefix = '" . $tabsBlockPrefix . "';
                if (tabsIdValue.startsWith(tabsBlockPrefix)) {
                    tabsIdValue = tabsIdValue.substr(tabsBlockPrefix.length)
                }
                var template = new Template(urlTemplate, /(^|.|\\r|\\n)({{(\w+)}})/);
                var url = template.evaluate({tab_id:tabsIdValue});
                editForm.submit(url);
            }
        ";
        return parent::_prepareLayout();
    }


}