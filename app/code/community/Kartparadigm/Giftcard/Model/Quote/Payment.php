<?php

class Kartparadigm_Giftcard_Model_Quote_Payment extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'kartparadigm_giftcard';
    protected $_formBlockType = 'kartparadigm_giftcard/payment_form';
    protected $_infoBlockType = 'kartparadigm_giftcard/payment_info';
    protected $_canUseInternal = false;

    public function isAvailable($quote=null)
    {

        return  $quote->getGiftcardCode()&&($quote->getGrandTotal()==$quote->getGiftcardBalused());
         
    }
}
