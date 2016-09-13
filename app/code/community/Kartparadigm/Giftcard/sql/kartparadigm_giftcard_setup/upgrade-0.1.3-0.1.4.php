<?php
$installer = $this;
$installer->startSetup();
$this->_conn->addColumn($this->getTable('sales_flat_quote'), 'giftcard_code', 'varchar(200)');
$this->_conn->addColumn($this->getTable('sales_flat_quote'), 'giftcard_bal', 'varchar(200)');
$this->_conn->addColumn($this->getTable('sales_flat_quote'), 'giftcard_balused', 'varchar(200)');
$this->_conn->addColumn($this->getTable('sales_flat_quote'), 'giftcard_newbal', 'varchar(200)');
$installer->endSetup();
