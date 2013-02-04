<?php
/**************/
/** INCLUDES **/
/**************/
include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/functions.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql_admin.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/forms.php';
/******************/
/** END INCLUDES **/
/******************/

//------------------------------------------------------

/***************/
/** CONSTANTS **/
/***************/

$REDIRECT_TIME     =    3;       // 3 seconds 

/*******************/
/** END CONSTANTS **/
/*******************/

//-----------------------------------------------------

/**************************/
/** FUNCTION DEFINITIONS **/
/**************************/

// empty

/*******************************/
/**  END FUNCTION DEFINITIONS **/
/*******************************/

//-----------------------------------------------------------------------------

/**************/
/** PHP CODE **/
/**************/

// establish a connection with our database
$c = mysql_make_connection();

$username = check_logged_in_user($c);

if($username != null)
{
  print_html_header();
  // show user information, give chance to log out, give chance to redirect 
  //    manually
  echo '<p>Welcome back ' . $username . '!</p>';
  echo '<p>If you are not ' . $username . ' then please'
          . ' <a href="logout.php">log out</a> now.</p>';
  echo '<p>Redirecting in ' . $REDIRECT_TIME . '. If you are not redirected,'
          . ' you may <a href="dashboard.php">click here</a> to go to the'
          . ' dashboard</p>';

  // redirect
  header( 'Refresh:' . $REDIRECT_TIME . '; url=dashboard.php' );
}
else
{
  // check to see if any logins have been attempted 
  //    (by checking for post values)
  if( isset($_POST['username']) && isset($_POST['password']) )
  {
    // if attempted check for success or failure
    if( mysql_admin_login_test( $c, $_POST['username'], $_POST['password'] ) )
    {
      // sucessful login
      // put cookie on computer and log value in database
      $rand_cookie_value = 0;
      while( mysql_admin_cookie_value_used($c, $rand_cookie_value) )
      {
        $rand_cookie_value = rand();
      }
       // place session info into database under user's entry
       mysql_admin_log_cookie($_POST['username'], $rand_cookie_value);
       // set cookie with value
       setcookie($ADMIN_COOKIE_NAME, $rand_cookie_value, $COOKIE_TIMEOUT);  
    
      // send to dashboard
      header('Refresh:0; url=dashboard.php'); 
    }
    else
    {
      print_html_header();
      // notify of failed login
      echo '<p style="color:red">Username or Password was not valid</p>';

      // show the pasword page
      show_form('login');
    }
  }
  else
  {
    print_html_header();
    // no login was attempted
    // just show the password page
    show_form('login');
  }
}

mysql_disconnect($c);
print_html_footer();

/******************/
/** END PHP CODE **/
/******************/
//-----------------------------------------------------------------------------

?>
