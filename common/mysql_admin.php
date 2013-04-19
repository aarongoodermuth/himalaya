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

  $query = 'UPDATE ' . $ADMIN_TABLE . ' SET asession="' . $cookie_val
              . '" WHERE username="' . $username . '"';
  mysqli_query($c, $query);
}

// checks if a proposed cookie login value exists in the database
// (boolean)
function mysql_admin_cookie_value_used( $c, $cookie_val )
{
  global $ADMIN_TABLE;

  $cookie_val = sanitize($cookie_val);

  if($results =  mysqli_query($c, 'SELECT * FROM ' . $ADMIN_TABLE 
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

// check if a usename and password combination are valid
// (boolean)
function mysql_admin_login_test( $c, $username, $password )
{
  global $ADMIN_TABLE;

  $username = sanitize($username);
  $password = sanitize($password);

  $query = 'SELECT * FROM ' . $ADMIN_TABLE . ' WHERE username="'
              . $username . '" AND password="' . $password . '"';

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
  
  $query = 'SELECT username FROM ' . $ADMIN_TABLE . ' WHERE asession="'
              . $cookie_val . '"';
 
  $results = mysqli_query($c, $query);
  if($results === false)
  {
    die('database error');
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

// returns the type of admin a user is
// (string)
function mysql_admin_get_type($c, $username)
{
  global $ADMIN_USER_TYPE_MAPPING, $ADMIN_TABLE;

  $username = sanitize($username);

  $query = 'SELECT atype FROM '. $ADMIN_TABLE . ' WHERE username="' . $username . '"';

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

  $query = 'UPDATE ' . $ADMIN_TABLE . ' SET password="' . $password 
              . '" WHERE username="' . $username . '"';

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

  $query = 'DELETE FROM ' . $ADMIN_TABLE . ' WHERE username="' 
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

  $query = 'UPDATE Orders SET status="3" WHERE item_id="' . $orderid
              . '" AND status="2"';

  $db_answer = mysqli_query($c, $query);
  
  if($db_answer === false)
  {
    return false;
  }  
  else
  { 
    if( mysqli_affected_rows($c) > 0 )
    {
      return true;
    }
    else
    {
      return false;
    }
  }
}

// (boolean)
function mysql_admin_shipment_made($c, $orderid)
{
  $orderid = sanitize($orderid);

  $query = 'UPDATE Orders SET status="2" WHERE item_id="' . $orderid
              . '" AND status="1"';

  $db_answer = mysqli_query($c, $query);
  
  if($db_answer === false)
  {
    return false;
  }  
  else
  { 
    if( mysqli_affected_rows($c) > 0 )
    {
      return true;
    }
    else
    {
      return false;
    }
  }
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

  $query = 'SELECT pnum FROM ' . $PHONE_TABLE . ' WHERE username="' . $username .'"';
  
  $db_answer = mysqli_query($c, $query);
  
  if($db_answer === false || $db_answer === true)
  {
    $row[0] = '{empty}'; // should never see
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

  $query = 'SELECT street, zip FROM ' . $ADDRESS_TABLE . ' WHERE username="' . $username .'"';
 
  $db_answer = mysqli_query($c, $query);
  
  if($db_answer === false || $db_answer === true)
  {
    $row[0] = 'could not find address'; // should never see this
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

// returns a row of zip code infor for a given zip code
// (string[])
function mysql_admin_zip($c, $zip)
{
  $zip = sanitize($zip);

  global $ZIP_TABLE;

  $query = 'SELECT zip, city, zstate FROM ' . $ZIP_TABLE . ' WHERE zip="' . $zip .'"';

  $db_answer = mysqli_query($c, $query);
  
  if($db_answer === false)
  {
    $row[0] = '{error}'; // should never see
    $row[1] = '{error}'; // should never see
    $row[2] = '{error}'; // should never see
    return $row;
  }
  else
  {
    $row = mysqli_fetch_row($db_answer);
  }

  return $row;
}

// returns all the entries of items that are found in the Products table but 
//    not in the Sale_Items table
// (string[][])
function mysql_admin_outofstock($c)
{
  global $SUPPLIERS_TABLE, $PRODUCTS_TABLE;

  $query = 'SELECT p_name, category_name, p_desc 
            FROM Products p, Sale_Items s, Categories c 
            WHERE p.product_id NOT IN (SELECT product_id FROM Sale_Items)
                  AND c.category_id=p.category_id
            GROUP BY category_name'; 
  
  $db_answer = mysqli_query($c, $query);
  if($db_answer === false)
  {
    return null;
  }

  if(mysqli_num_rows($db_answer) == 0)
  {
    return null;
  }

  $i = 0;
  while( $cur= mysqli_fetch_row($db_answer) )
  {
    $row[$i] = $cur;
    $i++;
  }

  return $row;
}

// returns all the entries of items that are found in the Products table but 
//    have only 1 or 2 items in the Sale_Items table
// (string[][])
function mysql_admin_lowstock($c)
{
  global $SUPPLIERS_TABLE, $PRODUCTS_TABLE;

  $query = 'SELECT p_name, category_name, p_desc 
            FROM Products p, Sale_Items s, Categories c 
            WHERE p.product_id IN (SELECT product_id 
                                   FROM Sale_Items 
                                   HAVING COUNT(*)=1 OR COUNT(*)=2) 
                  AND c.category_id=p.category_id'; 
  
  $db_answer = mysqli_query($c, $query);
  if($db_answer === false)
  {
    return null;
  }

  if(mysqli_num_rows($db_answer) == 0)
  {
    return null;
  }
 
  $i = 0;
  while( $cur= mysqli_fetch_row($db_answer) )
  {
    $row[$i] = $cur;
    $i++;
  }

  return $row;
}

// returns all the entries of items that have been sold in the last week
// (string[][])
function mysql_admin_recently_sold($c)
{
  global $SUPPLIERS_TABLE, $PRODUCTS_TABLE;

  $query = 'SELECT p.p_name, p.p_desc, category_name, si.username, price/100 
            FROM Orders o, Sale_Items si, Products p, Categories c
            WHERE si.item_id=o.item_id
            AND   p.product_id=si.product_id
            AND   p.category_id=c.category_id
            AND   (o.action_date >= (CURDATE()-7))';
  /*$query = 'SELECT (SELECT p_name FROM Products p WHERE p.product_id =
                       (SELECT product_id from Sale_Items s WHERE o.item_id=s.item_id)), 
                   (SELECT p_desc FROM Products p WHERE p.product_id =
                       (SELECT product_id from Sale_Items s WHERE o.item_id=s.item_id)), 
                   (SELECT o.price from Sales s, Orders o), 
                   (SELECT item_desc FROM Sale_Items where o.item_id=item_id), 
                   (SELECT username FROM Sale_Items where o.item_id=item_id) 
            FROM Orders o 
            WHERE (o.action_date >= (CURDATE()-7))';*/
  
  $db_answer = mysqli_query($c, $query);
  if($db_answer === false)
  {
    return null;
  }
 
  if(mysqli_num_rows($db_answer) == 0)
  {
    return null;
  }

  $i = 0;
  while( $cur= mysqli_fetch_row($db_answer) )
  {
    $row[$i] = $cur;
    $i++;
  }

  return $row;
}

// ...
// (string)
function mysql_admin_get_email($c, $username)
{
  global $EMAIL_TABLE;

  $username = sanitize($username);

  $query = 'SELECT email FROM ' . $EMAIL_TABLE . ' WHERE username="' 
              . $username . '"';

  $db_answer = mysqli_query($c, $query);

  if($db_answer === false)
  {
    return '';
  }
  else
  {
    $row = mysqli_fetch_row($db_answer);
    if($row === null)
    {
      return '';
    }
    else
    {
      return $row[0];
    }
  }
}

// inserts gift card into database
// returns success status of insert attempt
// (boolean)
function mysql_admin_insert_gift_card($c, $number, $amount)
{
  global $GIFT_CARD_TABLE;

  $number = sanitize($number);
  $amount = sanitize($amount);
  
  $query = 'INSERT INTO ' . $GIFT_CARD_TABLE . ' VALUES("' . $number . '", "' . $amount . '")' ;
  return mysqli_query($c, $query);
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//------------------------------------------------------------------------------
?>
