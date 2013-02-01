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
// (connection)
function mysql_connect()
{
  //...
  //test only
  $connection = 'test connection';
  return $connection;
}

// disconnects from the DB, even if connection is not open
// (void)
function mysql_disconnect( $connection )
{
  //...
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//----------------------------------------------------------------------------------------
?>

