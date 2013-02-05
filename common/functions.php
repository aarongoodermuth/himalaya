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

// checks if there is a user logged in. If so, resets the cookie and returns
//    username. If not, returns null
// (string || null)
function check_logged_in_user($c)
{
  global $ADMIN_COOKIE_NAME, $COOKIE_TIMEOUT;
  
  // if already logged in
  if( isset($_COOKIE[$ADMIN_COOKIE_NAME]) )
  {
echo '<p>at least we have a cookie</p>';
    // reset cookie
    setcookie($ADMIN_COOKIE_NAME, $_COOKIE[$ADMIN_COOKIE_NAME], time() + $COOKIE_TIMEOUT);

    // get username that is associated with the cookie
    return mysql_admin_get_username_from_cookie($c, $_COOKIE[$ADMIN_COOKIE_NAME]);
  }
  else
  {
    return null;
  }
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
