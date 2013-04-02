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
    echo '<h3>Gift Card Balance</h3>';
    echo '<p>$ ' . sprintf("%.2f", mysql_member_get_gift_card_balance($c, $user)/100) . '</p>';
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
