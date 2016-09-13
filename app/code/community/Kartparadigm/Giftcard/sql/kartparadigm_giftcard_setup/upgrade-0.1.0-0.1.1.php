<?php

$installer = $this;
$installer->startSetup();



/**
 * Create Registry Type Table 
 *
 *
 */

$tableName = $installer->getTable('kartparadigm_giftcard/giftcard');
// Check if the table already exists
if ($installer->getConnection()->isTableExists($tableName) != true) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('kartparadigm_giftcard/giftcard'))
        ->addColumn('giftcard_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Giftcard Id')
	->addColumn('gcpro_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(     
            'nullable'  => false,
        ), 'Giftcard Product Id')
 	->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(     
            'nullable'  => false,
        ), 'order Id')
	->addColumn('customer_mail', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
            'nullable'  => true,
        ), 'Customer Mail')
        ->addColumn('giftcard_code', Varien_Db_Ddl_Table::TYPE_TEXT, 25, array(
            'nullable'  => true,
        ), 'Giftcard Code')
	->addColumn('giftcard_currency', Varien_Db_Ddl_Table::TYPE_TEXT, 5, array(
            'nullable'  => true,
        ), 'Giftcard Currency')
        ->addColumn('giftcard_name', Varien_Db_Ddl_Table::TYPE_TEXT, 250, array(
            'nullable'  => true,
        ), 'name')
        
        ->addColumn('receiver_name', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
            'nullable'  => true,
        ), 'Receiver Name')
        ->addColumn('receiver_mail', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
            'nullable'  => true,
        ), 'Receiver Mail')
	->addColumn('giftcard_msg', Varien_Db_Ddl_Table::TYPE_TEXT, 300, array(
            'nullable'  => true,
        ), 'Giftcard Message')
        ->addColumn('giftcard_address', Varien_Db_Ddl_Table::TYPE_TEXT, 250, array(
            'nullable'  => true,
        ), 'Giftcard Address')
	->addColumn('delivery_date', Varien_Db_Ddl_Table::TYPE_DATE, null,
		    array(),
		    'Delivery Date'
		)
		
			->addColumn('is_notified', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(     
            'nullable'  => false,
        ), 'Notified')


	 ->addColumn('giftcard_val', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2', array(
		    'nullable'  => true,
		), 'Giftcard Value')
	->addColumn('giftcard_bal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2', array(
		    'nullable'  => true,
		), 'Giftcard bal')
	  ->addColumn('created_date', Varien_Db_Ddl_Table::TYPE_DATE, null,
		    array(),
		    'Created Date'
		)
	->addColumn('expiry_date', Varien_Db_Ddl_Table::TYPE_DATE, null,
		    array(),
		    'Expiry Date'
		)
        ->addColumn('customer_name', Varien_Db_Ddl_Table::TYPE_TEXT, 250, array(
            'nullable'  => true,
        ), 'Customer Name')
          ->addColumn('template_name', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
            'nullable'  => true,
        ), 'Template Name')

        ->addColumn('giftcard_status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
            array(
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
            ),
            'Status')
 ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
            array(
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
            ),
            'Status')
 ->addColumn('added_by', Varien_Db_Ddl_Table::TYPE_TEXT, 25, array(
            'nullable'  => true,
        ), 'Added By')
       
        ->setComment('Magento Developers Guide Type Table');
    $installer->getConnection()->createTable($table);
}
$installer->endSetup();
