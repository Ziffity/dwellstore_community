<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */


require_once 'Mage/Catalog/controllers/ProductController.php';


/**
 * Product controller
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class DWELL_Catalog_ProductController extends Mage_Catalog_ProductController
{

    /**
     * Product view action
     */
    public function viewAction()
    {
        // Get initial data from request
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId  = (int) $this->getRequest()->getParam('id');
        $specifyOptions = $this->getRequest()->getParam('options');
	/* Added by Krishnan */
		$product=Mage::getModel('catalog/product') ->load($productId );
		
		$cats=$product->getCategoryIds();
		$isActive=false;
		foreach ($cats as $category_id) {
		$_cat = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($category_id);
		
			if($_cat->getIsActive())
				$isActive=true;            
		}
		
		if(!$isActive){
			$this->norouteAction();
			return;
		}
	/* Added by Krishnan */	
		
		/****/
		/*
		/* 	DWELL - Redirect to parent product if simple product in order to show options.
		/*
		/****/
		
		/* Get parent product ids */
		$parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($productId);
	
		/* Default to first parent product id */
		if (isset($parentId[0])) {
		
			$childProduct = Mage::getModel('catalog/product')->load($productId);

			$child_product_name = $childProduct->getName();
			$child_product_name_array = explode(' ',$child_product_name);
			
			/* Load parent product */
			$parentProduct = Mage::getModel('catalog/product')->load($parentId[0]);
			$attributeCollection = $parentProduct->getTypeInstance(true)->getUsedProductAttributeIds($parentProduct);
		 	
			$parentProductNameArray = explode(' ',$parentProduct->getName());
			
			/* Add child option to query string to trigger select */
			$options_qs = array();
		
			foreach ($attributeCollection as $at) {
		 		$attr = Mage::getModel('catalog/resource_eav_attribute')->load($at);
		 		$options_qs[$at]=$childProduct->getData($attr->getName());
		 	}
		 	
			$url = $parentProduct->getProductUrl();

			/* Make sure we don't lose any tracking */
			if ($_SERVER['QUERY_STRING']) $url .= '?'.$_SERVER['QUERY_STRING'];
			
			if (!empty($options_qs)) {
				foreach ($options_qs as $k=>$v) $oqs .= urlencode("a_".$k).'='.urlencode($v). "&";
				$oqs = substr($oqs,0,strlen($oqs)-1);
				$url .= $_SERVER['QUERY_STRING'] ? '&'.$oqs:'?'.$oqs;
			}
			
			/* Redirect to parent product url with 301 */
			Mage::app()->getResponse()
				->setRedirect($url,301)
				->sendResponse();
		}
		
        // Prepare helper and params
        $viewHelper = Mage::helper('catalog/product_view');

        $params = new Varien_Object();
        $params->setCategoryId($categoryId);
        $params->setSpecifyOptions($specifyOptions);
		
		
		
        // Render page
        try {
            $viewHelper->prepareAndRender($productId, $this, $params);
        } catch (Exception $e) {
            if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
                if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
                    $this->_redirect('');
                } elseif (!$this->getResponse()->isRedirect()) {
                    $this->_forward('noRoute');
                }
            } else {
                Mage::logException($e);
                $this->_forward('noRoute');
            }
        }
    }

}
