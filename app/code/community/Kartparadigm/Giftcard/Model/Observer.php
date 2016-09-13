<?php
class Kartparadigm_Giftcard_Model_Observer

{
   // ------------------------------ Adding Custom Price To Cart --------------------------------------
   public function changingPrice(Varien_Event_Observer $observer)

   {
      $event = $observer->getEvent();
      $quote_item = $event->getQuoteItem();
      if ($quote_item->getProduct()->getTypeId() === 'giftcard') {
         $new_price = Mage::app()->getRequest()->getPost('customp');
         $data = Mage::app()->getRequest()->getPost();
         if (!is_null($new_price)) {
            $quote_item->setCustomPrice($new_price);
            $quote_item->setOriginalCustomPrice($new_price);
            $quote_item->getProduct()->setIsSuperMode(true);
         }
         else if (($quote_item->getProduct()->getPrice() != $quote_item->getProduct()->getGiftcardValue()) && ($quote_item->getProduct()->getGiftcardValue() != 0)) {
            $quote_item->setCustomPrice($quote_item->getProduct()->getGiftcardValue());
            $quote_item->setOriginalCustomPrice($quote_item->getProduct()->getPrice());
            $quote_item->getProduct()->setIsSuperMode(true);
         }
      }
   }
   // ------------------------------ load product after --------------------------------------
   public function catalogProductLoadAfter(Varien_Event_Observer $observer)

   {
      // set the additional options on the product
      $action = Mage::app()->getFrontController()->getAction();
      if ($action->getFullActionName() == 'checkout_cart_add') {
         // posting our custom form values in an array called extra_options
         if ($options = $action->getRequest()->getParams()) {
            $product = $observer->getProduct();
            // add to the additional options array
            $additionalOptions = array();
            if ($additionalOption = $product->getCustomOption('additional_options')) {
               $additionalOptions = (array)unserialize($additionalOption->getValue());
            }
            foreach($options as $key => $value) {
               if (($key == 'DeliveryDate' || $key == 'Template' || $key == 'SenderName' || $key == 'ReciptentName' || $key == 'RecipientEmail' || $key == 'Address' || $key == 'CustomMessage') && ($value != '')) {
                  /*if($key == 'Template')
                  {
                  continue;
                  }*/
                  $additionalOptions[] = array(
                     'label' => $key,
                     'value' => $value,
                  );
               }
            }
            $observer->getProduct()->addCustomOption('additional_options', serialize($additionalOptions));
         }
      }
   }
   // ------------------------ Persisting Custom Options in order and invoice--------------------------
   public function salesConvertQuoteItemToOrderItem(Varien_Event_Observer $observer)

   {
      $quoteItem = $observer->getItem();
      $codelengh = Mage::getStoreConfig('giftcard/giftcard/txt_clength', Mage::app()->getStore()->getStoreId());
      $dashafter = Mage::getStoreConfig('giftcard/giftcard/txt_dashafter', Mage::app()->getStore()->getStoreId());
      // ---------------------------Generating Giftcard Codes and updating in database-------------------------
      if ($quoteItem->getProduct()->getTypeId() === 'giftcard') {
         $itemcount = $quoteItem->getQty();
         $codes = '';
         for ($i = 0; $i < $itemcount; $i++) {
            $code = Mage::getModel('kartparadigm_giftcard/custommethods')->generateUniqueId($codelengh);
            $code = join('-', str_split($code, $dashafter)); // adding hypen after every 4 letters
            if ($i == 0) $codes = $code;
            else $codes = $codes . ',' . $code;
         }
         $orderItem = $observer->getOrderItem();
         if ($additionalOptions = $quoteItem->getOptionByCode('additional_options')) {
            $quoteitemid = $quoteItem->getItemId();
            $options = $orderItem->getProductOptions();
            $options['additional_options'] = unserialize($additionalOptions->getValue());
            $options['additional_options'][] = array(
               'label' => 'Giftcard Code(s)',
               'value' => $codes,
            );
            $orderItem->setProductOptions($options);
         }
         else {
            $options['additional_options'][] = array(
               'label' => 'Giftcard Code(s)',
               'value' => $codes,
            );
            $orderItem->setProductOptions($options);
         }
         Mage::register($quoteitemid, $codes);
      }
   }
   // ----------------------- applying giftcard redeem amount to cartitems subtotal -----------------------
   public function collectTotals(Varien_Event_Observer $observer)

