<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql_admin.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/functions.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/forms.php';

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

if( mysql_admin_get_type($c, $username) == $ADMIN_USER_TYPE_MAPPING[1])
{
  print_html_header();

  //check if we are already adding a user
  if(isset($_POST['username']))
  {
    if( mysql_admin_remove_user($c, $_POST['username']) )
    {
        echo '<p style="color:red">User "' . $_POST['username'] . '" successfully removed</p>.';
    }
    else
    {
        echo '<p style="color:red">Remove User failed</p>';
    }
  }
 
  show_form('removeadmin');
}
else
{
  header('Refresh:0; url=login.php');
}

echo '<p><a href="dashboard.php">Return to the Dashboard</a></p>';
print_html_footer();
mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
