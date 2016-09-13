<?php
class Kartparadigm_Giftcard_Block_Adminhtml_Templates extends Mage_Adminhtml_Block_Widget_Grid_Container

{
     public

     function __construct()
     {
          $this->_controller = 'adminhtml_templates';
          $this->_blockGroup = 'kartparadigm_giftcard';
          $this->_headerText = Mage::helper('kartparadigm_giftcard')->__('Giftcard Template Manager');
          $this->_addButtonLabel = Mage::helper('kartparadigm_giftcard')->__('Add New Template');
          parent::__construct();

          // $this->_removeButton('add');//to remove add new button

     }
}

?>
