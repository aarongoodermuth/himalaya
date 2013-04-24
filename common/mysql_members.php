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

  $str = "SELECT COUNT(*) FROM $MEMBERS_TABLE WHERE username=?";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 's', $username);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $count);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
	} else {
		return false;
  }

  return ($count == 0);
}

function reset_password($c, $username)
{
	global $MEMBERS_TABLE, $EMAIL_TABLE;
	
	$newpass = rand(1000000000, getrandmax());   /* 10-character random password */
	
	$hasher = new PasswordHash($hash_cost_log2, $hash_portable);
	$hash = $hasher->HashPassword($newpass);
	if (strlen($hash) < 20) {
		unset($hasher);
		return false;
	}
	unset($hasher);

	/* generate html and send the message */
	$message = 
	"<html>
	<head>
	  <title>Himalaya.biz - temporary password</title>
	</head>
	<body>
	  <h2>Himalaya.biz - temporary password</h2>
	  You are receiving this message because you indicated that you forgot your password, 
	  and submitted a request to reset your password. If you did not submit this request, 
	  we recommend you look into any possible breaches in security.<br><br>
	  Here is your new password for 
	  <a href=\"himalaya.goodermuth.com/members/changepassword.php\">himalaya.biz</a>:
	  <strong>$newpass</strong><br><br>
	  
	  We recommend that you immediately log in and 
	  <a href=\"himalaya.goodermuth.com/\">reset your password</a>. <br>
	  Be careful not to lose it again!<br><br>
	  
	  See you soon at Himalaya.biz,<br><br>
	  
	  -- The Himalaya Team<br>
	</body>
	</html>";
	
	if (file_put_contents("/tmp/recoverpass_$username.html", $message) === false) {
		return false;
	}
	
	$str = "SELECT E.email FROM $EMAIL_TABLE E WHERE username=?";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 's', $username);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $email);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
	} else {
		return false;
	}
	
	$str = "UPDATE $MEMBERS_TABLE SET password=? WHERE username=?";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'ss', $hash, $username);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	} else {
		return false;
	}

	/* send the email */
	shell_exec("/usr/local/bin/messagesend $email /tmp/recoverpass_$username.html \"temporary password\"");
	
	return true;
}

// check if a password is sufficiently strong
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
function mysql_member_create_ru($c, $username, $password, $name, $email, 
                                $gender, $address, $zip, $phone, $dob, $income)
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

  mysql_member_insert_phone($c, $username, $phone);
  mysql_member_insert_address($c, $address, $username, $zip, $name);

  $str = "INSERT INTO $RU_TABLE VALUES(?, ?, ?, ?, ?, 0)";
  if ($stmt = mysqli_prepare($c, $str)) {
    mysqli_stmt_bind_param($stmt, 'ssssi', $username, $name, $gender, $dob, $income);
    
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
	global $SUPPLIERS_TABLE, $MEMBERS_TABLE;

	if (!mysql_member_create_member($c, $username, $password)) {
		return false;
	} 
	
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
		return false;
	}

	return true;
}

// adds entry to the email table
// (boolean)
function mysql_member_add_email($c, $username, $email)
{
  global $EMAIL_TABLE;

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
function mysql_member_update_ru($c, $username, $name, $email, $gender, $dob, 
                                    $income, $street, $zip, $phone )
{
	global $RU_TABLE, $ADDRESS_TABLE, $PHONE_TABLE, $EMAIL_TABLE;

	$str = "UPDATE $RU_TABLE 
	        SET    name=?, gender=?, dob=?, income=? 
		WHERE  username=?";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'sssis', $name, $gender, 
		                              $dob, $income, $username);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	} else {
		return false;
	}
  
	$str = "UPDATE $ADDRESS_TABLE SET street=?, zip=?, username=?";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'sss', $street, $zip, $username);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	} else {
		mysqli_rollback($c);
		return false;
	}

	$str = "UPDATE $PHONE_TABLE SET pnum=? WHERE username=?";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'sss', $phone, $username);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	} else {
		mysqli_rollback($c);
		mysqli_rollback($c);
		return false;
	}
    	
	$str = "UPDATE $EMAIL_TABLE SET email=? WHERE username=?";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'ss', $email, $username);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	} else {
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

	$str = "UPDATE $SUPPLIERS_TABLE 
	        SET    company_name=?, contact_name=? 
		WHERE  username=?";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'sss', $company, $contact, $username);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	} else {
		return false;
	}

	return true;
}

