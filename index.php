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

$c = mysql_make_connection();
$user = check_logged_in_user($c);

if($user == null)
{
  // take to the login page
  header('refresh:0; url=welcome/login.php');
  print_html_header();
//  echo '<p>You are being redirected. If you are not automatically redirected,
//           you may click <a href="welcome/login.php">here.</a></p>';
  print_html_footer();
}
else
{
  // take to the user page for this user
  header('refresh:0; url=users/dashboard.php');
  print_html_header();
//  echo '<p>You are being redirected. If you are not automatically redirected,
//            you may click <a href="users/dashboard.php">here.</a></p>';
  print_html_footer();
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//-----------------------------------------------------------------------------

/**************/
/** PHP CODE **/
/**************/



/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
