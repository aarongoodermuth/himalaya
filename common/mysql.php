<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/PasswordHash.php';

/******************/
/** END INCLUDES **/
/******************/

//----------------------------------------------------------------------------------------

/***************/
/** FUNCTIONS **/
/***************/

// connects to the database (if it can, aborts connection otherwise)
// (mysqli_object)
function mysql_make_connection()
{
  global $MYSQL_SERVERNAME, $MYSQL_USERNAME, $MYSQL_PASSWORD, $DB_NAME;
  $mysqli_obj = mysqli_init();
  if(!$mysqli_obj)
  {  /*die('Could not connect');*/  }

  $connected = mysqli_real_connect($mysqli_obj, $MYSQL_SERVERNAME, $MYSQL_USERNAME, $MYSQL_PASSWORD, $DB_NAME);
  
  if (!$connected)
  {
    //die('Could not connect');
  }

  return $mysqli_obj;
}

// disconnects from the DB, even if connection is not open
// (void)
function mysql_disconnect( $mysqli_obj )
{
  mysqli_close($mysqli_obj);
}

// check if a usename and password combination are valid
// (boolean)
function mysql_login_test($c, $username, $pass)
{
  global $MEMBERS_TABLE, $dummy_salt, $hash_cost_log2, $hash_portable;

  $hasher = new PasswordHash($hash_cost_log2, $hash_portable);

  /* 
   * Sanity-check the username, don't rely on our use of prepared statements
   * alone to prevent attacks on the SQL server via malicious usernames. 
   */
  if (!preg_match('/^[a-zA-Z0-9_]{1,60}$/', $username))
    return false;
    
  $hash = '*';
  if ($stmt = mysqli_prepare($c, "SELECT M.password FROM $MEMBERS_TABLE M WHERE username=?")) {
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $hash);
    mysqli_stmt_fetch($stmt);
  }
  
  /* mitigate timing attacks (probing for valid username) */

  if (isset($dummy_salt) && strlen($hash) < 20)
    $hash = $dummy_salt;
    
  if ($hasher->CheckPassword($pass, $hash)) {
    $ret = true;
  } else {
    $ret = false;
  }
  
  unset($hasher);
  mysqli_stmt_close($stmt);
  
  return $ret;
}

// logs to cookie to the database
// returns success of query
// (boolean)
function mysql_log_cookie( $c, $username, $cookie_val )
{
  global $MEMBERS_TABLE;

  $username = sanitize($username);
  $cookie_val = sanitize($cookie_val);

  $query = 'UPDATE ' . $MEMBERS_TABLE . ' SET asession="' . $cookie_val
              . '" WHERE username="' . $username . '"';
  mysqli_query($c, $query);
}

// checks if a proposed cookie login value exists in the database
// (boolean)
function mysql_cookie_value_used( $c, $cookie_val )
{
  global $MEMBERS_TABLE;

  $cookie_val = sanitize($cookie_val);

  if($results =  mysqli_query($c, 'SELECT * FROM ' . $MEMBERS_TABLE
                                     . ' WHERE asession="'
                                     . $cookie_val. '"') )
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
function mysql_get_username_from_cookie( $c, $cookie_val )
{
  global $MEMBERS_TABLE;

  $cookie_val = sanitize($cookie_val);

  $query = 'SELECT username FROM ' . $MEMBERS_TABLE . ' WHERE asession="'
              . $cookie_val . '"';

  $results = mysqli_query($c, $query);
  if($results == null)
  {
    return null;
  }

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
// ...
// (string)
function mysql_get_type_from_username($c, $username)
{
  global $RU_TABLE, $SUPPLIERS_TABLE, $USER_TYPE_MAPPING;

  $username = sanitize($username);

  $query[0] = 'SELECT COUNT(*) FROM ' . $RU_TABLE        . ' WHERE username="' . $username . '"';
  $query[1] = 'SELECT COUNT(*) FROM ' . $SUPPLIERS_TABLE . ' WHERE username="' . $username . '"';

  $db_answer[0] = mysqli_query($c, $query[0]);
  $db_answer[1] = mysqli_query($c, $query[1]);

  $row[0] = mysqli_fetch_row($db_answer[0]);
  $row[1] = mysqli_fetch_row($db_answer[1]);

  if($row[0][0] == 1 && $row[1][0] == 0)
  {
    return $USER_TYPE_MAPPING[0];
  }
  elseif($row[0][0] == 0 && $row[1][0] == 1)
  {
    return $USER_TYPE_MAPPING[1];
  }
  else
  {
    return null;
  }
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//----------------------------------------------------------------------------------------
?>
