<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/functions.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql.php';

/******************/
/** END INCLUDES **/
/******************/

//------------------------------------------------------------------------------

/***************/
/** FUNCTIONS **/
/***************/


/*******************/
/** END FUNCTIONS **/
/*******************/

//-----------------------------------------------------------------------------

/**************/
/** PHP CODE **/
/**************/

$c = mysql_make_connection();
$user = check_logged_in_user($c);

if($user != null)
{
  $type = mysql_get_type_from_username($c, $user);
  if($type == $USER_TYPE_MAPPING[0])
  {
    print_html_header();
    if(1) // post values found
    {
      // get post values
      // ...

      // update DB
      // ...

      // print status message
      if(1) // success
      {
        echo '<p style="color:red">Account successfully updated</p>';
      }
      else // failure
      {
        echo '<p style="color:red">The database barfed at your values. It might
                 be our fault, but it also might be yours.</p>';
      }
    }
    show_form('editregisterduser');
    print_html_footer();
  }
  elseif($type == $USER_TYPE_MAPPING[1])
  {
    header('refresh:0; url=../supplier/editaccount.php');
  }
  else
  {
    header('refresh:0; url=../welcome/login.php');
  }
}
else
{
  header('refresh:0; url=../welcome/login.php');
}

mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
