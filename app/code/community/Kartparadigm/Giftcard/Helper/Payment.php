<?php

class Kartparadigm_Giftcard_Helper_Payment extends Mage_Payment_Helper_Data
{
   
    public function getStoreMethods($store=null, $quote=null)
    {

        $fullyPaidByGiftcard = (($quote->getGrandTotal()==$quote->getGiftcardBalused())&&($quote->getGiftcardCode()!='')); 


        if ($fullyPaidByGiftcard) {
            return array(Mage::getModel(Mage::getStoreConfig(self::XML_PATH_PAYMENT_METHODS.'/kartparadigm_giftcard/model', $store)));
        }
        return parent::getStoreMethods($store, $quote);
    }
}
