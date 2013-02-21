<?php

$COOKIE_TIMEOUT      =     10 * 60 * 60;    // 10 minutes
$ADMIN_COOKIE_NAME   =     'himalaya_admin';
$MYSQL_SERVERNAME    =     'localhost';
$MYSQL_USERNAME      =     'root';
$MYSQL_PASSWORD      =     '431w';
$DB_NAME             =     'himalaya'; 
$ADMIN_TABLE         =     $DB_NAME . '.Admin_Users';
$REG_USER_TABLE      =     $DB_NAME . '.Registered_Users';
$PHONE_TABLE         =     $DB_NAME . '.Phones';
$ADDRESS_TABLE       =     $DB_NAME . '.Addresses';
$ZIP_TABLE           =     $DB_NAME . '.ZIP_Codes';
$ADMIN_USER_TYPE_MAPPING = array('System', 'SysAdmin', 'Owner', 'Telemarketer',
                               'Sales Manager', 'Shipping', 'Accounting', 
                               'Gift Card');
?>
