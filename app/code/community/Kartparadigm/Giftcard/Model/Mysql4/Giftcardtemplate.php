<?php
class Kartparadigm_Giftcard_Model_Mysql4_Giftcardtemplate extends
Mage_Core_Model_Mysql4_Abstract
{
public function _construct()
{
$this->_init('kartparadigm_giftcard/giftcardtemplate', 'giftcardtemplate_id');
}
}
