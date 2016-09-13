<?php 

$installer = $this;
 
$installer->startSetup();
 
$attributes = array(
    'price',
    'tax_class_id'
);
 
foreach ($attributes as $attributeCode) {
    $applyTo = explode(
        ',',
        $installer->getAttribute(
            Mage_Catalog_Model_Product::ENTITY,
            $attributeCode,
            'apply_to'
        )
    );
 
    if (!in_array('giftcard', $applyTo)) {
        $applyTo[] = 'giftcard';
        $installer->updateAttribute(
            Mage_Catalog_Model_Product::ENTITY,
            $attributeCode,
            'apply_to',
            join(',', $applyTo)
        );
    }
}




$installer->endSetup();


