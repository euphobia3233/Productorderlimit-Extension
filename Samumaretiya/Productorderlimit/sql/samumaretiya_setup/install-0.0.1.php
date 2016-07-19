<?php
$installer = $this;
$installer->startSetup();
	$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'productorderlimit', array(
	'group' => 'Samumaretiya Product order limit',
	'type' => 'varchar',
	'backend' => '',
	'frontend' => '',
	'label' => 'Order Limit',
	'input' => 'text',
	'class' => '',
	'source' => '',
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => true,
	'required' => false,
	'user_defined' => false,
	'searchable' => false,
	'filterable' => false,
	'comparable' => false,
	'visible_on_front' => false,
	'unique' => false,
	'apply_to' => 'simple,configurable,bundle,grouped',
	'is_configurable' => false,
	));
$installer->endSetup();
?>