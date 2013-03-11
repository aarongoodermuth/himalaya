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
 
  // redirect
  header( 'Refresh:' . $REDIRECT_TIME . '; url=../users/dashboard.php' );
 
  print_html_header();
  // show user information, give chance to log out, give chance to redirect 
  //    manually
  echo '<p>Welcome back ' . $username . '!</p>';
  echo '<p>If you are not ' . $username . ' then please'
          . ' <a href="logout.php">log out</a> now.</p>';
  echo '<p>Redirecting in ' . $REDIRECT_TIME . '. If you are not redirected,'
          . ' you may <a href="dashboard.php">click here</a> to go to the'
          . ' dashboard</p>';
}
else
{
  // check to see if any logins have been attempted 
  //    (by checking for post values)
  if( isset($_POST['username']) && isset($_POST['password']) )
  {
    // if attempted check for success or failure
    if( mysql_login_test( $c, $_POST['username'], $_POST['password'] ) )
    {
      // sucessful login
      // put cookie on computer and log value in database
      $rand_cookie_value = rand();
      while( mysql_cookie_value_used($c, $rand_cookie_value) )
      {
        $rand_cookie_value = rand();
      }
      // place session info into database under user's entry
      mysql_log_cookie($c, $_POST['username'], $rand_cookie_value);
      // set cookie with value
      setcookie($COOKIE_NAME, $rand_cookie_value, time() + $COOKIE_TIMEOUT, '/');  
      // send to dashboard
      header('Refresh:0; url=../users/dashboard.php'); 
    }
    else
    {
      print_html_header();
      // notify of failed login
      echo '<p style="color:red">Username or Password was not valid</p>';

      // show the pasword page
      show_form('login');

      // give link to create account
      echo '<div><p>New to Himalaya.biz? <a href="createaccount.php">
              Create an account</a></p></div>';
    }
  }
  else
  {
    print_html_header();
    // no login was attempted
    // just show the password page
    show_form('login');

    // give link to create account
    echo '<div><p>New to Himalaya.biz? <a href="createaccount.php">
            Create an account</a></p></div>';
  }
}

print_html_footer();

mysql_disconnect($c);
print_html_footer();

/******************/
/** END PHP CODE **/
/******************/
//-----------------------------------------------------------------------------

?>
