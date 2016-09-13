<?php
class Kartparadigm_Giftcard_Adminhtml_TemplateController extends Mage_Adminhtml_Controller_Action

{

 //-------------------------listing all generated template records-------------------------------------------- 
    public function indexAction()

    {
        $this->loadLayout();
        $this->renderLayout();
        return $this;
    }
    
      //----------------------------------------View / Print Template  option ------------------------
    public function viewAction()

    {
        $id = $this->getRequest()->getParam('id', null);
        $row = Mage::getModel('kartparadigm_giftcard/giftcardtemplate')->getCollection()->addFieldToFilter('giftcardtemplate_id', $id)->getFirstItem();
        $mediapath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        if ($row['template_img'] != '') 
        $imgpath = $mediapath . $row['template_img'];
        else $imgpath = "http://www.imagesbuddy.com/images/165/smile-greeting-card.jpg";
        $barcode = $mediapath . "giftcard/bar.gif";
        $arr['customername'] = "Sender";
        $arr['giftcardname'] = "Giftcard Name";
        $arr['giftcardval'] = "$500";
        $arr['giftcardcode'] = "XXXX-XXXX-XXXX";
        $arr['templateimg'] = $imgpath;
        $arr['themecolor'] = "#" . $row['theme_color'];
        $arr['textcolor'] = "#" . $row['text_color'];
        $arr['receivername'] = "Receiver";
        $arr['gcnote'] = $row['giftcard_note'];
        $arr['gcmsg'] = "Giftcard Message By Sender :)";
        $arr['barcode'] = $barcode;
        $this->loadLayout();
        Mage::register("tempvariables", $arr);
        $this->renderLayout();
        return $this;
    }
    
     //----------------------------------------Edit Template option ------------------------
    public function editAction()

