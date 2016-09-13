<?php

$installer = $this;
$installer->startSetup();

/**
 * Create Giftcardtemplates Type Table 
 *
 *
 */
$tableName = $installer->getTable('kartparadigm_giftcard/giftcardtemplate');
// Check if the table already exists
if ($installer->getConnection()->isTableExists($tableName) != true) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('kartparadigm_giftcard/giftcardtemplate'))
        ->addColumn('giftcardtemplate_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Template Id')
	  ->addColumn('template_name', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
            'nullable'  => false,
        ), 'Template Name')
	->addColumn('template_layout', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
            'nullable'  => false,
        ), 'Template Layout')
        ->addColumn('theme_color', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(
            'nullable'  => false,
        ), 'Theme Color')
	->addColumn('text_color', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(
		    'nullable'  => false,
		), 'Text Color')
	->addColumn('background_img', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
		    'nullable'  => true,
		), 'Background Image')
		->addColumn('template_img', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
		    'nullable'  => true,
		), 'Template Image')
	
	->addColumn('giftcard_note', Varien_Db_Ddl_Table::TYPE_TEXT, 300, array(
		    'nullable'  => true,
		), 'Giftcard Note')
	   ->addColumn('template_status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
            array(
                'unsigned' => true,
                'nullable' => false,
                'default' => '1',
            ),
            'Status')
       
        ->setComment('Template Table');
    $installer->getConnection()->createTable($table);
}
$installer->addAttribute('catalog_product', 'giftcard_template',array(
	'attribute_set'		=> 'Default', // note the addition of this array key
	'group'				=> 'General', // and this one
	'label'				=> 'Giftcard Template',
    'type'              => 'varchar',
    'input'             => 'multiselect',
    'backend'           => 'eav/entity_attribute_backend_array',
    'frontend'          => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'is_user_defined'   => true,
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
     'apply_to' => 'giftcard',
    'visible_in_advanced_search' => false,
    'unique'            => false
));



$installer->endSetup();
