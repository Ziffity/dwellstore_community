<?php
class Kartparadigm_Giftcard_Helper_Data extends Mage_Core_Helper_Abstract
{
    
   public function getStatusarray(){
	$arr=array();
	$arr[0]="Inactive";
	$arr[1]="Active";
	$arr[2]="Processing";
	$arr[3]="Redeemed";
	$arr[4]="Expired";
	$arr[5]="Cancelled";
        return $arr;
   }
   public function getCodeMasked($cc=0)
    {
       // Get the cc Length
	$cc_length = strlen($cc);
	$i=0;
	// Replace all characters of credit card except the last four and dashes
	for($i=$cc_length; $i>9; $i--){
		if((isset($cc[$i])) && ($cc[$i] == '-')){continue;}
		$cc[$i] = 'X';
	}
	// Return the masked Credit Card #
	return $cc;
    }

}
