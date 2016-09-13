<?php
class Kartparadigm_Giftcard_Block_Adminhtml_Groupsend_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
public function __construct(){
parent::__construct();
$this->_objectId = 'id';

$this->_controller = 'adminhtml_groupsend';
$this->_blockGroup = 'kartparadigm_giftcard';

$this->_mode = 'edit';
$this->_updateButton('save', 'label', Mage::helper('kartparadigm_giftcard')->__('Send Giftcard'));
$this->_updateButton('delete', 'label', Mage::helper('kartparadigm_giftcard')->__('Delete Giftcard'));

 
}
    public function getHeaderText()
    {
        if(Mage::registry('registry_data') && Mage::registry('registry_data')->getId())
return Mage::helper('kartparadigm_giftcard')->__("Edit Giftcard '%s'", $this->htmlEscape(Mage::registry('registry_data')->getGiftcardName()));
return Mage::helper('kartparadigm_giftcard')->__('Send Giftcard');
    }
}

