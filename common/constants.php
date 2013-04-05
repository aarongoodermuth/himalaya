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
$GIFT_CARD_TABLE     =     $DB_NAME . '.Gift_Cards';
$PRODUCTS_TABLE      =     $DB_NAME . '.Products';
$SALE_ITEMS_TABLE    =     $DB_NAME . '.Sale_Items';
$AUCTIONS_TABLE      =     $DB_NAME . '.Auctions';
$GIFT_CARDS_TABLE    =     $DB_NAME . '.Gift_Cards';
$SALES_TABLE         =     $DB_NAME . '.Sales';
$ORDERS_TABLE        =     $DB_NAME . '.Orders';


$USER_TYPE_MAPPING       = array('Registered User', 'Supplier');
$ADMIN_USER_TYPE_MAPPING = array('System', 'SysAdmin', 'Owner', 'Telemarketer',
                               'Sales Manager', 'Shipping', 'Accounting', 
                               'Gift Card');
			       
// Do we have the pwqcheck(1) program from the passwdqc package?
$use_pwqcheck = TRUE;
// We can override the default password policy
$pwqcheck_args = '';
// Base-2 logarithm of the iteration count used for password stretching
$hash_cost_log2 = 8;
// Do we require the hashes to be portable to older systems (less secure)?
$hash_portable = FALSE;

/* Dummy salt to waste CPU time on when a non-existent username is requested.
 * This should use the same hash type and cost parameter as we're using for
 * real/new hashes.  The intent is to mitigate timing attacks (probing for
 * valid usernames).  This is optional - the line may be commented out if you
 * don't care about timing attacks enough to spend CPU time on mitigating them
 * or if you can't easily determine what salt string would be appropriate. */
$dummy_salt = '$2a$08$1234567890123456789012';
?>