    {
        $id = $this->getRequest()->getParam('id', null);
      
        $registry = Mage::getModel('kartparadigm_giftcard/giftcardtemplate');
        if ($id) {
            $registry->load((int)$id);
            if ($registry->getId()) {
                $data = $registry->getData();
                if ($data) {
                    $registry->setData($data)->setId($id);
                }
            }
            else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('kartparadigm_giftcard')
                ->__('The Giftcard Template does not exist'));
                $this->_redirect('*/*/index');
            }
        }
        Mage::register('templates_data', $registry);
        $this->loadLayout();
        // $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }
    
    //---------------------------------------Save Template action-----------------------------------
    public function saveAction()

    {
      
        if ($this->getRequest()->getPost()) {
          
            try {
                $data = $this->getRequest()->getPost();
                $id = $this->getRequest()->getParam('id');
                if ($data && $id) {
                    $registry1 = Mage::getModel('kartparadigm_giftcard/giftcardtemplate')->load($id);
                    $fileName = '';
                    $tempfileName = '';
                  
                    if (isset($_FILES['template_img']['name']) && $_FILES['template_img']['name'] != '' && ($_FILES["template_img"]["size"] < 2000000)) {
                        try {
                            $tempfileName = $_FILES['background_img']['name'];
                            $timestamp = time();
                            $tempfileExt = strtolower(substr(strrchr($tempfileName, ".") , 1));
                            $tempfileNamewoe = rtrim($tempfileName, $tempfileExt);
                            $tempfileName = $timestamp .".". $tempfileExt;
                            $uploader1 = new Varien_File_Uploader('template_img');
                            $uploader1->setAllowRenameFiles(false);
                            $uploader1->setFilesDispersion(false);
                            $path = Mage::getBaseDir('media') . DS . 'giftcard';
                            if (!is_dir($path)) {
                                mkdir($path, 0777, true);
                            }
                            $uploader1->save($path . DS, $tempfileName);
                           
                        }
                        catch(Exception $e) {
                            echo $e->getMessage();
                        }
                    }
                    else {
                    }
                    if ($tempfileName != '') {
                        $data['template_img'] = 'giftcard/' . $tempfileName;
                        unlink(Mage::getBaseDir('media') . DS . $registry1->getTemplateImg());
                    }
                    else {
                        $data['template_img'] = $registry1->getTemplateImg();
                    }
                  
                    $registry1->setData($data)->setId($id);
                    $registry1->save();
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')
                    ->__('Updated Successfully.'));
                }
                else {
                   
                    if (isset($_FILES['template_img']['name']) && $_FILES['template_img']['name'] != '' && ($_FILES["template_img"]["size"] < 2000000)) {
                        try {
                            $tempfileName = $_FILES['template_img']['name'];
                            $tempfileExt = strtolower(substr(strrchr($tempfileName, ".") , 1));
                            $tempfileNamewoe = rtrim($tempfileName, $tempfileExt);
                            $timestamp = time();
                            $tempfileName = $timestamp .".". $tempfileExt;
                            $uploader1 = new Varien_File_Uploader('template_img');
                            $uploader1->setAllowRenameFiles(false);
                            $uploader1->setFilesDispersion(false);
                            $path = Mage::getBaseDir('media') . DS . 'giftcard';
                            if (!is_dir($path)) {
                                mkdir($path, 0777, true);
                            }
                            $uploader1->save($path . DS, $tempfileName);
                           
                        }
                        catch(Exception $e) {
                            echo $e->getMessage();
                        }
                    }
                    else {
                        // $this->_getSession()->addError(Mage::helper('kartparadigm_giftcard')
                        //->__('Error in File Upload.'));
                    }
                    $data = array(
                        'template_name' => $data['template_name'],
                       
                        'theme_color' => $data['theme_color'], //
                        'text_color' => $data['text_color'],
                       
                        'template_img' => 'giftcard/' . $tempfileName,
                        'giftcard_note' => $data['giftcard_note'],
                       
                    );
                  
                    $model = Mage::getModel('kartparadigm_giftcard/giftcardtemplate')->setData($data);
                    $insertId = $model->save()->getId();
                    $arg_attribute = 'giftcard_template';
                    $arg_value = $data['template_name'];
                    $attr_model = Mage::getModel('catalog/resource_eav_attribute');
                    $attr = $attr_model->loadByCode('catalog_product', $arg_attribute);
                    $attr_id = $attr->getAttributeId();
                    $option['attribute_id'] = $attr_id;
                    $option['value']['giftcard_template'][0] = $arg_value;
                    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
                    $setup->addAttributeOption($option);
                   
                    Mage::getSingleton('core/session')->addSuccess("Template Added Succussfully");
                }
                $this->_redirect('*/*/', array(
                    'id' => $this->getRequest()->getParam('gifcardtemplate_id')
                ));
            }
            catch(Exception $e) {
                $this->_getSession()->addError(Mage::helper('kartparadigm_giftcard')
                ->__('An error occurred while saving the Template. Please review the log and try again.'));
                Mage::logException($e);
                $this->_redirect('*/*/', array(
                    'id' => $this->getRequest()->getParam('giftcardtemplate_id')
                ));
                return $this;
            }
        }
    }
    
    //--------------------------------------Add New Template Action-------------------------------------
    public function newAction()

    {
        $this->_redirect('*/*/edit');
    }
    
    //-------------------------------------Delete Template Action--------------------------------------------
    public function deleteAction()

    {
        $registry = Mage::getModel('kartparadigm_giftcard/giftcardtemplate');
        $registryId = $this->getRequest()->getParam('id', null);
        try {
            $registry->load($registryId)->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Template deleted Successfully.'));
        }
        catch(Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/index');
    }
    
    //------------------------------------------Mass delete template action------------------------------
    public function massDeleteAction()

    {
        $registryIds = $this->getRequest()->getParam('templates');
        if (!is_array($registryIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('kartparadigm_giftcard')->__('Please selectone or more giftcards.'));
        }
        else {
            try {
                $registry = Mage::getModel('kartparadigm_giftcard/giftcardtemplate');
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

