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

$username = check_logged_in_user($c);

if($username != null)
{
  // delete session info in DB
  mysql_log_cookie($c, $username, 0);
}
 
// remove cookie
setcookie($COOKIE_NAME, null, time() - 3600, '/');
   
// redirect to login page(?)
header('refresh:0; url="login.php"');

mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
