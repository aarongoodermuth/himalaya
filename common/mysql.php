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
// gets type of user
//  $USER_TYPE_MAPPING[0]  => registered user
//  $USER_TYPE_MAPPING[1]  => supplier
//  null => non-user
function mysql_get_type_from_username($c, $uname)
{
  global $REG_USER_TABLE, $SUPPLIERS_TABLE, $USER_TYPE_MAPPING;

	$uname = sanitize($uname);  

	$str = "SELECT COUNT(*)
	        FROM $REG_USER_TABLE ru
	        WHERE  ru.username = ?";

	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 's', $uname);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $reg_user);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
	} else {
		printf("Errormessage: %s\n", mysqli_error($c));
		return NULL;
	}

    if ($reg_user === 1) {
        return $USER_TYPE_MAPPING[0];
    }
    
    
    $str = "SELECT COUNT(*)
	        FROM $SUPPLIERS_TABLE s
	        WHERE  s.username = ?";

	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 's', $uname);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $supplier);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
	} else {
		printf("Errormessage: %s\n", mysqli_error($c));
		return NULL;
	}

    if ($supplier === 1) {
        return $USER_TYPE_MAPPING[1];
    }

	return null;
}

//  get the corresponding city and state for a ZIP code
// (array[city, state] on success, NULL failure)
function get_zip_info($c, $zip)
{
	global $ZIP_TABLE;

	$str = "SELECT Z.city, Z.zstate FROM $ZIP_TABLE Z WHERE zip=?";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 's', $zip);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $city, $zstate);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
	} else {
		return false;
	}
	
	if ($city == NULL || $zstate == NULL) {
		return false;
	}
	
	return array($city, $zstate);
}

// create new record in Orders relation
// (boolean)
function mysql_create_order($c, $item_id, $purchaser, $price, $street, $zip, $cnumber)
{
	global $ORDERS_TABLE;
	
	$today = date("Y-m-d");
	
	$str = "INSERT INTO $ORDERS_TABLE VALUES (?, ?, 0, ?, ?, ?, ?, ?)";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'ississs', $item_id, $purchaser, $today, 
		                       $price, $street, $zip, $cnumber);
		if (!mysqli_stmt_execute($stmt)) {
			printf("Error: %s.\n", mysqli_stmt_error($stmt));
		}
		mysqli_stmt_fetch();
		mysqli_stmt_close($stmt);
		return true;
	}
	
	$root = $_SERVER['DOCUMENT_ROOT'];
	shell_exec('/usr/local/bin/catxml.sh > $root/common/categories.xml');

	return false;
}

// remove the Sales record with item_id
// (boolean)
function mysql_remove_sales_record($c, $item_id)
{
	global $SALES_TABLE;

	$str = "DELETE FROM $SALES_TABLE WHERE item_id=?";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'i', $item_id);
		if (!mysqli_stmt_execute($stmt)) {
			mysqli_stmt_close($stmt);
			return false;
		}
		mysqli_stmt_fetch();
		mysqli_stmt_close($stmt);
		return true;
	}
	
	$root = $_SERVER['DOCUMENT_ROOT'];
	shell_exec('/usr/local/bin/catxml.sh > $root/common/categories.xml');

	return false;
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//----------------------------------------------------------------------------------------
?>