// returns true if a zip code is found in the database
// (boolean)
function mysql_member_zip_exists($c, $zip)
{
  global $ZIP_TABLE;

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
function mysql_member_insert_address($c, $street, $username, $zip, $name)
{
	global $ADDRESS_TABLE;

	if(!mysql_member_zip_exists($c, $zip)) {
		return false;
	}

	$str = "INSERT INTO $ADDRESS_TABLE VALUES(?, ?, ?, ?)";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'ssss', $street, $zip, $name, $username);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
		return true;
	}

	return false;
}

// add a record to Can_Rate for a buyer and seller
// (boolean)
function mysql_member_insert_can_rate($c, $seller, $buyer, $item_id)
{
	global $CAN_RATE_TABLE, $USER_TYPE_MAPPING;

	/* 
	 * check that both the buyer and seller are Registered_Users;
	 * this is not really an error if the seller is a Supplier, but return false
	 */
	if (mysql_get_type_from_username($c, $buyer) != $USER_TYPE_MAPPING[0]) {
		return false;
	}

	$str = "INSERT INTO $CAN_RATE_TABLE VALUES (?, ?, ?)";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'ssi', $seller, $buyer, $item_id);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
		return true;
	}

	return false;
}

// create records in Credit_Cards and Has_Card relations
// (boolean)
function mysql_member_insert_credit_card($c, $username, $cnumber, $ctype, $cname, $expmonth, $expyear)
{
	global $CREDIT_CARDS_TABLE, $HAS_CARD_TABLE;

	$username = sanitize($username);
	$phone    = sanitize($cardnumber);

	switch($expmonth) {  /* determine last day of the month for the expiration date */
	case 1:
	case 3:
	case 5:
	case 7:
	case 8:
	case 10:
	case 12:
		$expday = 31;
		break;
	case 2:
		if ($expyear % 4 == 0)
			$expday = 29;
		else 
			$expday = 28;
		break;
	case 4:
	case 6:
	case 9:
	case 11:
		$expday = 30;
		break;
	default:
		return false;
		break;
	}
	
	$expdate = "$expyear-$expmonth-$expday";
	
	$str = "INSERT INTO $CREDIT_CARDS_TABLE VALUES(?, ?, ?, ?)";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'ssss', $cnumber, $ctype, $cname, $expdate);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	} else {
		return false;
	}
	
	$str = "INSERT INTO $HAS_CARD_TABLE VALUES(?, ?)";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'ss', $cnumber, $username);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	} else {
		return false;
	}
	
	return true;
}

// gets all info related to a registered_user
// string[]
function mysql_member_get_ru_info($c, $username)
{
  global $RU_TABLE, $EMAIL_TABLE, $ADDRESS_TABLE, $PHONE_TABLE;

  $username = sanitize($username);

  $query = 'SELECT name, email, gender, street, zip, pnum, dob, income FROM '
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

// place a bid on an item for a user
// (boolean)
function mysql_member_place_bid($c, $user, $item_id, $newbid)
{
	global $AUCTIONS_TABLE, $SALE_ITEMS_TABLE, $HAS_BID_TABLE;
	
	$str = "SELECT S.username FROM $SALE_ITEMS_TABLE S WHERE S.item_id=?";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'i', $item_id);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $seller);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
	} else {
		printf("Errormessage: %s\n", mysqli_error($c));
		return false;
	}
	
	if ($seller == $user) {  /* the seller of the item is trying to bid on this item */
		return false;
	}
	
	$str = "UPDATE $AUCTIONS_TABLE SET recent_bid=?, recent_bidder=? WHERE item_id=?";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'isi', $newbid, $user, $item_id);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
	} else {
		return false;
	}
	
	$str = "INSERT INTO $HAS_BID_TABLE VALUES (?, ?, ?)";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'iis', $item_id,  $newbid, $user);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
	} else {
		printf("Errormessage: %s\n", mysqli_error($c));
		return false;
	}
	
	$str = "UPDATE $HAS_BID_TABLE SET bid_amount=? WHERE item_id=? AND bidder=?";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'iis', $newbid, $item_id, $user);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
	} else {
		printf("Errormessage: %s\n", mysqli_error($c));
		return false;
	}
	
	return true;
}

