<?php

class Kartparadigm_Giftcard_Block_Payment_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
     
        parent::_construct();
        $this->setTemplate('kartparadigm/giftcard/payment/form.phtml');
    }
    
    public function getComments()
    {
    
        return Mage::getStoreConfig('payment/kartparadigm_giftcard/comments');
    }
}
