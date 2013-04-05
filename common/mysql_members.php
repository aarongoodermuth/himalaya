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

// checks if this username is already in the database
// (boolean)
function mysql_member_username_unique($c, $username)
{
  global $MEMBERS_TABLE;
  $username = sanitize($username);

  $query = 'SELECT COUNT(*) FROM ' . $MEMBERS_TABLE . ' WHERE username="' 
              . $username . '"';
  
  $db_answer = mysqli_query($c, $query);

  $row = mysqli_fetch_row($db_answer);
  return ($row[0] == 0);
}

function my_pwqcheck($newpass, $oldpass = '', $user = '')
{
        global $use_pwqcheck, $pwqcheck_args;
        if ($use_pwqcheck)
                return pwqcheck($newpass, $oldpass, $user, '', $pwqcheck_args);

	/* Some really trivial and obviously-insufficient password strength checks -
	 * we ought to use the pwqcheck(1) program instead. */
        $check = '';
        if (strlen($newpass) < 7)
                $check = 'way too short';
        else if (stristr($oldpass, $newpass) ||
            (strlen($oldpass) >= 4 && stristr($newpass, $oldpass)))
                $check = 'is based on the old one';
        else if (stristr($user, $newpass) ||
            (strlen($user) >= 4 && stristr($newpass, $user)))
                $check = 'is based on the username';
        if ($check)
                return "Bad password ($check)";
        return 'OK';
}

// inserts a member into the database
// (boolean)
function mysql_member_create_member($c, $username, $password)
{  
  global $MEMBERS_TABLE, $hash_cost_log2, $hash_portable;

  if (!preg_match('/^[a-zA-Z0-9_]{1,60}$/', $username))
    return false;
  
  $hasher = new PasswordHash($hash_cost_log2, $hash_portable);
  
  /*if (($check = my_pwqcheck($password, '', $username)) != 'OK') {
    unset($hasher);
    return false;
  }*/
  
  $hash = $hasher->HashPassword($password);
  if (strlen($hash) < 20) {
    unset($hasher);
    return false;
  }
  unset($hasher);
  
  $str = "INSERT INTO $MEMBERS_TABLE VALUES(?, ?, NULL)";
  if ($stmt = mysqli_prepare($c, $str)) {
	mysqli_stmt_bind_param($stmt, 'ss', $username, $hash);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	return true;
  }

  return false;
}

// inserts a registered user into the database
// (boolean)
function mysql_member_create_ru($c, $username, $password, $name, $email, $gender, $address, $zip, 
                                 $phone, $age, $income)
{
  global $RU_TABLE, $MEMBERS_TABLE;

  if(!mysql_member_create_member($c, $username, $password))
  {
    return false;
  }

  if(!mysql_member_add_email($c, $username, $email))
  {
    $str = "DELETE FROM $MEMBERS_TABLE WHERE username=?";
    if ($stmt = mysqli_prepare($c, $str)) {
      mysqli_stmt_bind_param($stmt, 's', $username);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
    }
    return false;
  }

  $username = sanitize($username);
  $name     = sanitize($name);
  $email    = sanitize($email);
  $gender   = sanitize($gender);
  $age      = sanitize($age);
  $income   = sanitize($income);

  mysql_member_insert_phone($c, $username, $phone);
  mysql_member_insert_address($c, $address, $username, $zip);

  $str = "INSERT INTO $RU_TABLE VALUES(?, ?, ?, ?, ?, 0)";
  if ($stmt = mysqli_prepare($c, $str)) {
    mysqli_stmt_bind_param($stmt, 'sssii', $username, $name, $gender, $age, $income);
    
    if (!mysqli_stmt_execute($stmt)) {
      $str = "DELETE FROM $MEMBERS_TABLE WHERE username=?";
      if ($stmt = mysqli_prepare($c, $str)) {
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
      }
      if ($stmt = mysqli_prepare($c, $str)) {
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
      }
    } else {
      mysqli_stmt_close($stmt);
    }
  }

  return true;
}

// inserts a member and a supplier into the database 
// (boolean)
function mysql_member_create_supplier($c, $username, $password, $company, $contact)
{
	/*$username = sanitize($username);
	$company  = sanitize($company);
	$contact  = sanitize($contact);*/

	global $SUPPLIERS_TABLE, $MEMBERS_TABLE;

	if (!mysql_member_create_member($c, $username, $password)) {
		return false;
	} else if (!mysql_member_add_email($c, $username, $email)) {
		$str = "INSERT INTO $SUPPLIERS_TABLE VALUES(?, ?, ?)";
		if ($stmt = mysqli_prepare($c, $str)) {
			mysqli_stmt_bind_param($stmt, 'sss', $username, $company, $contact);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
		} else {
			$str = "DELETE FROM $MEMBERS_TABLE WHERE username=?";
			if ($stmt = mysqli_prepare($c, $str)) {
				mysqli_stmt_bind_param($stmt, 's', $username);
				mysqli_stmt_execute($stmt);
				mysqli_stmt_close($stmt);
			}
		}
		return false;
	}

	return true;
}

