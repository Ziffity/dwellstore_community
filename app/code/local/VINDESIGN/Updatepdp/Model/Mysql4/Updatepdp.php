<?php

class VINDESIGN_Updatepdp_Model_Mysql4_Updatepdp extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the updatepdp_id refers to the key field in your database table.
        $this->_init('updatepdp/updatepdp', 'updatepdp_id');
    }
}