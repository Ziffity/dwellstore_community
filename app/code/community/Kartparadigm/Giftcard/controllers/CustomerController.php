<?php
class Kartparadigm_Giftcard_CustomerController extends Mage_Core_Controller_Front_Action
{

// to authenticate only logged in users
 public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();
        $writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write');
         $writeConnection->query("update `kartparadigm_giftcard_giftcard` set giftcard_status=4
                                       WHERE expiry_date <= CURDATE();");
        $writeConnection->commit();
        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }      
 public function balanceAction()
    {
        $handles = array('default');
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $handles[] = 'customer_account';// setting the handle for same layout
        }
      
        $this->loadLayout($handles);
        $this->renderLayout();
    }
    
    
  public function viewAction()
    {
       $id = $this->getRequest()->getParam('id');
       $handles = array('default');
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $handles[] = 'customer_account';// setting the handle for same layout
        }
        $this->loadLayout($handles);
        $this->renderLayout();
    }
    
    public function shareviewAction()
    {
    
       $handles = array('default');
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $handles[] = 'customer_account';// setting the handle for same layout
        }
        $this->loadLayout($handles);
        $this->renderLayout();
    }

    
    public function shareAction()
    {
       $data = $this->getRequest()->getParams();
       Mage::log($data,null,'logfile.log');
       $handles = array('default');
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $handles[] = 'customer_account';// setting the handle for same layout
            $gcrecord=Mage::getModel('kartparadigm_giftcard/giftcard')->load($data['gcid']);
            $gcrecord->setReceiverMail($data['Sharingmail']);
            try{
            $gcrecord->save();
             Mage::getSingleton('core/session')->addSuccess("Giftcard Shared Succussfully");
	      }
	      catch(exception $e)
          {
	      Mage::getSingleton('core/session')->addFailure("Problem In Sharing Giftcard");
	      }
        
        }
        $this->loadLayout($handles);
        $this->renderLayout();
    }


}
