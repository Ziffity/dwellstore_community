<?php

$installer = $this;
$installer->startSetup();

/**
 * Create Giftcardtrans Type Table 
 *
 *
 */
$tableName = $installer->getTable('kartparadigm_giftcard/giftcardtrans');
// Check if the table already exists
if ($installer->getConnection()->isTableExists($tableName) != true) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('kartparadigm_giftcard/giftcardtrans'))
        ->addColumn('giftcardtrans_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Giftcard Trans Id')
	->addColumn('giftcard_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(     
            'nullable'  => false,
        ), 'Giftcard Id')
 	->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(     
            'nullable'  => false,
        ), 'order Id')
        ->addColumn('giftcard_code', Varien_Db_Ddl_Table::TYPE_TEXT, 25, array(
            'nullable'  => true,
        ), 'Giftcard Code')
	->addColumn('giftcard_currency', Varien_Db_Ddl_Table::TYPE_TEXT, 5, array(
            'nullable'  => true,
        ), 'Giftcard Currency')
        ->addColumn('giftcard_name', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
            'nullable'  => true,
        ), 'Giftcard Name')
	->addColumn('giftcard_val', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2', array(
		    'nullable'  => true,
		), 'Giftcard Value')
	->addColumn('giftcard_balused', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2', array(
		    'nullable'  => true,
		), 'Giftcard Bal Used')
	->addColumn('giftcard_bal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2', array(
		    'nullable'  => true,
		), 'Giftcard Balance')
	  ->addColumn('created_date', Varien_Db_Ddl_Table::TYPE_DATE, null,
		    array(),
		    'Created Date'
		)
	->addColumn('transac_date', Varien_Db_Ddl_Table::TYPE_DATE, null,
		    array(),
		    'Transaction Date'
		)
	->addColumn('expiry_date', Varien_Db_Ddl_Table::TYPE_DATE, null,
		    array(),
		    'Expiry Date'
		)
        ->addColumn('customer_name', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
            'nullable'  => true,
        ), 'Customer Name')
	->addColumn('customer_mail', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
            'nullable'  => true,
        ), 'Customer Mail')
	->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, 150, array(
            'nullable'  => true,
        ), 'comment')
        ->addColumn('giftcard_status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
            array(
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
            ),
            'Status')
            ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(     
            'nullable'  => false,
        ), 'Store Id')
        ->setComment('Magento Developer Type Table');
    $installer->getConnection()->createTable($table);
}
$installer->endSetup();
