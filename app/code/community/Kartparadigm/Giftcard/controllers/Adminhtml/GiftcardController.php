<?php
class Kartparadigm_Giftcard_Adminhtml_GiftcardController extends Mage_Adminhtml_Controller_Action

{
   // -------------------------listing all generated giftcard records--------------------------------------------
   public function indexAction()

   {
      $this->loadLayout();
      $this->renderLayout();
      return $this;
   }
   // ----------------------------------------View / Print Giftcards  option ------------------------
    public function viewAction()

   {
      $id = $this->getRequest()->getParam('id', null);
      $arr = array();
      $col = Mage::getModel('kartparadigm_giftcard/giftcard')->getCollection()->addFieldToFilter('giftcard_id', $id)->getFirstItem();
      $row = Mage::getModel('kartparadigm_giftcard/giftcardtemplate')->getCollection()->addFieldToFilter('template_name', $col['template_name'])->getFirstItem();
      $mediapath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
     
      if ($row['template_img'] != '') 
      $imgpath = $mediapath . $row['template_img'];
      else if($col['gcpro_id']!=''){
        $product = Mage::getModel('catalog/product')->load($col['gcpro_id']);
          try{
   		$imgpath = Mage::helper('catalog/image')->init($product, 'thumbnail');
	}
	catch(Exception $e) {
 	    $imgpath = $mediapath."giftcard/greet.jpg";
	}
          $row['theme_color'] = "32943F";
         $row['text_color'] = "BF0D0D";
      }
      else {
         $imgpath = $mediapath."giftcard/greet.jpg";
         $row['theme_color'] = "32943F";
         $row['text_color'] = "BF0D0D";
      }
      $barcode = $mediapath . "giftcard/bar.gif";
      $arr['customername'] = $col['customer_name'];
      $arr['giftcardname'] = $col['giftcard_name'];
      $arr['giftcardval'] = Mage::helper('core')->currency($col['giftcard_val']);
      $arr['giftcardcode'] = $col['giftcard_code'];
      $arr['templateimg'] = $imgpath;
      $arr['themecolor'] = "#" . $row['theme_color'];
      $arr['textcolor'] = "#" . $row['text_color'];
      $arr['receivername'] = $col['receiver_name'];
      $arr['gcnote'] = $row['giftcard_note'];
      $arr['gcmsg'] = $col['giftcard_msg'];
      $arr['barcode'] = $barcode;
      $this->loadLayout();
      Mage::register("tempvariables", $arr);
      $this->renderLayout();
      return $this;
   }
  // -------------------------------Send giftcards to groups form load action--------------------------------
   public function groupsendAction()

   {
      $this->loadLayout();
      $this->renderLayout();
   }
   // ----------------------------------Sending giftcards send action by admin--------------------------------
   public function sendAction()