   {
      $quote = $observer->getEvent()->getQuote();
      $quoteid = $quote->getId();
      $damt = $quote->getGiftcardBalused();
      $damtarr = explode(",", $damt);
      $disamt = array_sum(explode(",", $damt));
      $gbal = $quote->getGiftcardBal();
      $gbalarr = explode(",", $gbal);
      $gcbal = array_sum(explode(",", $gbal));
      $gccode = $quote->getGiftcardCode();
      $discountAmount = $gcbal;
      $disamt = end($damtarr);
      $gcbal = end($gbalarr);
      
        if (Mage::getStoreConfig('giftcard/giftcard/suborgrand_select', Mage::app()->getStore()->getStoreId())) {
      $total = $quote->getGrandTotal(); //apply gc to grandtotal
      if($quote->getShippingAddress()->getShippingAmount()>0){
      if(($gcbal-$disamt)!=0)
            $disamt= $gcbal;
       }
      }
      else $total = $quote->getSubtotal(); //apply gc to subtotal
     
      array_pop($damtarr);
      $damt1 = implode(",", $damtarr);
      array_pop($gbalarr);
      $gbal1 = implode(",", $gbalarr);
      $total = $total - array_sum($damtarr);
      if (isset($gcbal)) {
         $gcnewbal = 0;
         if ($total > $disamt) {
            $discountAmount = $disamt;
            $gcnewbal = $gcbal - $disamt;
         }
         else {
            $discountAmount = $total;
            $gcnewbal = $gcbal - $total;
         }
         if ($damt1 != '') {
            $damt = $damt1 . "," . $discountAmount;
            $gbal = $gbal1 . "," . $gcnewbal;
         }
         else {
            $damt = $discountAmount;
            $gbal = $gcnewbal;
         }
       
         $quote->setGiftcardBalused($damt);
         $discountAmount = array_sum(explode(",", $damt));
      }
      if ($quoteid && $discountAmount > 0) {
         if ($discountAmount > 0) {
            $total = $quote->getBaseSubtotal();
            $quote->setSubtotal(0);
            $quote->setBaseSubtotal(0);
            $quote->setSubtotalWithDiscount(0);
            $quote->setBaseSubtotalWithDiscount(0);
            $quote->setGrandTotal(0);
            $quote->setBaseGrandTotal(0);
            $canAddItems = $quote->isVirtual() ? ('billing') : ('shipping');
            foreach($quote->getAllAddresses() as $address) {
               $address->setSubtotal(0);
               $address->setBaseSubtotal(0);
               $address->setGrandTotal(0);
               $address->setBaseGrandTotal(0);
               $address->collectTotals();
               $quote->setSubtotal((float)$quote->getSubtotal() + $address->getSubtotal());
               $quote->setBaseSubtotal((float)$quote->getBaseSubtotal() + $address->getBaseSubtotal());
               $quote->setSubtotalWithDiscount((float)$quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount());
               $quote->setBaseSubtotalWithDiscount((float)$quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount());
               $quote->setGrandTotal((float)$quote->getGrandTotal() + $address->getGrandTotal());
               $quote->setBaseGrandTotal((float)$quote->getBaseGrandTotal() + $address->getBaseGrandTotal());
               $quote->save();
               $quote->setSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount)->setBaseSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount)->save();
               if ($address->getAddressType() == $canAddItems) {
                  $address->setSubtotalWithDiscount((float)$address->getSubtotalWithDiscount() - $discountAmount);
                  $address->setGrandTotal((float)$address->getGrandTotal() - $discountAmount);
                  $address->setBaseSubtotalWithDiscount((float)$address->getBaseSubtotalWithDiscount() - $discountAmount);
                  $address->setBaseGrandTotal((float)$address->getBaseGrandTotal() - $discountAmount);
                  if ($address->getDiscountDescription()) {
                     $address->setDiscountAmount(-($address->getDiscountAmount() - $discountAmount));
                     $address->setDiscountDescription($address->getDiscountDescription() . ', Giftcard Discount');
                     $address->setBaseDiscountAmount(-($address->getBaseDiscountAmount() - $discountAmount));
                  }
                  else {
                     $address->setDiscountAmount(-($discountAmount));
                     $address->setDiscountDescription('Giftcard ' . $gccode);
                     $address->setBaseDiscountAmount(-($discountAmount));
                  }
                  $address->save();
               }
            }
            foreach($quote->getAllItems() as $item) {
               // We apply discount amount based on the ratio between the GrandTotal and the RowTotal
               $rat = $item->getPriceInclTax() / $total;
               $ratdisc = $discountAmount * $rat;
               $item->setDiscountAmount(($item->getDiscountAmount() + $ratdisc) * $item->getQty());
               $item->setBaseDiscountAmount(($item->getBaseDiscountAmount() + $ratdisc) * $item->getQty())->save();
            }
         }
      }
   }
   // -------------------------- Changing giftcard status at invoice generation----------------------------
   // --------------------------- sales_order_invoice_save_after --------------------------------------------
   public function changingStatus(Varien_Event_Observer $observer)

   {
      $invoice = $observer->getEvent()->getInvoice();
      $order = $invoice->getOrder();
      $orderid = $order->getIncrementId();
      $ordered_items = $order->getAllItems();
      $disamt = $order->getDiscountAmount();
      $date = new Zend_Date(Mage::getModel('core/date')->timestamp());
      $date = $date->toString('Y-M-d H:m:s');
      // ---------------changing giftcard status while redeeming giftcard in order placement------------------
      if (-($disamt) > 0) {
         $collec = Mage::getModel('kartparadigm_giftcard/giftcardtrans')->getCollection()->addFieldToFilter('order_id', $order->getIncrementId());
         foreach($collec as $coll) {
            if ($coll != null) {
               $balused = $coll->getGiftcardBalused();
               $gcbal = $coll->getGiftcardBal();
               if ($gcbal > 0) {
                  $status = 1;
                  $message = 'Admin Processed Invoice and giftcard reactivated';
               }
               else {
                  $status = 3;
                  $message = 'Admin Processed Invoice and giftcard expired';
               }
               $transdata = array(
                  'giftcard_val' => $coll->getGiftcardVal() ,
                  'giftcard_id' => $coll->getGiftcardId() ,
                  'order_id' => $orderid,
                  'giftcard_bal' => $gcbal,
                  'giftcard_balused' => 0,
                  'giftcard_currency' => $coll->getGiftcardCurrency() ,
                  'giftcard_code' => $coll->getGiftcardCode() ,
                  'customer_name' => $coll->getCustomerName() ,
                  'customer_mail' => $coll->getCustomerMail() ,
                  'giftcard_name' => $coll->getGiftcardName() ,
                  'created_date' => $coll->getCreatedDate() ,
                  'expiry_date' => $coll->getExpiryDate() ,
                  'transac_date' => $date,
                  'giftcard_status' => $status,
                  'comment' => $message
               );
               $trasmodel = Mage::getModel('kartparadigm_giftcard/giftcardtrans')->setData($transdata);
               try {
                  $insertId = $trasmodel->save();
               }
               catch(Exception $e) {
               }
               $gc = Mage::getModel('kartparadigm_giftcard/giftcard')->load($coll->getGiftcardCode() , 'giftcard_code');
               $gcid = Mage::getModel('kartparadigm_giftcard/giftcard')->load($gc->getGiftcardId());
               
               $gcid->setGiftcardStatus($status);
               $gcid->save();
            }
         }
      }
      // --------------------------- activating giftcards present in order-------------------------------
      foreach($ordered_items as $item) { //order item detail
         if ($item->getProduct()->getTypeId() === 'giftcard') {
            $writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write');
            $coll = Mage::getModel('kartparadigm_giftcard/giftcard')->getCollection()->addFieldToFilter('gcpro_id', $item->getProduct()->getId())->addFieldToFilter('order_id', $orderid);
            $date = new Zend_Date(Mage::getModel('core/date')->timestamp());
            $date = $date->toString('Y-M-d H:m:s');
            foreach($coll as $col) {
               $transdata = array(
                  'giftcard_val' => $col['giftcard_val'],
                  'giftcard_id' => $col['giftcard_id'],
                  'order_id' => $col['order_id'],
                  'giftcard_bal' => $col['giftcard_bal'],
                  'giftcard_balused' => 0,
                  'giftcard_currency' => $col['giftcard_currency'],
                  'giftcard_code' => $col['giftcard_code'],
                  'customer_name' => $col['receiver_name'],
                  'customer_mail' => $col['receiver_mail'],
                  'giftcard_name' => $col['giftcard_name'],
                  'created_date' => $col['created_date'],
                  'expiry_date' => $col['expiry_date'],
                  'transac_date' => $date,
                  'giftcard_status' => 1, //active
                  'comment' => 'Generated Invoice And Giftcard Activated'
               );
               $trasmodel = Mage::getModel('kartparadigm_giftcard/giftcardtrans')->setData($transdata);
               $insertId = $trasmodel->save();
               if (Mage::getModel('catalog/product')->load($col['gcpro_id'])->getAttributeText('giftcard_type') != 'Physical Giftcard') {
                  // Sending Giftcard Activated Mail to Customer
                  $not = 0;
                  $today = date('Y-m-d');
                  $not = 1;
                  $templateid = Mage::getStoreConfig('giftcard/giftcard/custom_email_template1', Mage::app()->getStore()->getStoreId());
                  $emailTemplate = Mage::getModel('core/email_template');
                  if (!is_numeric($templateid)) {
                     $emailTemplate->loadDefault('custom_email_template1');
                     $emailTemplate->setTemplateSubject("Giftcard Receiverd From " . $col['customer_name']);
                  }
                  else {
                     $emailTemplate->load($templateid);
                  }
                  $type = Mage::getStoreConfig('giftcard/giftcard/giftcard_sender_email', Mage::app()->getStore()->getStoreId());
                  // Create an array of variables to assign to template
                  $row = Mage::getModel('kartparadigm_giftcard/giftcardtemplate')->getCollection()->addFieldToFilter('template_name', $col['template_name'])->getFirstItem();
                  $mediapath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
                  $imgpath="";
                  if (strpos($mediapath, 'localhost') !== false) {
                     $row['theme_color'] = "32943F";
                     $row['text_color'] = "BF0D0D";
                     $imgpath = "http://www.imagesbuddy.com/images/165/smile-greeting-card.jpg";
                  }
                  else if ($row['template_img'] != '') 
                  $imgpath = $mediapath . $row['template_img'];
                  else {
                     $row['theme_color'] = "32943F";
                     $row['text_color'] = "BF0D0D";
                     $product = Mage::getModel('catalog/product')->load($col['gcpro_id']);
                       try{
   		         $full_path_url = Mage::helper('catalog/image')->init($product, 'thumbnail');
			}
		     catch(Exception $e) {
 	     	 	   $full_path_url = $mediapath."giftcard/greet.jpg";
	            }
                     $imgpath = $full_path_url;
                  }
                  $emailTemplateVariables['customername'] = $col['customer_name'];
                  $emailTemplateVariables['giftcardname'] = $col['giftcard_name'];
                  $emailTemplateVariables['giftcardval'] = Mage::helper('core')->currency($col['giftcard_val']);
                  $emailTemplateVariables['giftcardcode'] = $col['giftcard_code'];
                  $emailTemplateVariables['templateimg'] = $imgpath;
                  $emailTemplateVariables['themecolor'] = "#" . $row['theme_color'];
                  $emailTemplateVariables['textcolor'] = "#" . $row['text_color'];
                  $emailTemplateVariables['receivername'] = $col['receiver_name'];
                  $emailTemplateVariables['gcnote'] = $row['giftcard_note'];
                  $emailTemplateVariables['custommsg'] = $col['giftcard_msg'];
                  $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_' . $type . '/name'));
                  $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_' . $type . '/email'));
                  $emailTemplate->send($col['receiver_mail'], $col['receiver_name'], $emailTemplateVariables);
                  $writeConnection->query("Update kartparadigm_giftcard_giftcard set giftcard_status=1, is_notified=1 where	giftcard_id=" . $col['giftcard_id']);
                  $writeConnection->commit();
               }
            }
         }
      }
   }
   // --------------------------------------cancel invoice----------------------------------------------------------
   // ----------------------------------sales_order_payment_cancel--------------------------------------------
   public function kartparadigmCancel(Varien_Event_Observer $observer)

   {
      $order = $observer->getPayment()->getOrder();
      $orderid = $order->getIncrementId();
      $ordered_items = $order->getAllItems();
      $disamt = $order->getDiscountAmount();
      $date = new Zend_Date(Mage::getModel('core/date')->timestamp());
      $date = $date->toString('Y-M-d H:m:s');
      // -----------------changing giftcard status while redeeming giftcard in order placement---------------
      if (-($disamt) > 0) {
         $collec = Mage::getModel('kartparadigm_giftcard/giftcardtrans')->getCollection()->addFieldToFilter('order_id', $order->getIncrementId());
         foreach($collec as $coll) {
            if ($coll != null) {
               $balused = $coll->getGiftcardBalused();
               $gcbal = $coll->getGiftcardBal();
               $updatebal = $gcbal + $balused; // update balance in trans
               if ($updatebal > 0) {
                  $status = 1; //active
                  $message = 'Admin Canceled Invoice and added amount to giftcard';
               }
               else {
                  $status = 3; //redeemed
                  $message = 'Admin Canceled Invoice and giftcard expired';
               }
               $transdata = array(
                  'giftcard_val' => $coll->getGiftcardVal() ,
                  'giftcard_id' => $coll->getGiftcardId() ,
                  'order_id' => $orderid,
                  'giftcard_bal' => $updatebal,
                  'giftcard_balused' => 0,
                  'giftcard_currency' => $coll->getGiftcardCurrency() ,
                  'giftcard_code' => $coll->getGiftcardCode() ,
                  'customer_name' => $coll->getCustomerName() ,
                  'customer_mail' => $coll->getCustomerMail() ,
                  'giftcard_name' => $coll->getGiftcardName() ,
                  'created_date' => $coll->getCreatedDate() ,
                  'expiry_date' => $coll->getExpiryDate() ,
                  'transac_date' => $date,
                  'giftcard_status' => $status,
                  'comment' => $message
               );
               $trasmodel = Mage::getModel('kartparadigm_giftcard/giftcardtrans')->setData($transdata);
               try {
                  $insertId = $trasmodel->save();
               }
               catch(Exception $e) {
               }
               $gc = Mage::getModel('kartparadigm_giftcard/giftcard')->load($coll->getGiftcardCode() , 'giftcard_code');
               $gcid = Mage::getModel('kartparadigm_giftcard/giftcard')->load($gc->getGiftcardId());
               $gcid->setGiftcardBal($updatebal);
               $gcid->setGiftcardStatus($status);
               $gcid->save();
            }
         }
      }
      // ------------------------------- updating giftcard status to canceled---------------------------
      foreach($ordered_items as $item) { //order item detail
         if ($item->getProduct()->getTypeId() === 'giftcard') {
            $writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write');
            $writeConnection->query("Update kartparadigm_giftcard_giftcard set giftcard_status=5 where
		order_id=" . $order->getIncrementId());
            $writeConnection->commit();
            $coll = Mage::getModel('kartparadigm_giftcard/giftcard')->getCollection()->addFieldToFilter('order_id', $order->getIncrementId());
            $date = new Zend_Date(Mage::getModel('core/date')->timestamp());
            $date = $date->toString('Y-M-d H:m:s');
            foreach($coll as $col) {
               $transdata = array(
                  'giftcard_val' => $col['giftcard_val'],
                  'giftcard_id' => $col['giftcard_id'],
                  'order_id' => $col['order_id'],
                  'giftcard_bal' => $col['giftcard_bal'],
                  'giftcard_balused' => 0,
                  'giftcard_currency' => $col['giftcard_currency'],
                  'giftcard_code' => $col['giftcard_code'],
                  'customer_name' => $col['customer_name'],
                  'customer_mail' => $col['customer_mail'],
                  'giftcard_name' => $col['giftcard_name'],
                  'created_date' => $col['created_date'],
                  'expiry_date' => $col['expiry_date'],
                  'transac_date' => $date,
                  'giftcard_status' => 5, //canceled
                  'comment' => 'Invoice Canceled And Giftcard Expired'
               );
               $trasmodel = Mage::getModel('kartparadigm_giftcard/giftcardtrans')->setData($transdata);
               $insertId = $trasmodel->save();
            }
         }
      }
   }
   // ------------------------------- generating giftcard code when buying giftcards-----------------------------
   // -------------------------------sales_model_service_quote_submit_success--------------------------------------
   public function generatecode(Varien_Event_Observer $observer)

   {
      $order = $observer->getEvent()->getOrder();
      $session = Mage::getSingleton('checkout/session');
      $ordered_items = $session->getQuote()->getAllItems();
      $quote = $session->getQuote();
      $orderid = $order->getIncrementId();
      $date = new Zend_Date(Mage::getModel('core/date')->timestamp());
      $date = $date->toString('Y-M-d H:m:s');
      $todate = new Zend_Date(Mage::getModel('core/date')->timestamp());
      $expireafter = Mage::getStoreConfig('giftcard/giftcard/txt_expiry', Mage::app()->getStore()->getStoreId());
      $exdate = $todate->addDay($expireafter)->toString('Y-M-d H:m:s');
      $gccodes = $quote->getGiftcardCode();
      $codearr = explode(",", $gccodes);
      $gcbal = $quote->getGiftcardBal();
      $balarr = explode(",", $gcbal);
      $balused = $quote->getGiftcardBalused();
      $balusedarr = explode(",", $balused);
      $x = 0;
      foreach($codearr as $gccode) {
         $row = Mage::getModel('kartparadigm_giftcard/giftcard')->getCollection()->addFieldToFilter('giftcard_code', $gccode)->addFieldToFilter('giftcard_status', 1)->getFirstItem();
         $status = 2; //processing
	$balgc=$balarr[$x] - $balusedarr[$x];
         $cod = $row['giftcard_code'];
         // -----------------------Redeeming Giftcard and updating the tables -----------------------------------
         if ($cod != "") {
            $transdata = array(
               'giftcard_val' => $row['giftcard_val'],
               'order_id' => $orderid,
               'giftcard_id' => $row['giftcard_id'],
               'giftcard_bal' =>$balgc,
               'giftcard_balused' => $balusedarr[$x],
               'giftcard_code' => $row['giftcard_code'],
               'giftcard_currency' => $row['giftcard_currency'],
               'customer_name' => $row['customer_name'],
               'customer_mail' => $row['customer_mail'],
               'giftcard_name' => $row['giftcard_name'],
               'created_date' => $row['created_date'],
               'expiry_date' => $row['expiry_date'],
               'transac_date' => $date,
               'giftcard_status' => $status,
               'comment' => 'Giftcard used for purchase in store'
            );
            $trasmodel = Mage::getModel('kartparadigm_giftcard/giftcardtrans')->setData($transdata);
            try {
               $insertId = $trasmodel->save()->getId();
            }
            catch(Exception $e) {
            }
            $writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write');
            $writeConnection->query("Update kartparadigm_giftcard_giftcard set 
	giftcard_status=$status,giftcard_bal =$balgc where giftcard_code='" . $cod . "'");
            $writeConnection->commit();
            $x++;
         }
      }
      $codelengh = Mage::getStoreConfig('giftcard/giftcard/txt_clength', Mage::app()->getStore()->getStoreId());
      $dashafter = Mage::getStoreConfig('giftcard/giftcard/txt_dashafter', Mage::app()->getStore()->getStoreId());
      // ---------------------------Generating Giftcard Codes and updating in database-------------------------
      foreach($ordered_items as $item) { //item detail
         if ($item->getProduct()->getTypeId() === 'giftcard') {
            $recipientname = '';
            $recipientmail = '';
            $address = '';
            $deliverydate = '';
            $message = '';
            $sendermail = '';
            $sendername = '';
            if ($additionalOptions = $item->getOptionByCode('additional_options')) {
               $options = $item->getProductOptions();
               $options['additional_options'] = unserialize($additionalOptions->getValue());
               if (count($options['additional_options'] > 0)) {
                  if ($options['additional_options'][1]['value'] != '') {
                     $i = 0;
                     if ($options['additional_options'][0]['label'] == 'Template') $template = $options['additional_options'][$i++]['value'];
                     $sendername = $options['additional_options'][$i++]['value'];
                     $recipientname = $options['additional_options'][$i++]['value'];
                     $recipientmail = $options['additional_options'][$i++]['value'];
                     $message = $options['additional_options'][$i++]['value'];
                     $deliverydate = $options['additional_options'][$i++]['value'];
                     if ($options['additional_options'][$i++]['value'] != "") $address = $options['additional_options'][$i++]['value'];
                     $exdate = date('M-d-Y', strtotime($deliverydate . " +$expireafter days"));
                  }
               }
            }
            $sendermail = $order->getCustomerEmail();
            if (empty($recipientname)) {
               $deliverydate = $date;
               $sendername = $order->getCustomerName();
               $template = $options['additional_options'][0]['value'];
               $recipientname = $sendername;
               $recipientmail = $sendermail;
            }
            $itemcount = $item->getQty();
            $codes = Mage::registry($item->getId());
            Mage::unregister($item->getId());
            $codes = explode(",", $codes);
            for ($i = 0; $i < $itemcount; $i++) {
               $currencycode = Mage::app()->getStore()->getCurrentCurrencyCode();
               $code = $codes[$i];
               $amount = $item->getPrice(); // discount amount
               // getting giftcard value
               $gcvalue = Mage::getModel('catalog/product')->load($item->getProductId())->getGiftcardValue();
               if ($gcvalue > $amount) $bal = $gcvalue;
               else $bal = $amount;
               $proid = $item->getProductId();
               $itemname = $item->getName();
               $comment = 'Giftcard Order Placed By ' . $order->getCustomerName();
               $customername = $order->getCustomerName();
               $data = array(
                  'giftcard_val' => $bal,
                  'order_id' => $orderid,
                  'giftcard_bal' => $bal,
                  'giftcard_code' => $code,
                  'giftcard_currency' => $currencycode,
                  'customer_name' => $sendername,
                  'customer_mail' => $sendermail,
                  'gcpro_id' => $proid,
                  'giftcard_name' => $item->getName() ,
                  'created_date' => $date,
                  'expiry_date' => $exdate,
                  'giftcard_status' => 0,
                  'store_id' => Mage::app()->getStore()->getStoreId() ,
                  'added_by' => $customername,
                  'receiver_name' => $recipientname,
                  'receiver_mail' => $recipientmail,
                  'giftcard_address' => $address,
                  'giftcard_msg' => $message,
                  'delivery_date' => $deliverydate,
                  'is_notified' => 0,
                  'template_name' => $template
               );
               $model = Mage::getModel('kartparadigm_giftcard/giftcard')->setData($data);
               try {
                  $insertId = $model->save()->getId();
                  // inserting in to trans table
                  $transdata = array(
                     'giftcard_val' => $bal,
                     'giftcard_id' => $insertId,
                     'order_id' => $orderid,
                     'giftcard_bal' => $bal,
                     'giftcard_balused' => 0,
                     'giftcard_code' => $code,
                     'giftcard_currency' => $currencycode,
                     'customer_name' => $recipientname,
                     'customer_mail' => $recipientmail,
                     'giftcard_name' => $item->getName() ,
                     'created_date' => $date,
                     'expiry_date' => $exdate,
                     'transac_date' => $date,
                     'giftcard_status' => 0,
                     'comment' => $comment
                  );
                  $trasmodel = Mage::getModel('kartparadigm_giftcard/giftcardtrans')->setData($transdata);
                  $trasmodel->save();
               }
               catch(Exception $e) {
               }
            }
         }
      }
   }
}

