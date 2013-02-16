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



/*******************/
/** END FUNCTIONS **/
/*******************/

//-----------------------------------------------------------------------------

/**************/
/** PHP CODE **/
/**************/

$c = mysql_make_connection();

$username = check_logged_in_user($c);
$user_type = mysql_admin_get_type($c, $username);

if($user_type == $ADMIN_USER_TYPE_MAPPING[1] ||
   $user_type == $ADMIN_USER_TYPE_MAPPING[2] ||
   $user_type == $ADMIN_USER_TYPE_MAPPING[5] )
{
  print_html_header();

  if(isset($_POST['orderid']))
  {
    if( mysql_admin_shipment_made($c, $_POST['orderid']) )
    {
      echo '<p style="color:red">Success! Please ship the item</p>';
    }
    else
    {
      echo '<p style="color:red">Order update failed.</p>';
    }
  }

  show_form('shipping');
}
else
{
  header('refresh:0; url=login.php');
}

echo '<p><a href="dashboard.php">Return to Dashboard</a></p>';

print_html_footer();

mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
