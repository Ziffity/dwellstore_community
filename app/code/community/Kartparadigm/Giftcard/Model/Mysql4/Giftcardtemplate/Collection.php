<?php
class Kartparadigm_Giftcard_Model_Mysql4_Giftcardtemplate_Collection extends
Mage_Core_Model_Mysql4_Collection_Abstract
{
public function _construct()
{
$this->_init('kartparadigm_giftcard/giftcardtemplate');
parent::_construct();
}
}
