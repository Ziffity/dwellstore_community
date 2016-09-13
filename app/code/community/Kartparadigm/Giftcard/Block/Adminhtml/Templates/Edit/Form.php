<?php
class Kartparadigm_Giftcard_Block_Adminhtml_Templates_Edit_Form extends Mage_Adminhtml_Block_Widget_Form

{
   protected function _prepareForm()
   {
      $form = new Varien_Data_Form(array(
         'id' => 'edit_form',
         'action' => $this->getUrl('*/*/save', array(
            'id' => $this->getRequest()->getParam('id')
         )) ,
         'method' => 'post',
         'enctype' => 'multipart/form-data'
      ));
      $form->setUseContainer(true);
      $this->setForm($form);
      if (Mage::getSingleton('adminhtml/session')->getFormData()) {
         $data = Mage::getSingleton('adminhtml/session')->getFormData();
         Mage::getSingleton('adminhtml/session')->setFormData(null);
      }
      elseif (Mage::registry('templates_data')) $data = Mage::registry('templates_data')->getData();
      $fieldset = $form->addFieldset('templates_form', array(
         'legend' => Mage::helper('kartparadigm_giftcard')->__('Templates information')
      ));
      $fieldset->addField('template_name', 'text', array(
         'label' => Mage::helper('kartparadigm_giftcard')->__('Template Name ') ,
         'class' => 'required-entry',
         'required' => true,
         'name' => 'template_name',
      ));
     /* $fieldset->addField('template_layout', 'select', array(
         'name' => 'template_layout',
         'label' => Mage::helper('kartparadigm_giftcard')->__('Template Design') ,
         'class' => 'required-entry',
         'required' => true,
         'options' => array(
            //'Center Layout' => Mage::helper('kartparadigm_giftcard')->__('Center Layout') ,
            'Left Layout' => Mage::helper('kartparadigm_giftcard')->__('Left Layout') ,
           // 'Top Layout' => Mage::helper('kartparadigm_giftcard')->__('Top Layout') ,
         ) ,
      ));*/
      $fieldset->addField('theme_color', 'text', array(
         'label' => Mage::helper('kartparadigm_giftcard')->__('Theme Color') ,
         'required' => true,
         'name' => 'theme_color',
         'class' => 'colorpicker',
      ));
        if (isset($data['template_img']) && $data['template_img'] != '') {
         $finderLink1 = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '' . $data['template_img'];
         $finderName1 = $data['template_img'];
         $fieldset->addField('imagelab', 'label', array(
            'label' => Mage::helper('kartparadigm_giftcard')->__('Template Image') ,
            'name' => 'image',
            'value' => $finderLink1,
            'required' => true,
            'after_element_html' => '<img src="' . $finderLink1 . '" alt=" ' . $finderName1 . '" height="50" width="50" /><br/><small>image size 250 x 350</small>',
         ));
         $fieldset->addField('template_img', 'image', array(
            'label' => Mage::helper('kartparadigm_giftcard')->__('Change image') ,
            'required' => false,
            'name' => 'template_img',
         ));
      }
      else {
         $fieldset->addField('template_img', 'image', array(
            'label' => Mage::helper('kartparadigm_giftcard')->__('Template Image') ,
            'required' => false,
            'name' => 'template_img',
               'after_element_html' => '<br/><small>image size 250 x 600</small>',
         ));
      }
    
   /*   if (isset($data['background_img']) && $data['background_img'] != '') {
         $finderLink = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '' . $data['background_img'];
         $finderName = $data['background_img'];
         $fieldset->addField('imagelabel', 'label', array(
            'label' => Mage::helper('kartparadigm_giftcard')->__('Background Image') ,
            'name' => 'image',
            'value' => $finderLink,
            'after_element_html' => '<img src="' . $finderLink . '" alt=" ' . $finderName . '" height="50" width="50" /><br/><small>image size 250 x 350</small>',
         ));
         $fieldset->addField('background_img', 'image', array(
            'label' => Mage::helper('kartparadigm_giftcard')->__('Change image') ,
            'required' => false,
            'name' => 'background_img',
             'after_element_html' => '<br/><small>image size 250 x 600</small>',
         ));
      }
      else {
         $fieldset->addField('background_img', 'image', array(
            'label' => Mage::helper('kartparadigm_giftcard')->__('BackGround Image') ,
            'required' => false,
            'name' => 'background_img',
         ));
      }*/
      $fieldset->addField('text_color', 'text', array(
         'label' => Mage::helper('kartparadigm_giftcard')->__('Text Color') ,
         'required' => true,
         'name' => 'text_color',
         'class' => 'colorpicker',
      ));
      $fieldset->addField('giftcard_note', 'textarea', array(
         'label' => Mage::helper('kartparadigm_giftcard')->__('Notes') ,
         'class' => 'required-entry',
         'required' => true,
         'name' => 'giftcard_note',
	 'value' => 'Please note that: Converting to cash is not allowed. You can use the Gift card code to pay for your order',
'style'=>"height: 6em;",
      ));
    /*  $fieldset->addField('template_status', 'select', array(
         'name' => 'template_status',
         'label' => Mage::helper('kartparadigm_giftcard')->__('Status') ,
         'class' => 'required-entry',
         'required' => true,
         'options' => array(
            1 => Mage::helper('kartparadigm_giftcard')->__('Active') ,
            0 => Mage::helper('kartparadigm_giftcard')->__('Inactive') ,
         ) ,
      ));*/
      $form->setValues($data);
      return parent::_prepareForm();
   }
}

