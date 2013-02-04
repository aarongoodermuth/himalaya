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

// connects to the database (if it can, aborts connection otherwise)
// (mysqli_object)
function mysql_make_connection()
{
  global $MYSQL_SERVERNAME, $MYSQL_USERNAME, $MYSQL_PASSWORD, $DB_NAME;
  $mysqli_obj = mysqli_init();
  if(!$mysqli_obj)
  {  die('Could not connect');  }

//  $connected = mysqli_obj->real_connect($MYSQL_SERVERNAME, $MYSQL_USERNAME, $MYSQL_PASSWORD, $DB_NAME);
  
//  if (!$connected)
//  {
//    die('Could not connect');
//  }

  return $mysqli_obj;
}

// disconnects from the DB, even if connection is not open
// (void)
function mysql_disconnect( $mysqli_obj )
{
 // mysqli_obj->close();
}

// filters all inputs for SQL Injection
// (string)
function sanitize($input)
{
  //...
  return $input;
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//----------------------------------------------------------------------------------------
?>

