<?php

class Kartparadigm_Giftcard_Block_Adminhtml_Catalog_Form_Renderer_Fieldset_Element extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('giftcard/catalog/form/renderer/fieldset/element.phtml');
    }
}
