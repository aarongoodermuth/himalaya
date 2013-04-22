<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/functions.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql_members.php';

/******************/
/** END INCLUDES **/
/******************/

//------------------------------------------------------------------------------

/***************/
/** FUNCTIONS **/
/***************/

// prints the sale items table
// (void)
function print_sales_table($c, $user)
{
  // print header
  echo '<div style="text-align:center">';
  echo '<h3>Items sold, but not yet recieved by Himalaya.biz</h3>';  
  // get info from db
  $rows = mysql_member_get_orders($c, $user);;

  if($rows === null)
  {
    echo '<p>You do not have any unfulfilled orders.</p>';
    return;
  }

  for($i=0; $i<count($rows); $i++)
  {
    print_sale_item($c, $rows[$i]);
    if($i < count($rows) - 1)
    {
      echo '<br />';
    }
  }

  // print footer
  echo '</div>';
}

// prints a sale item and all its info in a table
// (void)
function print_sale_item($c, $item)
{
  echo '<table border="1" align="center" width="60%">';
  
  echo '<tr><td style="font-weight:bold" width="20%">Item ID</td>';
  echo '<td><a href="../items/view.php?id=' . $item[0] . '">' . $item[0] . '</a></td></tr>';
  echo '<tr><td style="font-weight:bold">Description</td>';
  echo '<td>' . $item[1] . '</a></td></tr>';
  echo '<tr><td style="font-weight:bold">Sale Price</td>';
  echo '<td>' . sprintf('$%.2f', $item[2]/100) . '</a></td></tr>';

  echo '</table>';
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//-----------------------------------------------------------------------------

/**************/
/** PHP CODE **/
/**************/

$c = mysql_make_connection();
$user = check_logged_in_user($c);

if($user != null)
{
  if(mysql_get_type_from_username($c, $user) == $USER_TYPE_MAPPING[1])
  {
    print_html_header(); // will change to nav header later
    echo '<body>';
    print_html_nav();
    echo '<div class="container-fluid">';

    // print sales table
    print_sales_table($c, $user);
   
    echo '<br /><br />';
  }
  else
  {
    header('refresh:0; url=../users/orders.php');
  }
}
else
{
  header('refresh:0; url=../welcome/login.php');
}

echo '</div>';
print_html_footer_js();
print_html_footer2();
print_html_footer();
mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