// check if new bid is at least $2.00 more than the previous bid, or the
//   bid is >= to the starting bid if no bids have been placed yet
// (boolean)
function mysql_member_check_bid($c, $item_id, $newbid)
{
	global $AUCTIONS_TABLE;
	
	$str = "SELECT A.recent_bid, A.recent_bidder
	        FROM   $AUCTIONS_TABLE A WHERE A.item_id = ?";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'i', $item_id);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $recent_bid, $recent_bidder);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
	} else {
		return false;
	}
		
	/* no bid has been placed yet, new bid must match 
	 * at least the starting bid */
	if ($recent_bidder == NULL && $newbid >= $recent_bid) {
		return true;
	}
	
	/* there has been at least one bid */
	if ($newbid >= $recent_bid + 200) {
		return true;
	}
	
	return false;
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

// gets all items where a user is high bidder along with all its info
// (string[][])
function mysql_member_get_bidding($c, $user)
{
  global $AUCTIONS_TABLE, $SALE_ITEMS_TABLE;

  $user = sanitize($user);

  $query = 'SELECT SI.item_id, SI.item_desc, A.recent_bid, A.end_date 
            FROM ' . $SALE_ITEMS_TABLE . ' SI, ' . $AUCTIONS_TABLE . ' A 
            WHERE SI.item_id=A.item_id AND A.recent_bidder="' . $user . '"';
 
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

  $str = "SELECT O.item_id, SI.item_desc, O.price, O.status, O.action_date  
          FROM $SALE_ITEMS_TABLE SI, $ORDERS_TABLE O 
          WHERE SI.item_id = O.item_id
          AND SI.username = ?"; 
  if ($stmt = mysqli_prepare($c, $str)) 
  {
    mysqli_stmt_bind_param($stmt, 's', $user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $row[0], $row[1], $row[2], $row[3], $row[4]);
    mysqli_stmt_store_result($stmt);
    
    if(0 === mysqli_stmt_num_rows($stmt))
    {
      return null;
    }

    $i = 0;
    while(mysqli_stmt_fetch($stmt))
    {
      for($j=0; $j<5; $j++)
        $retval[$i][$j] = $row[$j];
      $i++;
    }

    mysqli_stmt_close($stmt);
    mysqli_stmt_free_result($stmt);
    
  } else {
	return $row = false;
  }

  if($row === false)
  {
    die('<p style="color:red">The database done goofed. This is definately our fault</p>');
  }
 
  return $retval;
}

