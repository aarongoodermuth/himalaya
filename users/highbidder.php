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

// prints the auctions table
// (void)
function print_auctions_table($c, $user)
{
  // print header
  echo '<div style="text-align:center">';
  echo '<h3>Auctions where I am high bidder</h3>';
  
  // get info from db
  $rows = mysql_member_get_bidding($c, $user);
  
  if($rows === null)
  {
    echo '<p>You are not the high bidder on any items.</p>';
    return;
  }
  
  for($i=0; $i<count($rows); $i++)
  {
    print_auction_item($c, $rows[$i]);
    if($i < count($rows) - 1)
    {
      echo '<br />';
    }
  }

  // print footer
  echo '</div>';
}

// prints an auction and all its info in a table
// (void)
function print_auction_item($c, $item)
{
  echo '<table border="1" align="center" width="60%">';

  echo '<tr><td style="font-weight:bold" width="20%">Item ID</td>';
  echo '<td><a href="../items/view.php?id=' . $item[0] . '">' . $item[0] . '</a></td></tr>';
  echo '<tr><td style="font-weight:bold">Description</td>';
  echo '<td>' . $item[1] . '</a></td></tr>';
  echo '<tr><td style="font-weight:bold">Current Bid</td>';
  echo '<td>' . sprintf('$%.2f', $item[2]/100) . '</a></td></tr>';
  echo '<tr><td style="font-weight:bold">End Time</td>';
  echo '<td>' . $item[3] . '</td>';
 
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
    print_html_header();
    echo '<body>';
    print_html_nav();
    echo '<div class="container-fluid">';

    // print auctions table
    print_auctions_table($c, $user);

    echo '<br /><br />';
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
