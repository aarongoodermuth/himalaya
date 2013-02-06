<?php

$COOKIE_TIMEOUT      =     10 * 60 * 60;    // 10 minutes
$ADMIN_COOKIE_NAME   =     'himalaya_admin';
$MYSQL_SERVERNAME    =     'localhost';
$MYSQL_USERNAME      =     'root';
$MYSQL_PASSWORD      =     '431w';
$DB_NAME             =     'testDB'; 
$ADMIN_TABLE         =     $DB_NAME . '.Admin_Users';

$ADMIN_USER_TYPE_MAPPING = array('System', 'SysAdmin', 'Owner', 'Telemarketer',
                               'Sales Manager', 'Shipping', 'Accounting', 
                               'Gift Card');
?>
