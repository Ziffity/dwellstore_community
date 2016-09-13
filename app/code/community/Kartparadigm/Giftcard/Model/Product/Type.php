<?php 

class Kartparadigm_Giftcard_Model_Product_Type
        extends Mage_Catalog_Model_Product_Type_Abstract
{
   
      public function isVirtual($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        $item = Mage::getModel('checkout/session')->getQuote()->getItemByProduct($product);
        if (!$item) {
            return false;
        }
       
	$_product = Mage::getModel('catalog/product')->load($item->getProductId());
	$attributeValue = $_product->getAttributeText('giftcard_type');
	
	if($item->getProductType()=='giftcard' && ($attributeValue=='Physical Giftcard' || $attributeValue=='Mixed Giftcard'))
	       return false;
	else
	       return true;
    }

    
}