function send_order_email($c, $item_id, $username, $cname)
{
	global $ORDERS_TABLE, $SALE_ITEMS_TABLE, $PRODUCTS_TABLE, $HAS_CARD_TABLE,
	       $CREDIT_CARDS_TABLE, $PHONE_TABLE, $EMAIL_TABLE, $ADDRESS_TABLE, 
	       $SITE_URL;
	
	$str = "SELECT R.action_date, R.price, R.street, R.zip, R.cnumber, 
                       P.p_name, S.username, S.shipping_zip,
                       C.ctype, C.cname, C.expiration
                FROM   $ORDERS_TABLE R, $SALE_ITEMS_TABLE S, $PRODUCTS_TABLE P, 
		       $HAS_CARD_TABLE H, $CREDIT_CARDS_TABLE C
                WHERE  R.item_id=? AND H.username=? AND H.cnumber=C.cnumber AND 
                       R.item_id = S.item_id AND P.product_id = S.product_id AND
                       C.cname=?;";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'iss', $item_id, $username, $cname);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $action_date, $price, $shiptostreet, $shiptozip, $cardnumber, 
		                               $product_name, $seller, $shipfromzip, 
					       $cardtype, $cardname, $cardexp);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
	} else {
		printf("Errormessage: %s\n", mysqli_error($c));
		return false;
	}
	
	if ($action_date == NULL || $price == NULL || $shiptostreet == NULL || $shiptozip == NULL || 
	    $cardnumber == NULL || $product_name == NULL || $seller == NULL || $shipfromzip == NULL ||
	    $cardtype == NULL || $cardname == NULL || $cardexp == NULL) {
		return false;
	}
	
	if (($shipfrom = get_zip_info($c, $shipfromzip)) == NULL) {
		echo "problem with from zip";
		return false;
	} elseif (($shipto = get_zip_info($c, $shiptozip)) == NULL) {
		echo "problem with to zip";
		return false;
	}
		
	$str = "SELECT A.name FROM $ADDRESS_TABLE A WHERE A.street=? AND A.zip=? AND A.username=?";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'sss', $shiptostreet, $shiptozip, $username);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $shiptoname);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
	} else {
		echo 'problem with second stmt';
		return false;
	}
	
	/* generate html and send the message */
	$message = "
	<html>
	<head>
	  <title>Himalaya.biz - order confirmation</title>
	</head>
	<body>
	  <h2>Himalaya.biz - order confirmation</h2>
	  You are receiving this message because you have purchased an item from 
	  <a href=\"$SITE_URL\">himalaya.biz</a>.<br><br>
	  
	  Here are your order details:
	  <h2>Order Summary</h2>
	  <h3>Item Information</h3>
	  <table>
	    <tr>
	      <td>Item Name:</td>
	      <td>$product_name</td>
	    </tr>
	    <tr>
	      <td>Seller:</td>
	      <td>$seller</td>
	    </tr>
	    <tr>
	      <td>Item Price:</td>
	";
	$message .= sprintf("<td>$%.2f</td>", $price / 100);
	$message .= "
	    </tr>
	    <tr>
	      <td>Shipping from:</td>
	      <td>$shipfrom[0], $shipfrom[1] $shipfromzip</td>
	    </tr>
	  </table>
	
	  <h3>Billing Information</h3>
	  <table>
	    <tr> 
	      <td>Name on Card:</td>
	      <td>$cardname</td>
	    </tr>
	    <tr>
	      <td>Card Type:</td>
	      <td>$cardtype</td>
	    </tr>
	    <tr>
	      <td>Card Number:</td>
	      <td>$cardnumber</td>
	    </tr>
	    <tr>
	      <td>Expiration Date:</td>
	      <td>$cardexp</td>
	    </tr>
	  </table>
	
	  <h3>Shipping Information</h3>
	  <table>
	    <tr>
	      <td>Full Name:</td>
	      <td>$shiptoname</td>
	    </tr>
	    <tr>
	      <td>Street Address:</td>
	      <td>$shiptostreet</td>
	    </tr>
	    <tr>
	      <td>City, State:</td>
	      <td>$shipto[0], $shipto[1]</td>
	    </tr> 
	    <tr>
	      <td>ZIP code:</td>
	      <td>$shiptozip</td>
	    </tr>
	  </table>
	  <br><br>
	  
	  Be on the lookout for more emails as your order progresses. 
	  Thanks for shopping with Himalaya.biz!<br><br>
	  
	  -- The Himalaya Team<br>
	</body>
	</html>";
	
	$filepath = "/tmp/order_". $item_id . "_" . "$username.html";
	
	if (file_put_contents("$filepath", $message) === false) {
		echo 'problem with put file contents';
		return false;
	}
	
	$str = "SELECT E.email FROM $EMAIL_TABLE E WHERE username=?";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 's', $username);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $email);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
	} else {
		echo 'problem with email stmt';
		return false;
	}

	/* send the email */
	shell_exec("/usr/local/bin/messagesend $email $filepath \"order confirmation\"");
	
	return true;
}

