<?php
$installer = $this;
$installer->startSetup();
$th =  new Mage_Catalog_Model_Resource_Setup();  

$th->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'giftcard_type', array(
    'group' => 'Prices',
    'type'       => 'int',
    'attribute_set' =>  'Default',
    'input'      => 'select',
    'label'      => 'Giftcard Type',
     'user_defined' => true,
    'sort_order' => 990,
     'required' => true,
       'used_in_product_listing' => true,
    'global'     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'backend'    => 'eav/entity_attribute_backend_array',
    'apply_to' => 'giftcard', 
    'option'     => array (
        'values' => array(
            0 => 'Virtual Giftcard',
            1 => 'Physical Giftcard',
             2 => 'Mixed Giftcard',
        )
    ),

));

$th->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'price_type', array(
    'group' => 'Prices',
    'type'       => 'int',
    'attribute_set' =>  'Default',
    'input'      => 'select',
    'label'      => 'Giftcard Price Type',
    'sort_order' => 991,
    'user_defined' => true,
    'required' => true,
      'used_in_product_listing' => true,
    'global'     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'backend'    => 'eav/entity_attribute_backend_array',
    'apply_to' => 'giftcard', 
    'option'     => array (
        'values' => array(
            0 => 'Fixed Price',
            1 => 'Range Price',
        )
    ),

));
$th->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'giftcard_value', array(
            'group' => 'Prices', 
            'type' => 'decimal',
                        'attribute_set' =>  'Default', // Your custom Attribute set
            'backend' => '',
            'frontend' => '',
            'label' => 'Giftcard Value',
            'input' => 'price',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
            'visible' => true,
	    'sort_order' => 992,
            'required' => true,
            'user_defined' => true,
            'default' => '0',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
	   
            'apply_to' => 'giftcard',  // Apply to simple product type
        ) );

$th->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'min_gcamt', array(
            'group' => 'Prices', 
            'type' => 'decimal',
                        'attribute_set' =>  'Default', // Your custom Attribute set
            'backend' => '',
            'frontend' => '',
            'label' => 'Min Giftcard Amount',
            'input' => 'price',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
            'visible' => true,
             'required' => true,
            'user_defined' => true,
            'default' => '0',
	    'sort_order' => 993,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
	    'note' => 'Fill it only if you select range price',
            'apply_to' => 'giftcard',  // Apply to simple product type
        ) );

$th->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'max_gcamt', array(
            'group' => 'Prices', 
            'type' => 'decimal',
                        'attribute_set' =>  'Default', // Your custom Attribute set
            'backend' => '',
            'frontend' => '',
            'label' => 'Max Giftcard Amount',
            'input' => 'price',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
            'visible' => true,
             'required' => true,
            'user_defined' => true,
            'default' => '0',
	    'sort_order' => 994,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
	     'note' => 'Fill it only if you select range price',
            'apply_to' => 'giftcard',  // Apply to simple product type
        ) );
$installer->endSetup();
