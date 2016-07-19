<?php
$installer = $this;
$installer->startSetup();
	$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'custom_orderlimit_from', array(
	'group' => 'Samumaretiya Product order limit',
	'type' => 'datetime',
	'backend' => 'eav/entity_attribute_backend_datetime',
	'frontend' => '',
	'label' => 'Active From',
	'input' => 'date',
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