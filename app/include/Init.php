<?php
define('CUSTOMER_TABLE', 'customers');
define('BILLING_ADDRESSES_TABLE','billing_addresses');
define('SHIPPING_ADDRESSES_TABLE','shipping_addresses');
define('LOG_DIRECTORY',$_SERVER['DOCUMENT_ROOT'].'/Customer-Management/app/logs/');

$GLOBALS['config'] = array(
    'mysql'=>array(
        'host'=>'127.0.0.1',
        'username' => 'root',
        'password' => '',
        'db' => 'test'
    ));