   {

      $data = $this->getRequest()->getParams();
      
      $templateid = Mage::getStoreConfig('giftcard/giftcard/admin_giftcard_template');
      $emailTemplate = Mage::getModel('core/email_template');
      if (!is_numeric($templateid)) {
         $emailTemplate->loadDefault('custom_email_template1');
         $emailTemplate->setTemplateSubject("Giftcard From Store");
      }
      else {
         $emailTemplate->load($templateid);
      }
      $type = Mage::getStoreConfig('giftcard/giftcard/giftcard_sender_email');
      
      $lastorderid = Mage::getModel('kartparadigm_giftcard/giftcard')->getCollection()->addFieldToFilter('added_by', 'Admin')->getLastItem()->getOrderId();
      if ($lastorderid) $orderid = $lastorderid + 1; //admin orderid
      else $orderid = 100;
if($data['customer_groups']){
	$customers = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*')
	->addFieldToFilter('group_id', $data['customer_groups']);
      foreach($customers as $customer) {
         $customermail = $customer->getEmail();
         $customername = $customer->getName();
         $msg = $data['giftcard_msg'];
         $date = new Zend_Date(Mage::getModel('core/date')->timestamp());
         $date = $date->toString('Y-M-d H:m:s');
         $exdate = $data['expiry_date'];
         $codelengh = Mage::getStoreConfig('giftcard/giftcard/txt_clength');
         $dashafter = Mage::getStoreConfig('giftcard/giftcard/txt_dashafter');
         $code = Mage::getModel('kartparadigm_giftcard/custommethods')->generateUniqueId($codelengh - 1);
         $code = $code . "A"; //admin giftcade
         $code = join('-', str_split($code, $dashafter));
         $data = array(
            'giftcard_val' => $data['giftcard_val'],
            'giftcard_bal' => $data['giftcard_val'],
            'giftcard_code' => $code,
            'order_id' => $orderid,
            'giftcard_currency' => $data['giftcard_currency'],
            'customer_name' => $customername,
            'customer_mail' => $customermail,
            'receiver_name' => $customername,
            'receiver_mail' => $customermail,
            'giftcard_name' => $data['giftcard_name'],
            'giftcard_msg' => $data['giftcard_msg'],
            'created_date' => $date,
            'store_id' => $data['store_id'],
            'expiry_date' => $exdate,
            'giftcard_status' => $data['giftcard_status'],
            'added_by' => 'Admin',
            'is_notified' => 1,
         );
         $model = Mage::getModel('kartparadigm_giftcard/giftcard')->setData($data);
         $insertId = $model->save()->getId();
           // inserting in to trans table
               $transdata = array(
                  'giftcard_val' => $data['giftcard_val'],
                  'giftcard_id' => $insertId,
                  'order_id' => $orderid, //
                  'giftcard_bal' => $data['giftcard_val'],
                  'giftcard_balused' => 0,
                  'giftcard_code' => $code,
                  'giftcard_currency' => $data['giftcard_currency'],
                  'customer_name' => $data['receiver_name'],
                  'customer_mail' => $data['receiver_mail'],
                  'giftcard_name' => $data['giftcard_name'],
                  'created_date' => $date,
                  'transac_date' => $date,
                  'store_id' => $data['store_id'],
                  'expiry_date' => $exdate,
                  'giftcard_status' => $data['giftcard_status'],
                  'comment' => "Added By Admin"
               );
               $trasmodel = Mage::getModel('kartparadigm_giftcard/giftcardtrans')->setData($transdata);
               $trasmodel->save();
             
         $orderid++;
         $emailTemplateVariables['customername'] = "Admin";
         $emailTemplateVariables['giftcardname'] = $data['giftcard_name'];
         $emailTemplateVariables['giftcardval'] = Mage::app()->getLocale()->currency($data['giftcard_currency'])->getSymbol()." ".$data['giftcard_val'];
         $emailTemplateVariables['giftcardcode'] = $code;
         $mediapath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
         if (strpos($mediapath, 'localhost') !== false) 
         $emailTemplateVariables['templateimg'] = "http://www.imagesbuddy.com/images/165/smile-greeting-card.jpg";
         else 
         $emailTemplateVariables['templateimg'] = $mediapath . "giftcard/greet.jpg";
         $emailTemplateVariables['themecolor'] = "#32943F";
         $emailTemplateVariables['textcolor'] = "#BF0D0D";
         $emailTemplateVariables['receivername'] = $customername;
         $emailTemplateVariables['custommsg'] = $msg;
		 $emailTemplateVariables['code'] = $code;
		 $emailTemplateVariables['balance'] =  Mage::app()->getLocale()->currency($data['giftcard_currency'])->getSymbol()." ".$data['giftcard_val'];
         $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_' . $type . '/name'));
         $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_' . $type . '/email'));
         //$emailTemplate->send($customermail, $customername, $emailTemplateVariables);
		 
		 
		 $recipientName = $customername;
        $recipientEmail = $customermail;
        $recipientStore = $data['store_id'];
        
        $storeId = $data['store_id'];

        $balance = Mage::app()->getLocale()->currency($data['giftcard_currency'])->getSymbol()." ".$data['giftcard_val'];
        $code = $code;

        $balance = Mage::app()->getLocale()->currency($recipientStore->getBaseCurrencyCode())->toCurrency($balance);

        $email = Mage::getModel('core/email_template')->setDesignConfig(array('store' => $storeId));
        $email->sendTransactional(
            Mage::getStoreConfig('giftcard/giftcard/admin_giftcard_template'),
            $recipientEmail,
            $recipientName,
			$code,
			$balance,
            array(
                'name' => $code,
                'code' => $code,
                'balance' => $data['giftcard_val'],
                'store' => $recipientStore,
                'store_name' => $storeId // @deprecated after 1.4.0.0-beta1
            )
        );

        $this->setEmailSent(false);
        if ($email->getSentSuccess()) {
            $this->setEmailSent(true)
                ->setHistoryAction(Enterprise_GiftCardAccount_Model_History::ACTION_SENT)
                ->save();
        }
		
		
      }
}
else{
$customermail = $data['receiver_mail'];
         $customername = $data['receiver_name'];
         $msg = $data['giftcard_msg'];
         $date = new Zend_Date(Mage::getModel('core/date')->timestamp());
         $date = $date->toString('Y-M-d H:m:s');
         $exdate = $data['expiry_date'];
         $codelengh = Mage::getStoreConfig('giftcard/giftcard/txt_clength');
         $dashafter = Mage::getStoreConfig('giftcard/giftcard/txt_dashafter');
         $code = Mage::getModel('kartparadigm_giftcard/custommethods')->generateUniqueId($codelengh - 1);
         $code = $code . "A"; //admin giftcade
         $code = join('-', str_split($code, $dashafter));
         $data = array(
            'giftcard_val' => $data['giftcard_val'],
            'giftcard_bal' => $data['giftcard_val'],
            'giftcard_code' => $code,
            'order_id' => $orderid,
            'giftcard_currency' => $data['giftcard_currency'],
            'customer_name' => $customername,
            'customer_mail' => $customermail,
            'receiver_name' => $customername,
            'receiver_mail' => $customermail,
            'giftcard_name' => $data['giftcard_name'],
            'giftcard_msg' => $data['giftcard_msg'],
            'created_date' => $date,
            'store_id' => $data['store_id'],
            'expiry_date' => $exdate,
            'giftcard_status' => $data['giftcard_status'],
            'added_by' => 'Admin',
            'is_notified' => 1,
         );
         $model = Mage::getModel('kartparadigm_giftcard/giftcard')->setData($data);
         $insertId = $model->save()->getId();
           // inserting in to trans table
               $transdata = array(
                  'giftcard_val' => $data['giftcard_val'],
                  'giftcard_id' => $insertId,
                  'order_id' => $orderid, //
                  'giftcard_bal' => $data['giftcard_val'],
                  'giftcard_balused' => 0,
                  'giftcard_code' => $code,
                  'giftcard_currency' => $data['giftcard_currency'],
                  'customer_name' => $data['receiver_name'],
                  'customer_mail' => $data['receiver_mail'],
                  'giftcard_name' => $data['giftcard_name'],
                  'created_date' => $date,
                  'transac_date' => $date,
                  'store_id' => $data['store_id'],
                  'expiry_date' => $exdate,
                  'giftcard_status' => $data['giftcard_status'],
                  'comment' => "Added By Admin"
               );
               $trasmodel = Mage::getModel('kartparadigm_giftcard/giftcardtrans')->setData($transdata);
               $trasmodel->save();
             
         $orderid++;
         $emailTemplateVariables['customername'] = "Admin";
         $emailTemplateVariables['giftcardname'] = $data['giftcard_name'];
         $emailTemplateVariables['giftcardval'] = Mage::app()->getLocale()->currency($data['giftcard_currency'])->getSymbol()." ".$data['giftcard_val'];
         $emailTemplateVariables['giftcardcode'] = $code;
         $mediapath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
         if (strpos($mediapath, 'localhost') !== false) 
         $emailTemplateVariables['templateimg'] = "http://www.imagesbuddy.com/images/165/smile-greeting-card.jpg";
         else 
         $emailTemplateVariables['templateimg'] = $mediapath . "giftcard/greet.jpg";
         $emailTemplateVariables['themecolor'] = "#32943F";
         $emailTemplateVariables['textcolor'] = "#BF0D0D";
         $emailTemplateVariables['receivername'] = $customername;
         $emailTemplateVariables['custommsg'] = $msg;
		 $emailTemplateVariables['code'] = $code;
		 $emailTemplateVariables['balance'] =  Mage::app()->getLocale()->currency($data['giftcard_currency'])->getSymbol()." ".$data['giftcard_val'];
         $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_' . $type . '/name'));
         $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_' . $type . '/email'));
         $emailTemplate->send($customermail, $customername, $emailTemplateVariables);
      
}
      Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Giftcards Sent Successfully.'));
	  
     $this->_redirect('*/*/');
   }
   // ----------------------------------------Edit giftcard record option for admin--------------------------------
   public function editAction()

