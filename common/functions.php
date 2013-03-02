<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';

/******************/
/** END INCLUDES **/
/******************/

//------------------------------------------------------------------------------

/***************/
/** FUNCTIONS **/
/***************/

// (void)
function print_html_header()
{
  echo '<html><body>';
}

// (void)
function print_html_footer()
{
  echo '</body></html>';
}

// checks if there is a user logged in. If so, resets the cookie and returns
//    username. If not, returns null
// (string || null)
function check_logged_in_user($c)
{
  global $COOKIE_NAME, $COOKIE_TIMEOUT;
  
  // if already logged in
  if( isset($_COOKIE[$COOKIE_NAME]) )
  {
    // get username that is associated with the cookie
    $rn = mysql_get_username_from_cookie($c, $_COOKIE[$COOKIE_NAME]);
    if($rn == null)
    {
      // delete cookie
      setcookie($COOKIE_NAME, null, 1);
    }
    else
    {
      // reset cookie
      setcookie($COOKIE_NAME, $_COOKIE[$COOKIE_NAME], time() + $COOKIE_TIMEOUT);
    }

    return $rn;
  }
  else
  {
    return null;
  }
}

// checks if there is admin user logged in. If so, resets the cookie and returns
//    username. If not, returns null
// (string || null)
function check_logged_in_admin_user($c)
{
  global $ADMIN_COOKIE_NAME, $COOKIE_TIMEOUT;
  
  // if already logged in
  if( isset($_COOKIE[$ADMIN_COOKIE_NAME]) )
  {
    // get username that is associated with the cookie
    $rn = mysql_admin_get_username_from_cookie($c, $_COOKIE[$ADMIN_COOKIE_NAME]);
    if($rn == null)
    {
      // delete cookie
      setcookie($ADMIN_COOKIE_NAME, null, 1);
    }
    else
    {
      // reset cookie
      setcookie($ADMIN_COOKIE_NAME, $_COOKIE[$ADMIN_COOKIE_NAME], time() + $COOKIE_TIMEOUT);
    }

    return $rn;
  }
  else
  {
    return null;
  }
}

// (void)
function show_form($form_name)
{
  $data = file_get_contents('/home/goodermuth/dev/websites/himalaya/common/forms/' 
                                 . $form_name . '.form');
  echo $data;
 
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
