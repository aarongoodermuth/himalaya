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
function print_sales_table($c)
{
  // print header
  echo '<div>';
  
  // get info from db
  //...
  /*temp*/$rows[0] = array("test");

  for($i=0; $i<count($rows); $i++)
  {
    print_sale_item($c, $rows[$i]);
  }

  // print footer
  echo '</div>';
}

// prints a sale item and all its info in a table
// (void)
function print_sale_item($c, $item)
{
  echo '<table border="1">';
  
  echo '<tr><td style="font-weight:bold">Item ID</td>';
  echo '<td><a href="../items/view.php?id=' . $item[0] . '">' . $item[0] . '</a></td></tr>';
  
  echo '</table>';
}

// prints the auctions table
// (void)
function print_auctions_table($c)
{
  // print header
  echo '<div>';
  
  // get info from db
  //...
  /*temp*/$rows[0] = array("test");

  for($i=0; $i<count($rows); $i++)
  {
    print_sale_item($c, $rows[$i]);
  }

  // print footer
  echo '</div>';
}

// prints an auction and all its info in a table
// (void)
function print_auction_item($c, $item)
{
  echo '<table>';

  echo '<tr><td style="font-weight:bold">Item ID</td>';
  echo '<td><a href="../items/view.php?id=' . $item[0] . '">' . $item[0] . '</a></td></tr>';
  
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
  if(mysql_get_type_from_username($c, $user) == $USER_TYPE_MAPPING[0])
  {
    print_html_header(); // will change to nav header later

    // print sales table
    print_sales_table($c);
    
    // print auctions table
    print_auctions_table($c);

    echo '<p><a href="dashboard.php">Return to Dashboard</a></p>';
  }
  else
  {
    header('refresh:0; url=../suppliers/selling.php');
  }
}
else
{
  header('refresh:0; url=../welcome/login.php');
}

print_html_footer();
mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
