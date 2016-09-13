<?php
class Kartparadigm_Giftcard_Block_Adminhtml_Registries_Grid extends Mage_Adminhtml_Block_Widget_Grid

{
    public function __construct()

    {
        parent::__construct();
        $this->setId('registriesGrid');
        $this->setDefaultSort('giftcard_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('kartparadigm_giftcard/giftcard')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        $this->addColumn('giftcard_id', array(
            'header' => Mage::helper('kartparadigm_giftcard')->__('Id') ,
            'width' => 50,
            'index' => 'giftcard_id',
            'sortable' => true,
            'type' => 'number',
        ));
        $this->addColumn('order_id', array(
            'header' => Mage::helper('kartparadigm_giftcard')->__('Order ID') ,
            'index' => 'order_id',
            'sortable' => true,
            'type' => 'number',
        ));
        $this->addColumn('customer_name', array(
            'header' => Mage::helper('kartparadigm_giftcard')->__('Customer Name') ,
            'index' => 'receiver_name',
            'sortable' => true,
            'type'=>'text',
        ));
        $this->addColumn('giftcard_code', array(
            'header' => Mage::helper('kartparadigm_giftcard')->__('Giftcard Code') ,
            'index' => 'giftcard_code',
            'sortable' => true,
            'type'=>'text',
        ));
        $this->addColumn('giftcard_bal', array(
            'header' => Mage::helper('kartparadigm_giftcard')->__('Giftcard Balance') ,
            'index' => 'giftcard_bal',
            'sortable' => true,
            'type' => 'currency',
            'currency' => 'giftcard_currency',
        ));
        $this->addColumn('store_id', array(
            'header' => $this->__('Store View') ,
            'width' => '200px',
            'index' => 'store_id',
            'type' => 'store',
            'store_all' => false,
            'store_view' => true,
        ));
        $this->addColumn('created_date', array(
            'header' => Mage::helper('kartparadigm_giftcard')->__('Created Date') ,
            'index' => 'created_date',
            'sortable' => true,
            'type' => 'date',
        ));
        $this->addColumn('giftcard_status', array(
            'header' => Mage::helper('kartparadigm_giftcard')->__('Giftcard Status') ,
            'index' => 'giftcard_status',
            'type' => 'options',
            'sortable' => true,
            'options' => array(
                0 => 'Inactive',
                1 => 'Active',
                2 => 'Processing',
                3 => 'Redeemed',
                4 => 'Expired',
                5 => 'Cancelled',
            ) ,
        ));
        $this->addColumn('is_notified', array(
            'header' => Mage::helper('kartparadigm_giftcard')->__('Is Notified') ,
            'index' => 'is_notified',
            'type' => 'options',
            'sortable' => true,
            'options' => array(
                0 => 'NO',
                1 => 'YES',
            ) ,
        ));
        $this->addColumn('action_view', array(
            'header' => $this->helper('kartparadigm_giftcard')->__('Print') ,
            'width' => 15,
            'getter' => 'getId',
            'sortable' => false,
            'filter' => false,
            'type' => 'action',
            'actions' => array(
                array(
                    'caption' => Mage::helper('kartparadigm_giftcard')->__('Print') ,
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
        $this->setMassactionIdField('giftcard_id');
        $this->getMassactionBlock()->setFormFieldName('registries');
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
            'id' => $row->getGiftcardId()
        ));
    }
}

