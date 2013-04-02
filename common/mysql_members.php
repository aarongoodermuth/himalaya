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

// inserts a member into the database
// (void)
function mysql_member_create_member($c, $username, $password)
{
  $username = sanitize($username);
  $password = sanitize($password);

  global $MEMBERS_TABLE;

  $query = 'INSERT INTO ' . $MEMBERS_TABLE . ' VALUES("' . $username . 
              '", "' . $password . '", "0")';

  $db_answer = mysqli_query($c, $query);

  if($db_answer === false)
  {
    return false;
  }
  return true;
}

// inserts a member and a supplier into the database 
// (boolean)
function mysql_member_create_supplier($c, $username, $password, $company, $contact)
{
  $username = sanitize($username);
  $company  = sanitize($company);
  $contact  = sanitize($contact);

  global $SUPPLIERS_TABLE, $MEMBERS_TABLE;
  if(!mysql_member_create_member($c, $username, $password))
  {
    return false;
  }
  
  $query = 'INSERT INTO ' . $SUPPLIERS_TABLE . ' VALUES("' . $username .
              '", "' . $company . '", "' . $contact . '")';

  $db_answer = mysqli_query($c, $query);

  if($db_answer === false)
  {
    $query = 'DELETE FROM ' . $MEMBERS_TABLE . ' WHERE username="' . $username . '"';
    mysqli_query($c, $query);
    return false;
  }

  return true;
}

// inserts a registered user into the database
// (boolean)
function mysql_member_create_ru($c, $username, $password, $name, $email, $gender, $address, $zip, $phone, $age, $income)
{
  global $RU_TABLE, $MEMBERS_TABLE;
  
  if(!mysql_member_create_member($c, $username, $password))
  {
    return false;
  }

  if(!mysql_member_add_email($c, $username, $email))
  {
    $query = 'DELETE FROM ' . $MEMBERS_TABLE . ' WHERE username="' . $username 
                . '"';
    mysqli_query($c, $query);
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

  $query = 'INSERT INTO ' . $RU_TABLE . ' VALUES("' . $username 
              . '", "' . $name . '", "' . $gender . '", "' 
              . $age . '", "' . $income . '", "0")';
  
  $db_answer = mysqli_query($c, $query);

  if($db_answer === false)
  {
    $query = 'DELETE FROM ' . $MEMBERS_TABLE . ' WHERE username="' . $username 
                . '"';
    mysqli_query($c, $query);
    $query = 'DELETE FROM ' . $EMAIL_TABLE   . ' WHERE username="' . $username 
                . '" AND email="' . $email . '"';
    mysqli_query($c, $query);
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

  $query = 'INSERT INTO ' . $EMAIL_TABLE . ' VALUES("' . $username . '", "' 
              . $email . '")';
  $db_answer = mysqli_query($c, $query);
  if($db_answer === false)
  {
    return false;
  }

  return true;
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

// returns true if a zip code is found the database
// (boolean)
function mysql_member_zip_exists($c, $zip)
{
  global $ZIP_TABLE;

  $zip = sanitize($zip);

  $query = 'SELECT COUNT(*) FROM ' . $ZIP_TABLE . ' WHERE zip="' . $zip . '"';

  $db_answer = mysqli_query($c, $query);

  $row =  mysqli_fetch_row($db_answer);
  
  return $row[0][0];
}

// ...
// (boolean)
function mysql_member_insert_phone($c, $username, $phone)
{
  global $PHONE_TABLE;

  $username = sanitize($username);
  $phone    = sanitize($phone);

  $query = 'INSERT INTO ' . $PHONE_TABLE . ' VALUES("' . $username . '", "' 
              . $phone . '")';

  return mysqli_query($c, $query);
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

  $query = 'INSERT INTO ' . $ADDRESS_TABLE . ' VALUES("' . $street . '", "' 
              . $zip . '", "' . $username . '")';

  return mysqli_query($c, $query);
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

/*******************/
/** END FUNCTIONS **/
/*******************/

//----------------------------------------------------------------------------------------
?>

