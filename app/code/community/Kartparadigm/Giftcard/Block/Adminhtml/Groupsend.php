<?php
class Kartparadigm_Giftcard_Block_Adminhtml_Groupsend extends Mage_Adminhtml_Block_Widget_Grid_Container
{
public function __construct(){
$this->_controller = 'adminhtml_groupsend';
$this->_blockGroup = 'kartparadigm_giftcard';
$this->_headerText = Mage::helper('kartparadigm_giftcard')->__('Send Giftcard');
$this->_addButtonLabel = Mage::helper('kartparadigm_giftcard')->__('Add New Giftcard'); 
parent::__construct();
$this->_removeButton('add');//to remove add new button
}
}
?>