// posts the item that the user wants to post
// (boolean)
function post_item($c, $desc, $prod_id, $category, $condition, $url, $zip, $saletype, $abid, $areserve, $sprice)
{
    global $SALE_ITEMS_TABLE, $AUCTIONS_TABLE, $SALES_TABLE, $PRODUCTS_TABLE;

    // get smallest available item id for new posting
    $query = "SELECT MIN(s1.item_id) + 1 AS 'smallest'
                FROM $SALE_ITEMS_TABLE s1 LEFT OUTER JOIN $SALE_ITEMS_TABLE s2
                ON s2.item_id = s1.item_id + 1
                WHERE s2.item_id IS NULL;";
    $db_answer = mysqli_query($c, $query);
    $row = mysqli_fetch_row($db_answer);
    $item_id = $row[0];
    
    $uname = check_logged_in_user($c);
    
    $today = date("Y-m-d H:i:s");
    
    $str = "INSERT INTO $SALE_ITEMS_TABLE VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'isisisss', $item_id, $desc, $prod_id, $uname, $category, $url, $today, $zip);
		mysqli_stmt_execute($stmt);
        mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
	} else {
		printf("Errormessage: %s\n", mysqli_error($c));
		return false;
	}
    
    if ($saletype == 'S') {
        $str = "INSERT INTO $SALES_TABLE VALUES (?, ?)";
        if ($stmt = mysqli_prepare($c, $str)) {
            mysqli_stmt_bind_param($stmt, 'ii', $item_id, $sprice);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        } else {
            printf("Errormessage: %s\n", mysqli_error($c));
            return false;
        }
    }
    else if ($saletype == 'A') {
        $date = date("Y-m-d H:i:s"); // current date
        $end_date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($date)) . " +2 week"));
        
        $str = "INSERT INTO $AUCTIONS_TABLE VALUES (?, ?, ?, ?, NULL)";
        if ($stmt = mysqli_prepare($c, $str)) {
            mysqli_stmt_bind_param($stmt, 'iiis', $item_id, $areserve, $abid, $end_date);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        } else {
            printf("Errormessage: %s\n", mysqli_error($c));
            return false;
        }
    }
    else {
        return false;
    }
    
    shell_exec('sudo /usr/local/bin/cattoxml');
    return true;
}

// posts the rating that the user wants to post
// (boolean)
function post_rating($c, $seller_item, $rating, $desc)
{
    global $CAN_RATE_TABLE, $RATINGS_TABLE;

    $uname = check_logged_in_user($c);
    
    $today = date("Y-m-d");
    
    $pieces = explode("-", $seller_item);
    $seller = $pieces[0];
    $item = $pieces[1];
    
    /*rated_user
	rating_user
	rating
	review_date
	item_id
	review_description*/
    
    $str = "INSERT INTO $RATINGS_TABLE VALUES (?, ?, ?, ?, ?, ?)";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'ssisis', $seller, $uname, $rating, $today, $item, $desc);
		mysqli_stmt_execute($stmt);
        mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
	} else {
		printf("Errormessage: %s\n", mysqli_error($c));
		return false;
	}

    /*rated_user
	rating_user
	item_id*/
    $str = "DELETE FROM $CAN_RATE_TABLE WHERE rated_user=? AND rating_user=? AND item_id=?";
    if ($stmt = mysqli_prepare($c, $str)) {
        mysqli_stmt_bind_param($stmt, 'ssi', $seller, $uname, $item);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    } else {
        printf("Errormessage: %s\n", mysqli_error($c));
        return false;
    }

    return true;
}


/*******************/
/** END FUNCTIONS **/
/*******************/

//----------------------------------------------------------------------------------------
?>
