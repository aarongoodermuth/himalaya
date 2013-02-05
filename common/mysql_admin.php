<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql.php';

/******************/
/** END INCLUDES **/
/******************/

//----------------------------------------------------------------------------------------

/***************/
/** FUNCTIONS **/
/***************/

// logs to cookie to the database
// returns success of query
// (boolean)
function mysql_admin_log_cookie( $c, $username, $cookie_val )
{
  global $ADMIN_TABLE;

  $username = sanitize($username);
  $cookie_val = sanitize($cookie_val);

  $query = 'UPDATE ' . $ADMIN_TABLE . ' SET Session="' . $cookie_val
              . '" WHERE Username="' . $username . '"';
  mysqli_query($c, $query);
}

// checks if a proposed cookie login value exists in the database
// (boolean)
function mysql_admin_cookie_value_used( $c, $cookie_val )
{
  global $ADMIN_TABLE;

  $cookie_val = sanitize($cookie_val);

  if($results =  mysqli_query($c, 'SELECT * FROM ' . $ADMIN_TABLE . ' WHERE Session="'
                 . $cookie_val. '"') )
  {
    return mysqli_num_rows($results); 
  }
  else
  {
    return false;
  }
}

// check if a usename and password combination are valid
// (boolean)
function mysql_admin_login_test( $c, $username, $password )
{
  global $ADMIN_TABLE;

  $username = sanitize($username);
  $password = sanitize($password);

  $query = 'SELECT * FROM ' . $ADMIN_TABLE . ' WHERE Username="'
              . $username . '" AND Password="' . $password . '"';

  if( $results = mysqli_query($c, $query) )
  {
    return mysqli_num_rows($results);
  }
  else
  {
    return false;
  }
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
