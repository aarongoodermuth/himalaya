<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/functions.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql_admin.php';

/******************/
/** END INCLUDES **/
/******************/

//------------------------------------------------------------------------------

/***************/
/** FUNCTIONS **/
/***************/

// prints the rows of the table for items that are out of stock
// (void)
function print_outofstock_table($c)
{
  echo '';
}

// returns the value of items that need restocked
// (float)
function restock_value($c)
{
  return 0;
}

// ...
// (void)
function print_lowstock_table($c)
{
  echo '';
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//-----------------------------------------------------------------------------

/**************/
/** PHP CODE **/
/**************/

$c = mysql_make_connection();
$username = check_logged_in_admin_user($c);
$user_type = mysql_admin_get_type($c, $username);

if( $user_type == $ADMIN_USER_TYPE_MAPPING[1] ||
    $user_type == $ADMIN_USER_TYPE_MAPPING[2] ||
    $user_type == $ADMIN_USER_TYPE_MAPPING[4] )
{
  print_html_header();
  echo '<h3 style="text-align:center">Sales/Order Report</h3>';

  echo '<div><h4 style="text-align:center">Out of Stock Items</h4>';
  echo '<table style="text-align:center" align="center" border="1">';
  echo '<tr style="font-weight:bold; text-align:center"><td>Name</td>
           <td>Retailer</td><td>Selling Price</td></tr>';
  // print table contents
  print_outofstock_table($c);
  echo '</table>'; 
  echo '<p><strong>Restock Value:</strong> ' . restock_value($c) . '</p>';
  echo '</div>';
  
  echo '<div><h4 style="text-align:center">Low Stock Items</h4>';
  echo '<table style="text-align:center" align="center" border="1">';
  echo '<tr style="font-weight:bold; text-align:center"><td>Name</td>
           <td>Retailer</td><td>Selling Price</td></tr>';
  // print table contents
  print_lowstock_table($c);
  echo '</table>'; 
  echo '</div>';
 
  echo '<h1>This one is going to be a pain in the ass to implement</h1>'; 

  //echo '<table style="text-align:center" align="center" border="1">';
  //echo '<tr style="font-weight:bold; text-align:center"><td>Username</td><td>Name</td>
  //         <td>Email</td><td>Gender</td><td>Age</td><td>Income</td><td>Address</td>
  //         <td>Phone</td><td>Additional Phones</td></tr>';
  
  // run telemarketer report
  //...
  //$rows = mysql_admin_tele_report($c);
  //$i = 0;
  //while( $rows[$i] != NULL && $rows[$i] != '')
  //{
  //  print_report_row($c, $rows[$i]);
  //  $i++;
  //}
  ///...
  //echo '</table>';
  echo '<p><a href="dashboard.php">Return to Dashboard</a></p>';
  
  print_html_footer();
}
else
{
  header('refresh:0; url=dashboard.php');
}

mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
