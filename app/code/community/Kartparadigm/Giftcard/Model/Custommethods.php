<?php
class Kartparadigm_Giftcard_Model_Custommethods
{
   
  
public function generateRule($code, $amount, $label, $from_date,$to_date,$name){
	    $name = (empty($name))? $label : $name;
	    $labels[0] = $label;//default store label
	    $coupon = Mage::getModel('salesrule/rule');
	    $coupon->setName($name)
	    ->setDescription($name)
	    ->setFromDate($from_date)
	    ->setToDate($to_date)
	    ->setCouponCode($code)
	    ->setUsesPerCoupon(10)
	    ->setUsesPerCustomer(1)
	    ->setCustomerGroupIds(1) //an array of customer grou pids
	    ->setIsActive(0)
	   ->setStopRulesProcessing(0)
	    ->setIsAdvanced(1)
	    ->setProductIds('')
	    ->setSortOrder(0)
	    ->setSimpleAction('by_fixed')
	    ->setDiscountAmount($amount)
	    ->setDiscountQty(null)
	    ->setDiscountStep('0')
	    ->setSimpleFreeShipping('0')
	    ->setApplyToShipping('0')
	    ->setIsRss(0)
	    ->setWebsiteIds(1)
	    ->setCouponType(2)
	    ->setStoreLabels($labels);
	    $coupon->save();
}

public function generateUniqueId($length = null){
    $rndId = crypt(uniqid(rand(),1));
    $rndId = strip_tags(stripslashes($rndId));
    $rndId = str_replace(array(".", "$"),"",$rndId);
    $rndId = strrev(str_replace("/","",$rndId));
    if (!is_null($rndId)){
        return strtoupper(substr($rndId, 0, $length));
    }
    return strtoupper($rndId);
}
  

 public function getBalance($bal,$code)
    {
        $store = Mage::app()->getStore();
        if ($store->getCurrentCurrencyCode()===$code) {
            return $store->formatPrice($bal, true, false);
        } else { 
            return $store->convertPrice($bal, true, false);
        }
    }

  public function checkCartitems()
    {
	$cart = Mage::getModel('checkout/cart')->getQuote();
	
	foreach ($cart->getAllItems() as $item) {
	    if ($item->getProduct()->getTypeId() === 'giftcard')
		return true;
	    else
		continue;	
	}
	return false;
    }

public function getConamt($amt,$from,$to){
Mage::log("conver : ".$amt);
    if ($from != $to) {
       $amt = Mage::helper('directory')->currencyConvert($amt, $from, $to);
       return $amt;
       }
    else
     return $amt;
     Mage::log("conver : ".$amt);
}

}
 

 

