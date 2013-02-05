<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql_admin.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/functions.php';

/******************/
/** END INCLUDES **/
/******************/

//----------------------------------------------------------------------------------------

/***************/
/** FUNCTIONS **/
/***************/

// show all the links that everyone can view
// (void)
function show_all()
{
  echo "<p><a href='changepassword.php'>Change Password</a.</p>";
  echo "<p><a href='logout.php'>Log Out</a></p>";
}

// (void)
function show_telemarketer_page()
{
  echo "<p><a href='telemarketer.php'>Run Telemarketing Report</a></p>";
}

// (void)
function show_sales_page()
{
  echo "<p><a href='salesorderreport.php'>Show Sales/Order Reports</a></p>";
}

// (void)
function show_shipping_page()
{
  echo "<p><a href='shipping.php'>Shipping Page</a></p>";
}

// (void)
function show_accounting_page()
{
  echo "<p><a href='accounting.php'>Accounting Page</a></p>";
} 

// (void)
function show_owner_page()
{
  show_telemarketer_page();
  show_sales_page();
  show_shipping_page();
  show_accounting_page();
}

// (void)
function show_sysadmin_page()
{
  show_owner_page();
  echo "<p><a href='addadmin.php'>Add Admin Users</a></p>";
  echo "<p.<a href='removeadmin.php'>Remove Admin Users</a></p>";
  echo "<p><a href='modifydb.php'><Modify Database</a></p>";
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//----------------------------------------------------------------------------------------

/**************/
/** PHP CODE **/
/**************/

$c = mysql_make_connection();

// check for valid cookie
$username = check_logged_in_user($c);

if( $username != null ) 
{
die('test');
  // get admin user type based on cookie
  $user_type = mysql_admin_get_type($c, $username);

  // display a welcome message
  echo '<h3>Welcome to the ' . $user_type . ' dashboard!</h3>';

  // display the dashboard based on user type
  switch( $user_type )
  {
    case 'Telemarketer':
        show_html_header();
	show_telemarketer_page();
        show_all();
	break;
    case 'Sales Manager':
        show_html_header();
	show_sales_page();
        show_all();
	break;
    case 'Shipping':
        show_html_header();
	show_shipping_page();
        show_all();
	break;
    case 'Accounting':
        show_html_header();
	show_accounting_page();
        show_all();
	break;
    case 'Gift Card':
        show_html_header();
	show_gift_card_page();
        show_all();
	break;
    case 'Owner':
        show_html_header();
	show_owner_page();
        show_all();
	break;
    case 'SysAdmin':
        show_html_header();
	show_sysadmin_page();
        show_all();
	break;
    default:
	// redirect to login page
        header('Refresh:0; url="login.php"');
	break;
    }
}
else
{die('other test');
  //redirect to login page
  header('Refresh:0; url="login.php"'); 
}

mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

?>

