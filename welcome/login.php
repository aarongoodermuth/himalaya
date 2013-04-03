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

function my_pwqcheck($newpass, $oldpass = '', $user = '')
{
        global $use_pwqcheck, $pwqcheck_args;
        if ($use_pwqcheck)
                return pwqcheck($newpass, $oldpass, $user, '', $pwqcheck_args);

	/* Some really trivial and obviously-insufficient password strength checks -
	 * we ought to use the pwqcheck(1) program instead. */
        $check = '';
        if (strlen($newpass) < 7)
                $check = 'way too short';
        else if (stristr($oldpass, $newpass) ||
            (strlen($oldpass) >= 4 && stristr($newpass, $oldpass)))
                $check = 'is based on the old one';
        else if (stristr($user, $newpass) ||
            (strlen($user) >= 4 && stristr($newpass, $user)))
                $check = 'is based on the username';
        if ($check)
                return "Bad password ($check)";
        return 'OK';
}

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
  echo "<p>Welcome back $username!</p>";
  echo "<p>If you are not $username then please <a href=\"logout.php\">log out</a> now.</p>";
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
    if(mysql_login_test($c, get_post_var('username'), get_post_var('password')))
    {
      // sucessful login
      // put cookie on computer and log value in database
      $rand_cookie_value = rand();
      while(mysql_cookie_value_used($c, $rand_cookie_value))
      {
        $rand_cookie_value = rand();
      }
      // place session info into database under user's entry
      mysql_log_cookie($c, get_post_var('username'), $rand_cookie_value);
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
      echo '<div class="demo-icons-24"><span class="fui-plus-24"></span></div>';
      echo '<div><p>New to Himalaya.biz? <a href="createaccount.php">
              Create an account</a></p></div>';
      echo '';
    }
  }
  else
  {
    print_html_header();
    echo '<body>';
    // no login was attempted
    // just show the password page
    show_form('login');

    // give link to create account
    echo '
      <div class="demo-icons">
        <div class="demo-icons-24">
          <p style="text-indent: 1em"><span class="fui-plus-24"></span>New to Himalaya.biz? <a href="createaccount.php">
            Create an account</a></p>
        </div>
      </div>';
  }
}

mysql_disconnect($c);
print_html_footer2();
print_html_footer_js();
print_html_footer();

/******************/
/** END PHP CODE **/
/******************/
//-----------------------------------------------------------------------------

?>