   {
      $id = $this->getRequest()->getParam('id', null);
      $registry = Mage::getModel('kartparadigm_giftcard/giftcard');
      if ($id) {
         $registry->load((int)$id);
         if ($registry->getId()) {
            $data = $registry->getData();
            if ($data) {
               $registry->setData($data)->setId($id);
            }
         }
         else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('kartparadigm_giftcard')->__('The Gift Card does not exist'));
            $this->_redirect('*/*/');
         }
      }
      Mage::register('registry_data', $registry);
      $this->loadLayout();
      $this->renderLayout();
   }
   // ------------------------Save Updated/added giftcard record option for admin--------------------------------
   public function saveAction()

   {
      if ($this->getRequest()->getPost()) {
         try {
            $data = $this->getRequest()->getPost();
            $id = $this->getRequest()->getParam('id');
            if ($data && $id) {
               $registry1 = Mage::getModel('kartparadigm_giftcard/giftcard')->load($id); 
               $data['is_notified'] = 1;
               $registry1->setData($data)->setId($id);
               $registry1->save();
               Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Updated Successfully.'));
            }
            else {
               $lastorderid = Mage::getModel('kartparadigm_giftcard/giftcard')->getCollection()->addFieldToFilter('added_by', 'Admin')->getLastItem()->getOrderId();
               if ($lastorderid) $orderid = $lastorderid + 1; //admin orderid
               else $orderid = 100;
               $date = new Zend_Date(Mage::getModel('core/date')->timestamp());
               $date = $date->toString('Y-M-d H:m:s');
               $today = new Zend_Date(Mage::getModel('core/date')->timestamp());
               $expireafter = Mage::getStoreConfig('giftcard/giftcard/txt_expiry');
               $exdate = $today->addDay($expireafter)->toString('Y-M-d H:m:s');
               $codelengh = Mage::getStoreConfig('giftcard/giftcard/txt_clength');
               $dashafter = Mage::getStoreConfig('giftcard/giftcard/txt_dashafter');
               $code = Mage::getModel('kartparadigm_giftcard/custommethods')->generateUniqueId($codelengh - 1);
               $code = $code . "A"; //admin giftcade
               $code = join('-', str_split($code, $dashafter));
               $data = array(
                  'giftcard_val' => $data['giftcard_val'],
                  'order_id' => $orderid, //
                  'giftcard_bal' => $data['giftcard_bal'],
                  'giftcard_code' => $code,
                  'giftcard_currency' => $data['giftcard_currency'],
                  'customer_name' => $data['customer_name'],
                  'customer_mail' => $data['customer_mail'],
                  'receiver_name' => $data['receiver_name'],
                  'receiver_mail' => $data['receiver_mail'],
                  'giftcard_name' => $data['giftcard_name'],
                  'giftcard_msg' => $data['giftcard_msg'],
                  'created_date' => $date,
                  'store_id' => $data['store_id'],
                  'expiry_date' => $exdate,
                  'giftcard_status' => $data['giftcard_status'],
                  'added_by' => 'Admin',
                  'is_notified' => 1
               );
               $model = Mage::getModel('kartparadigm_giftcard/giftcard')->setData($data);
               $insertId = $model->save()->getId();
               // inserting in to trans table
               $transdata = array(
                  'giftcard_val' => $data['giftcard_val'],
                  'giftcard_id' => $insertId,
                  'order_id' => $orderid, //
                  'giftcard_bal' => $data['giftcard_bal'],
                  'giftcard_balused' => 0,
                  'giftcard_code' => $code,
                  'giftcard_currency' => $data['giftcard_currency'],
                  'customer_name' => $data['receiver_name'],
                  'customer_mail' => $data['receiver_mail'],
                  'giftcard_name' => $data['giftcard_name'],
                  'created_date' => $date,
                  'transac_date' => $date,
                  'store_id' => $data['store_id'],
                  'expiry_date' => $exdate,
                  'giftcard_status' => $data['giftcard_status'],
                  'comment' => "Added By Admin"
               );
               $trasmodel = Mage::getModel('kartparadigm_giftcard/giftcardtrans')->setData($transdata);
               $trasmodel->save();
               // Sending Giftcard Activated Mail to Customer
               $templateid = Mage::getStoreConfig('affiliateproduct/giftcard/admin_giftcard_template');
               $emailTemplate = Mage::getModel('core/email_template');
               if (!is_numeric($templateid)) {
                  $emailTemplate->loadDefault('custom_email_template1');
                  $emailTemplate->setTemplateSubject("Giftcard From Store");
               }
               else {
                  $emailTemplate->load($templateid);
               }
               $type = Mage::getStoreConfig('giftcard/giftcard/giftcard_sender_email');
               // Create an array of variables to assign to template
               $emailTemplateVariables['customername'] = $data['customer_name'];;
               $emailTemplateVariables['giftcardname'] = $data['giftcard_name'];
               $emailTemplateVariables['giftcardval'] =Mage::app()->getLocale()->currency($data['giftcard_currency'])->getSymbol()." ".$data['giftcard_val'];
               $emailTemplateVariables['giftcardcode'] = $code;
               $mediapath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
               if (strpos($mediapath, 'localhost') !== false) $emailTemplateVariables['templateimg'] = "http://www.imagesbuddy.com/images/165/smile-greeting-card.jpg";
               else $emailTemplateVariables['templateimg'] = $mediapath . "giftcard/greet.jpg";
               $emailTemplateVariables['themecolor'] = "#32943F";
               $emailTemplateVariables['textcolor'] = "#BF0D0D";
                $emailTemplateVariables['receivername'] = $data['receiver_name'];
               $emailTemplateVariables['custommsg'] = $data['giftcard_msg'];
               $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_' . $type . '/name'));
               $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_' . $type . '/email'));
			   $emailTemplateVariables['code'] = $code;
		 $emailTemplateVariables['balance'] =  Mage::app()->getLocale()->currency($data['giftcard_currency'])->getSymbol()." ".$data['giftcard_val'];
               $emailTemplate->send($data['receiver_mail'], $data['receiver_name'], $emailTemplateVariables);
               Mage::getSingleton('core/session')->addSuccess("Giftcard Added Succussfully");
            }
            $this->_redirect('*/*/', array(
               'id' => $this->getRequest()->getParam('gifcard_id')
            ));
         }
         catch(Exception $e) {
            $this->_getSession()->addError(Mage::helper('kartparadigm_giftcard')->__('An error occurred while saving the Giftcard. Please review the log and try again.'));
            $this->_redirect('*/*/', array(
               'id' => $this->getRequest()->getParam('giftcard_id')
            ));
            return $this;
         }
      }
   }
   // ----------------------------------------Add New Giftcard Option For admin--------------------------------
   public function newAction()

   {
      $this->_redirect('*/*/edit');
   }
   // ----------------------------------------Delete giftcard option for admin--------------------------------
   public function deleteAction()

   {
      $registry = Mage::getModel('kartparadigm_giftcard/giftcard');
      $registryId = $this->getRequest()->getParam('id', null);
      try {
         $registry->load($registryId)->delete();
         Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Giftcard deleted Successfully.'));
      }
      catch(Exception $e) {
         Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
      }
      $this->_redirect('*/*/index');
   }
   // --------------------------------------------Giftcards Mass Delete Option-------------------------------------
   public function massDeleteAction()

   {
      $registryIds = $this->getRequest()->getParam('registries');
      if (!is_array($registryIds)) {
         Mage::getSingleton('adminhtml/session')->addError(Mage::helper('kartparadigm_giftcard')->__('Please selectone or more giftcards.'));
      }
      else {
         try {
            $registry = Mage::getModel('kartparadigm_giftcard/giftcard');
            foreach($registryIds as $registryId) {
               $registry->load($registryId)->delete();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were deleted.', count($registryIds)));
         }
         catch(Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
         }
      }
      $this->_redirect('*/*/index');
   }
}

