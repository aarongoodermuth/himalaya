<?php
/**************/
/** INCLUDES **/
/**************/
include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
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

//empty

/*******************************/
/**  END FUNCTION DEFINITIONS **/
/*******************************/

//-----------------------------------------------------

/**************/
/** PHP CODE **/
/**************/

print_html_header();

//establish a connection with our database
$c = mysql_make_connection();

//check if cookie corresponds to a valid user
if( isset($_COOKIE[ $ADMIN_COOKIE_NAME ]) ) 
{
  //determine if the cookie belongs to a valid user
  $valid_cookie_exists = mysql_admin_cookie_value_used($c, $_COOKIE[$ADMIN_COOKIE_NAME]);
}
else
{
  $valid_cookie_exists = false;
}

if($valid_cookie_exists)
{
  //if cookie found, redirect to dashboard (after waiting REDIRECT_TIME seconds)

  //reset cookie
  setcookie($COOKIE_NAME, $_COOKIE[$ADMIN_COOKIE_NAME], $COOKIE_TIMEOUT); 

  //get user information that is found 
  $username = mysql_admin_get_username_from_cookie($c, $_COOKIE[$ADMIN_COOKIE_NAME]);

  //show user information, give chance to log out, give chance to redirect manually
  echo "<p>Welcome back " . $username . "!</p>";
  echo "<p>If you are not " . $username . " then please <a href='logout.php'>log out</a> now.</p>";
  echo "<p>Redirecting in " . $REDIRECT_TIME . ". If you are not redirected, you may <a href='dashboard.php'>click here</a> to go to the dashboard</p>";

  //redirect
  header( 'Refresh:' . $REDIRECT_TIME . '; url=dashboard.php' );
}
else
{
  //check to see if any logins have been attempted (by checking for post values)
  if( isset($_POST['username']) && isset($_POST['password']) )
  {
    //if attempted check for success or failure
    if( mysql_admin_login_test( $c, $_POST['username'], $_POST['password'] ) )
    {
      //sucessful login
      //put cookie on computer and log value in database
      $rand_cookie_value = 0;
      while( mysql_admin_cookie_value_used($c, $rand_cookie_value) )
      {
        $rand_cookie_value = rand();                                 //get random number for cookie value
      }
      mysql_admin_log_cookie($_POST['username'], $rand_cookie_value);      //place session info into database under user's entry
      setcookie($ADMIN_COOKIE_NAME, $rand_cookie_value, $COOKIE_TIMEOUT);  //set cookie with value 
    
      //send to dashboard
      header('Refresh:0; url=dashboard.php'); 
    }
    else
    {
      //notify of failed login
      echo "<p style='color:red'>Username or Password was not valid</p>";

      //show the pasword page
      show_password_form();
    }
  }
  else
  {
    //no login was attempted
    //just show the password page
    show_password_form();
  }
}

mysql_disconnect($c);

print_html_footer();

/******************/
/** END PHP CODE **/
/******************/
//-----------------------------------------------------------------

?>
