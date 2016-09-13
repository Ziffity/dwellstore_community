<?php
class Kartparadigm_Giftcard_IndexController extends Mage_Core_Controller_Front_Action

{
    public function indexAction()

    {
        $data = $this->getRequest()->getParams();
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $customermail = Mage::getSingleton('customer/session')->getCustomer()->getEmail(); //checkout customer
        $collection = Mage::getModel('kartparadigm_giftcard/giftcard')->getCollection()
        ->addFieldToFilter('giftcard_code', $data['gc_code'])->getFirstItem();
        $hasgiftcards = Mage::getModel('kartparadigm_giftcard/custommethods')->checkCartitems();
        $storeid = Mage::app()->getStore()->getStoreId();
        $canothersbuy = Mage::getStoreConfig('giftcard/giftcard/othersbuy_select', $storeid);

        $canbuygiftcards = Mage::getStoreConfig('giftcard/giftcard/giftcardbuy_select', $storeid);

        $date = new Zend_Date(Mage::getModel('core/date')->timestamp());
        $today = $date->toString('Y-M-d H:m:s'); // present date
        $expireday = $collection['expiry_date'];
        if (Mage::getStoreConfig('giftcard/giftcard/suborgrand_select', Mage::app()->getStore()->getStoreId()))   	  
	$total = $quote->getGrandTotal(); //apply gc to grandtotal
        else $total = $quote->getSubtotal(); //apply gc to subtotal
        $damt = $quote->getGiftcardBalused();
        $damtarr = explode(",", $damt);
        $disamt = array_sum(explode(",", $damt));
        $codesstring=$quote->getGiftcardCode();
        $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
		$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
		
		  $giftbal=Mage::helper('directory')->currencyConvert($collection['giftcard_bal'],$baseCurrencyCode,$currentCurrencyCode);
		
        if($total<=$disamt)
        {
          Mage::getSingleton('core/session')->addNotice("Giftcard cannot be applied to '0' total");
            $this->_redirect('checkout/cart/');
        }
        else if (strpos($codesstring, $data['gc_code']) !== false) {
            Mage::getSingleton('core/session')->addNotice("Giftcard already applied to cart");
            $this->_redirect('checkout/cart/');
        }
        else if ($collection['giftcard_status'] == 2) {
            Mage::getSingleton('core/session')->addNotice("Giftcard in processing stage");
            $this->_redirect('checkout/cart/');
        }
        else if ($collection['giftcard_status'] == 3) {
            Mage::getSingleton('core/session')->addError("Giftcard Redeemed");
            $this->_redirect('checkout/cart/');
        }
        else if (strtotime($expireday) < strtotime($today)) {
            Mage::getSingleton('core/session')->addError("Gift Card Code Expired");
            $this->_redirect('checkout/cart/');
        }
        else if ($hasgiftcards && !$canbuygiftcards) {
            Mage::getSingleton('core/session')->addError("Gift Card codes are not allowed to buy giftcards");
            $this->_redirect('checkout/cart/');
        }
	else if (!$canothersbuy && ($customermail!=$collection['receiver_mail'])) {
            Mage::getSingleton('core/session')->addError("Only Giftcard Customer Can Use The Code");
            $this->_redirect('checkout/cart/');
        }
        else {
            if (isset($giftbal)) {
               
                    // checking giftcard redeem amount isset or apply total giftcard amount
                    if (isset($data['redeem_amt']) && ($data['redeem_amt'] > 0) && ($data['redeem_amt'] <= $giftbal)) $amt = $data['redeem_amt'];
                    else $amt = $giftbal;
                    // checking redeem amount with balance amount in giftcard
                    if ($data['redeem_amt'] > $giftbal) {
                        Mage::getSingleton('core/session')->addError("Insufficient Giftcard Amount. Your Gift Card '" . $data['gc_code'] . "' balance amount is  '" . Mage::getModel('kartparadigm_giftcard/custommethods')
                        ->getBalance($amt, $collection['giftcard_currency']) . "'");
                    }
                    else {
                       
                        if (($quote->getGiftcardCode()) != '') {
                            $quote->setGiftcardCode($quote->getGiftcardCode() . "," . $data['gc_code']);
                            $quote->setGiftcardBal($quote->getGiftcardBal() . "," . $giftbal);
                        
                            $quote->setGiftcardBalused($quote->getGiftcardBalused() . "," . $amt);
                        }
                        else {
                            $quote->setGiftcardCode($data['gc_code']);
                            $quote->setGiftcardBal($giftbal);
                           
                            $quote->setGiftcardBalused($amt);
                        }
                        $quote->save();
                        $bal = Mage::getModel('kartparadigm_giftcard/custommethods')
                        ->getBalance($amt, $collection['giftcard_currency']);
                        Mage::getSingleton('core/session')
                        ->addSuccess("Gift voucher '" . $data['gc_code'] . "' was applied with amount '" . $bal . "' to your order");
                    }
                }
            
            else {
                Mage::getSingleton('core/session')
                ->addError("Gift voucher '" . $data['gc_code'] . "' applied is not valid");
            }
            $this->_redirect('checkout/cart/');
        }
    }
    public function cancelAction()

    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $quote->setGiftcardCode('');
        $quote->setGiftcardBal(0);
        $quote->setGiftcardBalused(0);
        $quote->setGiftcardNewbal(0);
        $quote->save();
        Mage::getSingleton('core/session')->addNotice("Gift voucher removed from your order");
        $this->_redirect('checkout/cart/');
    }
}

