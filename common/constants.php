<?php

$COOKIE_TIMEOUT      =     10 * 60 * 60;    // 10 minutes
$ADMIN_COOKIE_NAME   =     'himalaya_admin';
$COOKIE_NAME         =     'himalaya';
$MYSQL_SERVERNAME    =     'localhost';
$MYSQL_USERNAME      =     'root';
$MYSQL_PASSWORD      =     '431w';
$DB_NAME             =     'himalaya'; 
$ADMIN_TABLE         =     $DB_NAME . '.Admin_Users';
$REG_USER_TABLE      =     $DB_NAME . '.Registered_Users';
$PHONE_TABLE         =     $DB_NAME . '.Phones';
$ADDRESS_TABLE       =     $DB_NAME . '.Addresses';
$ZIP_TABLE           =     $DB_NAME . '.ZIP_Codes';
$SUPPLIERS_TABLE     =     $DB_NAME . '.Suppliers';
$MEMBERS_TABLE       =     $DB_NAME . '.Members';
$RU_TABLE            =     $DB_NAME . '.Registered_Users';
$EMAIL_TABLE         =     $DB_NAME . '.Emails';
$PRODUCTS_TABLE      =     $DB_NAME . '.Products';
$SALE_ITEMS_TABLE    =     $DB_NAME . '.Sale_Items';
$AUCTIONS_TABLE      =     $DB_NAME . '.Auctions';
$GIFT_CARDS_TABLE    =     $DB_NAME . '.Gift_Cards';

$USER_TYPE_MAPPING       = array('Registered User', 'Supplier');
$ADMIN_USER_TYPE_MAPPING = array('System', 'SysAdmin', 'Owner', 'Telemarketer',
                               'Sales Manager', 'Shipping', 'Accounting', 
                               'Gift Card');
?>
