<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/functions.php';

/******************/
/** END INCLUDES **/
/******************/

//------------------------------------------------------------------------------

/***************/
/** FUNCTIONS **/
/***************/

// prints links to all RU functions
// (void)
function show_links()
{
  echo '<div class="pvl">';
  echo '<p><a href="selling.php">Items I am selling</a></p>';                // items I'm selling
  echo '<p><a href="bought.php">Items I am buying</a></p>';                  // items I have bought
  echo '<p><a href="highbidder.php">Items where I am high bidder</a></p>';   // items I am high bidder
  echo '<p><a href="../items/search.php">Search or Browse</a></p>';          // search/browse
  echo '<p><a href="sellitem.php">Sell Item</a></p>';                        // sell an item
  echo '<p><a href="redeem.php">Redeem a Gift Card</a></p>';
  echo '<p><a href="balance.php">Show Gift Card Balance</a></p>';
  echo '<p><a href="editaccount.php">Edit Account</a></p>';                  // edit account
  echo '<p><a href="../members/changepassword.php">Change Password</a></p>'; // change password
  echo '<p><a href="../welcome/logout.php">Log Out</a></p>';                 // log out
  echo '</div>';
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
  $type = mysql_get_type_from_username($c, $user);
  if($type == $USER_TYPE_MAPPING[0])
  {
    // is a RU
    print_html_header();
    echo '<body>';
    print_html_nav();
    echo '<body><div class="container-fluid">';
    echo '<h3>Welcome to the User Dashboard, ' . $user . '!</h3>';
    show_links();
    echo "</div></br>\n";
    print_html_footer2();
    print_html_footer_js();
    print_html_footer();
  }
  elseif($type == $USER_TYPE_MAPPING[1])
  {
    // is a supplier
    header('refresh:0; url=../supplier/dashboard.php');
  }
  else
  {
    die('The database done goofed!');
  }
}
else
{
  header('refresh:0; url=../welcome/login.php');
}

mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
