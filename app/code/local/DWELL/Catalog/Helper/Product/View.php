<?php

class DWELL_Catalog_Helper_Product_View extends Mage_Catalog_Helper_Product_View
{
   
    public function prepareAndRender($productId, $controller, $params = null)
    {
        // Prepare data
        $productHelper = Mage::helper('catalog/product');
        if (!$params) {
            $params = new Varien_Object();
        }

        // Standard algorithm to prepare and rendern product view page
        $product = $productHelper->initProduct($productId, $controller, $params);
	
        if (!$product) {
            throw new Mage_Core_Exception($this->__('Product is not loaded'), $this->ERR_NO_PRODUCT_LOADED);
        }

		$cat = Mage::getModel('catalog/category')->load(314);
		$subcats = explode(',',$cat->getChildren());
				
		$cats=$product->getCategoryIds();
		$result = array_intersect($subcats, $cats);
		if(count($result)<1){
			// Mage::throwException(Mage::helper('checkout')->__('This item has been discontinued.'));
			$controller->norouteAction();
			return;
		}
		
        $buyRequest = $params->getBuyRequest();
        if ($buyRequest) {
            $productHelper->prepareProductOptions($product, $buyRequest);
        }

        if ($params->hasConfigureMode()) {
            $product->setConfigureMode($params->getConfigureMode());
        }

        Mage::dispatchEvent('catalog_controller_product_view', array('product' => $product));

        if ($params->getSpecifyOptions()) {
            $notice = $product->getTypeInstance(true)->getSpecifyOptionMessage();
            Mage::getSingleton('catalog/session')->addNotice($notice);
        }

        Mage::getSingleton('catalog/session')->setLastViewedProductId($product->getId());

        $this->initProductLayout($product, $controller);

        $controller->initLayoutMessages(array('catalog/session', 'tag/session', 'checkout/session'))
            ->renderLayout();

        return $this;
    }
}
