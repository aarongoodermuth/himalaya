<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql_admin.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/functions.php';

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

// check for valid cookie
$username = check_logged_in_admin_user($c);

if( $username != null)
{
  print_html_header();

  if( isset($_POST['newpassword1']) && isset($_POST['newpassword2']) )
  {
    // see if passwords match
    if($_POST['newpassword1'] == $_POST['newpassword2'])
    {
      if( mysql_admin_new_password($c, $username, $_POST['newpassword1']) )
      {
        echo '<p style="color:red">Password had been updated</p>';
      }
      else
      {
        echo '<p style="color:red">Invalid Password</p>';
      }
    }
    else
    {
      echo '<p style="color:red">Passwords did not match</p>';
    }
  }
  
  // always show the change password form
  echo '<h3>Change password for account name: ' . $username . '</h3>';
  show_form('changepassword');

  echo '<p><a href="dashboard.php">Return to Dashboard</a></p>';
}
else
{
  header('Refresh:0; url=login.php');
}

print_html_footer();
mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>