// adds entry to the email table
// (boolean)
function mysql_member_add_email($c, $username, $email)
{
  global $EMAIL_TABLE;

  $username = sanitize($username);
  $email    = sanitize($email);

  $str = "INSERT INTO $EMAIL_TABLE VALUES(?, ?)";
  if ($stmt = mysqli_prepare($c, $str)) {
	mysqli_stmt_bind_param($stmt, 'ss', $username, $email);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	return true;
  }
  
  return false;
}

// updates RU info. Returns true on sucess, false on failure
// (boolean)
function mysql_member_update_ru($c, $username, $name, $email, $gender, $age, 
                                    $income, $street, $zip, $phone )
{
  global $RU_TABLE, $ADDRESS_TABLE, $PHONE_TABLE, $EMAIL_TABLE;

  $username = sanitize($username);
  $name     = sanitize($name);
  $email    = sanitize($email);
  $gender   = sanitize($gender);
  $age      = sanitize($age);
  $income   = sanitize($income);
  $street   = sanitize($street);
  $zip      = sanitize($zip);
  $phone    = sanitize($phone);

  $query = 'UPDATE ' . $RU_TABLE . 
          ' SET name="'   . $name   . '",' .
          ' gender="' . $gender . '",' .
          ' age="'    . $age    . '",' .
          ' income="' . $income . '" ' .
          ' WHERE username="' . $username . '"';
  if( !mysqli_query($c, $query) ){ echo $query;return false;}

  $query = 'UPDATE ' . $ADDRESS_TABLE . 
          ' SET street="' . $street . '", ' . 
          ' zip="'    . $zip    . '" ' .
          ' WHERE username="' . $username . '"';
  if( !mysqli_query($c, $query) )
  {echo $query;
    mysqli_rollback($c);
    return false;
  }

  $query = 'UPDATE '. $PHONE_TABLE .
          ' SET pnum="' . $phone . '" ' . 
          ' WHERE username="' . $username . '"';
  if( !mysqli_query($c, $query) )
  {echo $query;
    mysqli_rollback($c);
    mysqli_rollback($c);
    return false;
  }  
 
  $query = 'UPDATE ' . $EMAIL_TABLE .
          ' SET email="' . $email . '"' .
          ' WHERE username="' . $username . '"';
  if( !mysqli_query($c, $query) )
  {
    mysqli_rollback($c);
    mysqli_rollback($c);
    mysqli_rollback($c);
    return false;
  }
 
  return true;
}

// updates supplier info. returns true on success, false on failure
// (boolean)
function mysql_member_update_supplier($c, $username, $company, $contact)
{
  global $SUPPLIERS_TABLE;

  $username = sanitize($username);
  $company  = sanitize($company);
  $contact  = sanitize($contact);

  $query = 'UPDATE ' . $SUPPLIERS_TABLE . 
          ' SET company_name="' . $company . '", ' . 
               'contact_name="' . $contact . '"'  .
          ' WHERE username="' . $username . '"';
  if( !mysqli_query($c, $query) )
  {
    return false;
  }
  
  return true;
}

// ...
// (boolean)
function mysql_member_new_password($c, $username, $password)
{
  global $MEMBERS_TABLE;

  $username = sanitize($username);
  $password = sanitize($password);

  $query = 'UPDATE ' . $MEMBERS_TABLE. ' SET password="' . $password . 
              '" WHERE username="' . $username . '"';

  return mysqli_query($c, $query);
}

// returns true if a zip code is found in the database
// (boolean)
function mysql_member_zip_exists($c, $zip)
{
  global $ZIP_TABLE;

  $zip = sanitize($zip);
  
  $str = "SELECT COUNT(*) FROM $ZIP_TABLE WHERE zip=?";
  if ($stmt = mysqli_prepare($c, $str)) {
	mysqli_stmt_bind_param($stmt, 's', $zip);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $count);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
  }

  if ($count == 1)
    return true;

  return false;
}

// ...
// (boolean)
function mysql_member_insert_phone($c, $username, $phone)
{
  global $PHONE_TABLE;

  $username = sanitize($username);
  $phone    = sanitize($phone);

  $str = "INSERT INTO $PHONE_TABLE VALUES(?, ?)";
  if ($stmt = mysqli_prepare($c, $str)) {
    mysqli_stmt_bind_param($stmt, 'ss', $username, $phone);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return true;
  }

  return false;
}

