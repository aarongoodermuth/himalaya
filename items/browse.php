<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/functions.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql.php';

/******************/
/** END INCLUDES **/
/******************/

//------------------------------------------------------------------------------

/***************/
/** FUNCTIONS **/
/***************/



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
  print_html_header();
  echo '<body>';
  print_html_nav();
  echo '<div class="container-fluid">';

  echo '<h3>Browse</h3>';

  if(empty($_GET['cid'])) // basic browse page - show complete category list
  {
    echo '<b>Climb higher!</b> Choose a category to browse.';
  }
  else // show items, sorting options, and subcategories
  {
  }
}
else // user not logged in
{
  header('refresh:0; url=../welcome/login.php');
}

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
