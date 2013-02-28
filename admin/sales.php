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

// prints a custom html header for this page only. adds styling (padding 
//    for a div.)
// (void)
function print_this_html_header()
{
  echo '<html><head><style type="text/css">div{margin-bottom:100px;}</style>'
          . '</head><body>';
}

// prints the rows of the table for items that are out of stock
// (void)
function print_outofstock_table($c)
{
  $rows = mysql_admin_outofstock($c);
  
  for($i=0; $i<count($rows); $i++)
  {
    echo '<tr><td>' . $rows[$i][0] . '</td><td>' . $rows[$i][1]
         . '</td><td>' . $rows[$i][2] . '</td></tr>';
  }
}

// returns the value of items that need restocked
// (float)
function restock_value($c)
{
  return 0;
}

// prints the items that have only 1 or 2 items currently being sold
// (void)
function print_lowstock_table($c)
{
  $rows = mysql_admin_lowstock($c);
  
  for($i=0; $i<count($rows); $i++)
  {
    echo '<tr><td>' . $rows[$i][0] . '</td><td>' . $rows[$i][1]
         . '</td><td>' . $rows[$i][2] . '</td></tr>';
  }
}

// prints all items that have been sold in the last week
// (void)
function print_sold_items_table($c)
{
  $rows = mysql_admin_recently_sold($c);
  
  for($i=0; $i<count($rows); $i++)
  {
    echo '<tr><td>' . $rows[$i][0] . '</td><td>' . $rows[$i][1]
         . '</td><td>' . $rows[$i][2] . '</td></tr>';
  }
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
  print_this_html_header();
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
  
  //print sold items table
  echo '<div><h4 style="text-align:center">Recently Sold Items</h4>';
  echo '<table style="text-align:center" align="center" border="1">';
  echo '<tr style="font-weight:bold; text-align:center"><td>Name</td>
           <td>Retailer</td><td>Selling Price</td></tr>';
  print_sold_items_table();
  echo '</table>'; 
  echo '</div>';
  
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