// ...
// (boolean)
function mysql_member_insert_address($c, $street, $username, $zip)
{
  global $ADDRESS_TABLE;

  $street   = sanitize($street);
  $zip      = sanitize($zip);
  $username = sanitize($username);

  if(!mysql_member_zip_exists($c, $zip))
  {
    return false;
  }
  
  $str = "INSERT INTO $ADDRESS_TABLE VALUES(?, ?, ?)";
  if ($stmt = mysqli_prepare($c, $str)) {
    mysqli_stmt_bind_param($stmt, 'sss', $street, $zip, $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return true;
  }

  return false;
}

// gets all info related to a registered_user
// string[]
function mysql_member_get_ru_info($c, $username)
{
  global $RU_TABLE, $EMAIL_TABLE, $ADDRESS_TABLE, $PHONE_TABLE;

  $username = sanitize($username);

  $query = 'SELECT name, email, gender, street, zip, pnum, age, income FROM '
              . $RU_TABLE . ' r, ' . $EMAIL_TABLE . ' e, ' . $ADDRESS_TABLE 
              . ' a, ' . $PHONE_TABLE . ' p WHERE r.username=e.username AND ' . 
              'r.username=p.username AND r.username=a.username AND r.username="'
              . $username . '"';
  $db_answer = mysqli_query($c, $query);

  if($db_answer === false)
  {
    return array("", "", "", "", "", "", "", "");
  }

  return mysqli_fetch_row($db_answer);
}

// gets all info related to a supplier
// string[]
function mysql_member_get_supplier_info($c, $username)
{
  global $SUPPLIERS_TABLE;

  $username = sanitize($username);

  $query = 'SELECT company_name, contact_name FROM ' . $SUPPLIERS_TABLE 
              . ' WHERE username="' . $username . '"';
  
  $db_answer = mysqli_query($c, $query);

  if($db_answer === false)
  {
    return array("done", "goofed");
  }

  return mysqli_fetch_row($db_answer);
}

// ...
// (boolean)
function mysql_member_redeem_gift_card($c, $number, $username)
{
  global $GIFT_CARD_TABLE, $RU_TABLE;

  $number   = sanitize($number);
  $username = sanitize($username);

  $query = 'SELECT amount FROM ' . $GIFT_CARD_TABLE . ' WHERE code="' . $number . '"';
  $db_answer = mysqli_query($c, $query);
  if($db_answer === false || $db_answer === null)
  {
    return false;
  }

  if(mysqli_num_rows($db_answer) <= 0)
  {
    return false;
  }

  $row = mysqli_fetch_row($db_answer);
  if($row === null)
  {
    return false;
  }

  // prepare for query to update user's gift card balance
  $query = 'UPDATE ' . $RU_TABLE 
        . ' SET gift_card_balance=gift_card_balance + ' . $row[0] 
        . ' WHERE username="' . $username .'"';
  if( !mysqli_query($c, $query) )
  {
    return false;
  }

  $query = 'DELETE FROM ' . $GIFT_CARD_TABLE . 
          ' WHERE code="' . $number . '"';
  return mysqli_query($c, $query);
}

// ...
// (int)
function mysql_member_get_gift_card_balance($c, $username)
{
  global $RU_TABLE;

  $username = sanitize($username);

  $query = 'SELECT gift_card_balance FROM ' . $RU_TABLE 
        . ' WHERE username="' . $username . '"';

  $db_answer = mysqli_query($c, $query);
  
  $row = mysqli_fetch_row($db_answer);

  return $row[0];
}

// returns gift card balance for this user
// (int)
function mysql_member_gift_balance($c, $username)
{
  global $REG_USER_TABLE;
  
  $str = "SELECT gift_card_balance FROM $REG_USER_TABLE WHERE username=?";
  if ($stmt = mysqli_prepare($c, $str)) {
	mysqli_stmt_bind_param($stmt, 's', $username);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $balance);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
  }

  if ($balance > 0)
    return $balance;

  return -1;
}

// returns value of redeemed gift card if valid, -1 if invalid
// (int)
function mysql_member_gift_exists($c, $code)
{
  global $GIFT_CARDS_TABLE;
  $str = "SELECT COUNT(*) FROM $GIFT_CARDS_TABLE WHERE code=?";
  if ($stmt = mysqli_prepare($c, $str)) {
	mysqli_stmt_bind_param($stmt, 's', $code);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $count);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
  }

  if ($count == 1)
    return true;

  return false;
}

