<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql.php';

/******************/
/** END INCLUDES **/
/******************/

//------------------------------------------------------------------------------

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

  if($results =  mysqli_query($c, 'SELECT * FROM ' . $ADMIN_TABLE 
                                     . ' WHERE Session="'
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

// adds a new admin user
// (boolean)
function mysql_admin_add_user($c, $username, $password, $type)
{
  global $ADMIN_TABLE;
  
  $username = sanitize($username);
  $password = sanitize($password);
  $type     = sanitize($type);

  $query = 'INSERT INTO ' . $ADMIN_TABLE . ' VALUES("' . $username . ' ", "' 
              . $password . '", "' . $type . '", "0")';

  return mysqli_query($c, $query);
}

// deletes an admin user
// (boolean)
function mysql_admin_remove_user($c, $username)
{
  global $ADMIN_TABLE;

  $username = sanitize($username);

  $query = 'DELETE FROM ' . $ADMIN_TABLE . ' WHERE Username="' 
              . $username . '"';
  
  return mysqli_query($c, $query);
}

// takes the query submitted by the user and sends it to the database
// this fn then takes a look at the database response and attempts to 
//    send that response to the caller
// NOTE: this fn does not do any sanitization of the query before sending
//          it to the database and any call of this fn is made at the caller's
//          own risk
// (html string)
function mysql_admin_db_query($c, $query)
{
  $db_answer = mysqli_query($c, $query);

  if($db_answer === TRUE)
  {
    // get actual response
    //...
    return 'Query successfully executed. ' . mysqli_affected_rows($c) 
              . ' affected rows.';
  }

  if($db_answer === FALSE)
  {
    // get actual response
    return mysqli_error($c);
  }

  // database is trying to return something
  $i = 0;
  while( $row[$i] = mysqli_fetch_row($db_answer) )
  {
    $i++;
  }
  $fields = mysqli_fetch_fields($db_answer);

  mysqli_free_result($db_answer);
 
  if($row === NULL)
  {
    return '{empty}';
  }
  else
  {
    $retval = '<table style="color:red" border="1"><tr>';

    //make the table head such that the column names are shown
    foreach($fields as $field)
    {
      $retval = $retval . '<td><h4>' . $field->name . '</h4></td>';
    }
    $retval = $retval . '</tr>';

    for($j=0; $j<$i; $j++)
    {
      $retval = $retval . '<tr>';
      foreach($row[$j] as $item)
      {
        $retval = $retval . '<td>' . $item . '</td>';
      }
      $retval = $retval . '</tr>';
    }

    $retval = $retval . '</table>';
    return $retval;
  }
}

// (boolean)
function mysql_admin_complete_transaction($c, $orderid)
{
  $orderid = sanitize($orderid);

  $query = 'UPDATE Orders SET status="complete" WHERE order_id="' . $orderid
              . '" AND status="pendingpayment"';

  return mysqli_query($c, $query);  
}

// (boolean)
function mysql_admin_shipment_made($c, $orderid)
{
  $orderid = sanitize($orderid);

  $query = 'UPDATE Orders SET status="pendingpayment" WHERE order_id="' . $orderid
              . '" AND status="pendingshipment"';

  return mysqli_query($c, $query);  
}

// returns information used for running telemarketer report
// (array(array(string)))
function mysql_admin_tele_report($c)
{
  global $REG_USER_TABLE;

  $query = 'SELECT * FROM ' . $REG_USER_TABLE;

  $result = mysqli_query($c, $query);

  $i = 0;
  while( $row[$i] = mysqli_fetch_row($result) )
  {
    $i++;
  }
  $row[$i] = NULL;

  mysqli_free_result($result);

  return $row;
}

// returns the phone numbers associated with a username
// (string)
function mysql_admin_phone($c, $username)
{
  global $PHONE_TABLE;

  $query = 'SELECT number FROM ' . $PHONE_TABLE . ' WHERE username="' . $username .'"';
  
  $db_answer = mysqli_query($c, $query);
  
  if($db_answer === false || $db_answer === true)
  {
    $row[0] = '{empty}';
    return $row;
  }
  else
  {
    $i = 0;
    while( $row[$i] = mysqli_fetch_row($db_answer) )
    {
      $i++;
    }
  }

  return $row;
}

// returns the number of Registered Users
// (int)
function mysql_admin_tele_users($c)
{
  global $REG_USER_TABLE;
  $query = 'SELECT COUNT(*) FROM ' . $REG_USER_TABLE;

  $db_answer = mysqli_query($c, $query);
  
  $row = mysqli_fetch_row($db_answer);
  return $row[0];
}

// gets the address of a Registered_User
// (string)
function mysql_admin_get_address($c, $username)
{
  global $ADDRESS_TABLE;

/*TEMP ONLY*/return '{not yet implemented}';
  $query = 'SELECT street FROM ' . $ADDRESS_TABLE . ' WHERE username="' . $username .'"';
  
  $db_answer = mysqli_query($c, $query);
  
  if($db_answer === false || $db_answer === true)
  {
    $row[0] = '{empty}';
    return $row;
  }
  else
  {
    $i = 0;
    while( $row[$i] = mysqli_fetch_row($db_answer) )
    {
      $i++;
    }
  }
  return $row;
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//------------------------------------------------------------------------------
?>
