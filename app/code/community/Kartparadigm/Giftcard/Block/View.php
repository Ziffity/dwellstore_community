<?php
class Kartparadigm_Giftcard_Block_View extends Mage_Core_Block_Template
{

	public function getGiftcardTrans()
	{
		
		$collection =Mage::getModel('kartparadigm_giftcard/giftcardtrans')->getCollection()
	        ->addFieldToFilter('giftcard_id',$this->getRequest()->getParam('id'));
		return $collection;
	}
}
