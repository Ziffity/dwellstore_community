<?php
class Kartparadigm_Giftcard_Block_Balance extends Mage_Core_Block_Template
{

	public function getCustomerGiftcards()
	{
		$collection = null;
		$currentCustomer =Mage::getSingleton('customer/session')->getCustomer();
	
		if($currentCustomer)
		{
		$collection =Mage::getModel('kartparadigm_giftcard/giftcard')->getCollection()
		->addFieldToFilter('receiver_mail',$currentCustomer->getEmail())->addOrder('giftcard_id','DESC');
		}
		return $collection;
	}
}
