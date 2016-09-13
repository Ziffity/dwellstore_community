<?php
class Kartparadigm_Giftcard_Model_Mysql4_Giftcard extends
Mage_Core_Model_Mysql4_Abstract
{
public function _construct()
{
$this->_init('kartparadigm_giftcard/giftcard', 'giftcard_id');
}
}
