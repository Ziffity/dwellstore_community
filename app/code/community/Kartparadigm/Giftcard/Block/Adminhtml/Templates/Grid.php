<?php
class Kartparadigm_Giftcard_Block_Adminhtml_Templates_Grid extends Mage_Adminhtml_Block_Widget_Grid

{
    public function __construct()

    {
        parent::__construct();
        $this->setId('templatesGrid');
        $this->setDefaultSort('giftcardtemplate_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('kartparadigm_giftcard/giftcardtemplate')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        $this->addColumn('giftcardtemplate_id', array(
            'header' => Mage::helper('kartparadigm_giftcard')->__('Id') ,
            'width' => 50,
            'index' => 'giftcardtemplate_id',
            'sortable' => true,
            'type' => 'number',
        ));
        $this->addColumn('template_name', array(
            'header' => Mage::helper('kartparadigm_giftcard')->__('Template Name') ,
            'index' => 'template_name',
            'sortable' => true,
            'type'=>'text',
        ));
      /*  $this->addColumn('template_layout', array(
            'header' => Mage::helper('kartparadigm_giftcard')->__('Layout') ,
            'index' => 'template_layout',
            'sortable' => true,
            'type' => 'options',
            'options' => array(
                'Center Layout' => 'Ceter Layout',
                'Left Layout' => 'Left Layout',
                'Top Layout' => 'Top Layout',
            ) ,
        ));*/
        $this->addColumn('theme_color', array(
            'header' => Mage::helper('kartparadigm_giftcard')->__('Theme Color') ,
            'index' => 'theme_color',
            'sortable' => true,
            'type'=>'text',
        ));
 $this->addColumn('text_color', array(
            'header' => Mage::helper('kartparadigm_giftcard')->__('Text Color') ,
            'index' => 'text_color',
            'sortable' => true,
            'type'=>'text',
        ));
        $this->addColumn('giftcard_note', array(
            'header' => Mage::helper('kartparadigm_giftcard')->__('Note') ,
            'index' => 'giftcard_note',
            'sortable' => true,
            'type'=>'textarea',
        ));
       /*$this->addColumn('template_status', array(
            'header' => Mage::helper('kartparadigm_giftcard')->__('Status') ,
            'index' => 'template_status',
             'sortable' => true,
            'type' => 'options',
            'options' => array(
                0 => 'Inactive',
                1 => 'Active',
            ) ,
        ));*/
        $this->addColumn('action_edit', array(
            'header' => $this->helper('kartparadigm_giftcard')->__('Action') ,
            'width' => 15,
            'getter' => 'getId',
            'sortable' => false,
            'filter' => false,
            'type' => 'action',
            'actions' => array(
                array(
                    'caption' => Mage::helper('kartparadigm_giftcard')->__('Edit') ,
                    'url' => array(
                        'base' => '*/*/edit'
                    ) ,
                    'field' => 'id'
                )
            )
        ));
        $this->addColumn('action_view', array(
            'header' => $this->helper('kartparadigm_giftcard')->__('Preview') ,
            'width' => 15,
            'getter' => 'getId',
            'sortable' => false,
            'filter' => false,
            'type' => 'action',
            'actions' => array(
                array(
                    'caption' => Mage::helper('kartparadigm_giftcard')->__('Preview') ,
                    'url' => array(
                        'base' => '*/*/view'
                    ) ,
                    'field' => 'id'
                )
            )
        ));
        return parent::_prepareColumns();
    }
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('giftcardtemplate_id');
        $this->getMassactionBlock()->setFormFieldName('templates');
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('kartparadigm_giftcard')->__('Delete') ,
            'url' => $this->getUrl('*/*/massDelete') ,
            'confirm' => Mage::helper('kartparadigm_giftcard')->__('Are you sure?')
        ));
        return $this;
    }
    public function getRowUrl($row) //disable edit giftcard option

    {
        return $this->getUrl('*/*/edit', array(
            'id' => $row->getGiftcardtemplateId()
        ));
    }
}

