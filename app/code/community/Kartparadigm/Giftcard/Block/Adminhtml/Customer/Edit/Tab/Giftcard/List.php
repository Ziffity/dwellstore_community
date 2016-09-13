<?php
class Kartparadigm_Giftcard_Block_Adminhtml_Customer_Edit_Tab_Giftcard_List extends Mage_Adminhtml_Block_Widget_Grid
{
public function __construct(){
parent::__construct();
$this->setId('cardsGrid');
$this->setDefaultSort('giftcard_id');
$this->setDefaultDir('ASC');
$this->setSaveParametersInSession(true);
}
protected function _prepareCollection(){
$id = $this->getRequest()->getParam('id');
$model = Mage::getModel('customer/customer')->load($id);
$col = Mage::getModel('kartparadigm_giftcard/giftcard')->getCollection()->addFieldToFilter('receiver_mail',$model['email']);
$this->setCollection($col);

return parent::_prepareCollection();
}
protected function _prepareColumns()
{
$this->addColumn('giftcard_id', array(
'header'=> Mage::helper('kartparadigm_giftcard')->__('Id'),
'width'=> 50,
'index'=> 'giftcard_id',
'sortable' => false,
));
$this->addColumn('order_id', array(
'header'=> Mage::helper('kartparadigm_giftcard')->__('Order ID'),
'index'=> 'order_id',
'sortable' => true,
));
$this->addColumn('receiver_name', array(
'header'=> Mage::helper('kartparadigm_giftcard')->__('Customer Name'),
'index'=> 'customer_name',
'sortable' => true,
));
$this->addColumn('giftcard_code', array(
'header'=> Mage::helper('kartparadigm_giftcard')->__('Giftcard Code'),
'index'=> 'giftcard_code',
'sortable' => true,
));
$this->addColumn('giftcard_val', array(
'header'=> Mage::helper('kartparadigm_giftcard')->__('Giftcard Value'),
'index'=> 'giftcard_val',
'sortable' => true,
));
$this->addColumn('giftcard_bal', array(
'header'=> Mage::helper('kartparadigm_giftcard')->__('Giftcard Balance'),
'index'=> 'giftcard_bal',
'sortable' => true,
));
$this->addColumn('created_date', array(
'header'=> Mage::helper('kartparadigm_giftcard')->__('Created Date'),
'index'=> 'created_date',
'sortable' => true,
));
$this->addColumn('expiry_date', array(
'header'=> Mage::helper('kartparadigm_giftcard')->__('Expiry Date'),
'index'=> 'expiry_date',
'sortable' => true,
));
$this->addColumn('giftcard_status', array(
            'header'    => Mage::helper('kartparadigm_giftcard')->__('Status'),
            'index'     => 'giftcard_status',
            'type'      => 'options',
             'options'   => array(
                0 => 'Inactive',
                1 => 'Active',
                2 => 'Processing',
		3 => 'Redeemed',
		4 => 'Expired',
		5 => 'Canceled',
            ),
        ));
return parent::_prepareColumns();
}



}

