<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';

/******************/
/** END INCLUDES **/
/******************/

//----------------------------------------------------------------------------------------

/***************/
/** FUNCTIONS **/
/***************/

// logs to cookie to the database
// (void)
function mysql_admin_log_cookie( $connection, $username, $cookie_cal )
{
  //...
}

// checks if a proposed cookie login value exists in the database
// (boolean)
function mysql_admin_cookie_value_used( $connection, $cookie_val )
{
  //...
  return false;
}

// check if a usename and password combination are valid
// (boolean)
function mysql_admin_login_test( $connection, $username, $password )
{
  //...
  return false;
}

// finds the username from a cookie value
// (string || null)
function mysql_admin_get_username_from_cookie( $connection, $cookie_val )
{
  //...
  return "test_user_name";
}

// returns the type of admin a user is
// (string)
function mysql_admin_get_type($c, $username)
{
  //...
  return 'sysadmin';
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//----------------------------------------------------------------------------------------
?>