// (boolean)
function mysql_member_redeem_gift($c, $username, $code)
{
  global $GIFT_CARDS_TABLE, $REG_USER_TABLE;

  if(!mysql_member_gift_exists($c, $code))
  {
    return -1;
  }
  
  $str = "SELECT G.amount FROM $GIFT_CARDS_TABLE G WHERE G.code=?";
  if ($stmt = mysqli_prepare($c, $str)) {
	mysqli_stmt_bind_param($stmt, 's', $code);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $amount);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
  } else {
	return -1;
  }
    
  // update user's balance and delete the record in Gift_Cards
  $str = "UPDATE $REG_USER_TABLE SET gift_card_balance = gift_card_balance + ? WHERE username=?";
  if ($stmt = mysqli_prepare($c, $str)) {
	mysqli_stmt_bind_param($stmt, "is", $amount, $username);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
  } else {
	return -1;
  }

  $str = "DELETE FROM $GIFT_CARDS_TABLE WHERE code=?";
  if ($stmt = mysqli_prepare($c, $str)) {
	mysqli_stmt_bind_param($stmt, 's', $code);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
  } else {
	return -1;
  }
  
  return $amount;
}

// change a user's password, return true on success
// assume username-password combination has already been checked
// (boolean)
function mysql_change_password($c, $username, $newpass)
{
	global $MEMBERS_TABLE;

	$hasher = new PasswordHash($hash_cost_log2, $hash_portable);

	/*if (($check = my_pwqcheck($password, '', $username)) != 'OK') {
	unset($hasher);
	return false;
	}*/

	$hash = $hasher->HashPassword($newpass);
	if (strlen($hash) < 20) {
		unset($hasher);
		return false;
	}
	unset($hasher);

	$str = "UPDATE $MEMBERS_TABLE SET password = ? WHERE username=?";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'ss', $hash, $username);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
		return true;
	} 

	return false;
}  
  
// gets all items a user is selling along with all its info
// (string[][])
function mysql_member_get_sales($c, $user)
{
  global $SALE_ITEMS_TABLE, $SALES_TABLE;

  $user = sanitize($user);

  $query = 'SELECT SI.item_id, SI.item_desc, S.price 
            FROM ' . $SALE_ITEMS_TABLE . ' SI, ' . $SALES_TABLE . ' S 
            WHERE SI.item_id=S.item_id AND SI.username="' . $user . '"';
  
  $db_answer = mysqli_query($c, $query);

  if($db_answer === false)
  {
    die('<p style="color:red">The database done goofed. This is definately our fault</p>');
  }
 
  if(0 === mysqli_num_rows($db_answer))
  {
    return null;
  }

  $i = 0;
  while($temp = mysqli_fetch_row($db_answer))
  {
    $retval[$i] = $temp;
    $i++;
  }

  return $retval;
}

// gets all items a user is selling along with all its info
// (string[][])
function mysql_member_get_auctions($c, $user)
{
  global $AUCTIONS_TABLE, $SALE_ITEMS_TABLE;

  $user = sanitize($user);

  $query = 'SELECT SI.item_id, SI.item_desc, A.recent_bid 
            FROM ' . $SALE_ITEMS_TABLE . ' SI, ' . $AUCTIONS_TABLE . ' A 
            WHERE SI.item_id=A.item_id AND SI.username="' . $user . '"';
 
  $db_answer = mysqli_query($c, $query);

  if($db_answer === false)
  {
    die('<p style="color:red">The database done goofed. This is definately our fault</p>');
  }
 
  if(0 === mysqli_num_rows($db_answer))
  {
    return null;
  }

  $i = 0;
  while($temp = mysqli_fetch_row($db_answer))
  {
    $retval[$i] = $temp;
    $i++;
  }

  return $retval;
}

// ...
// (void)
function mysql_member_get_orders($c, $user)
{
  global $ORDERS_TABLE, $SALE_ITEMS_TABLE;

  $str = "SELECT O.item_id, SI.item_desc, O.status, O.action_date  
          FROM $SALE_ITEMS_TABLE SI, $ORDERS_TABLE O 
          WHERE SI.item_id = O.item_id
          AND O.username = ?";
  if ($stmt = mysqli_prepare($c, $str)) 
  {
    mysqli_stmt_bind_param($stmt, 's', $user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $row[0], $row[1], $row[2], $row[3]);
    mysqli_stmt_store_result($stmt);
    
    if(0 === mysqli_stmt_num_rows($stmt))
    {
      return null;
    }

    mysqli_stmt_free_result($stmt);

    $i = 0;
    while(mysqli_stmt_fetch($db_answer))
    {
      for($j=0; $j<4; $j++)
        $retval[$i][$j] = $row[$j];
      $i++;
    }

    mysqli_stmt_close($stmt);
  } else {
	return $row = false;
  }


  if($row === false)
  {
    die('<p style="color:red">The database done goofed. This is definately our fault</p>');
  }
 

  return $retval;
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//----------------------------------------------------------------------------------------
?>
