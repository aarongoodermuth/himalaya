<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/functions.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql_members.php';

/******************/
/** END INCLUDES **/
/******************/

//------------------------------------------------------------------------------

/***************/
/** FUNCTIONS **/
/***************/

// prints links to all Supplier functions
// (void)
function show_links()
{
  echo '<p><a href="selling.php">Items I am selling</a></p>';                // items I'm selling
  echo '<p><a href="orders.php">My Unfullfilled Orders</a></p>';             // items that have sold but need shipping
  echo '<p><a href="editaccount.php">Edit Account</a></p>';                  // edit account
  echo '<p><a href="../members/changepassword.php">Change Password</a></p>'; // change password
  echo '<p><a href="../welcome/logout.php">Log Out</a></p>';                 // log out
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
  if($type == $USER_TYPE_MAPPING[1])
  {
    // is a supplier
    print_html_header();
    print_html_nav();
    echo '<div class="container-fluid">';
    echo '<body>';
    echo '<h3>Welcome to the Supplier Dashboard, ' . $user . '!</h3>';
    show_links();
    echo '</div>';
    print_html_footer_js();
    print_html_footer2();
    print_html_footer();
  }
  elseif($type == $USER_TYPE_MAPPING[0])
  {
    // is a supplier
    header('refresh:0; url=../user/dashboard.php');
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
