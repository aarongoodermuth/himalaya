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
function mysql_admin_get_username_from_cookie( $c, $cookie_val )
{
  global $ADMIN_TABLE;

  $cookie_val = sanitize($cookie_val);
  
  $query = 'SELECT Username FROM ' . $ADMIN_TABLE . ' WHERE Session="'
              . $cookie_val . '"';
 
  $results = mysqli_query($c, $query);
  $row = mysqli_fetch_row($results);
  if($row)
  {
    return $row[0];
  }
  else
  {
    return null;
  }
}

// returns the type of admin a user is
// (string)
function mysql_admin_get_type($c, $username)
{
  global $ADMIN_USER_TYPE_MAPPING, $ADMIN_TABLE;

  $username = sanitize($username);

  $query = 'SELECT Type FROM '. $ADMIN_TABLE . ' WHERE Username="' . $username . '"';

  $results = mysqli_query($c, $query);
  if($results)
  {
    $row = mysqli_fetch_row($results);
    if($row)
    {
      return $ADMIN_USER_TYPE_MAPPING[$row[0]]; 
    }
  }

  return '';
}

// changes password of user
// (boolean)
function mysql_admin_new_password($c, $username, $password)
{
  global $ADMIN_TABLE;

  $username = sanitize($username);
  $password = sanitize($password);

  $query = 'UPDATE ' . $ADMIN_TABLE . ' SET Password="' . $password 
              . '" WHERE Username="' . $username . '"';

  return mysqli_query($c, $query);
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//----------------------------------------------------------------------------------------
?>
