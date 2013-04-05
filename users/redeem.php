<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/functions.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql_members.php';

/******************/
/** END INCLUDES **/
/******************/

//------------------------------------------------------------------------------

/***************/
/** FUNCTIONS **/
/***************/

// creates custom html header
// (void)
function print_this_html_header($c, $username)
{
  print_html_header();
  print_html_nav();
}

// checks if all post values are set to non empty values
// (boolean)
function all_set()
{
	return isset($_POST['code']) && !empty($_POST['code']);
}

// checks to see if any post values are set and not empty
// (boolean)
function values_set()
{
  return isset($_POST['code']) && !empty($_POST['code']);
}

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
    if(all_set()) // post values found
    {
      // update DB
      $amount = mysql_member_redeem_gift($c, $user, $_POST['code']);
      
      print_this_html_header($c, $user);
      // print status message
      if($amount === -1) // failure
      {
        echo '<p style="color:red">The code you entered is invalid. Please try again.</p>';
      }
      else                 // success
      {
        $balance = mysql_member_gift_balance($c, $user);
	printf ("<div class=\"container-fluid\"><p style=\"color:red\">Success! You have redeemed &#36;%.2f, and now have &#36;%.2f in gift card credit.</p></div>", 
	        $amount / 100, $balance / 100);
      }
    }
    elseif(values_set())
    {
      print_this_html_header($c, $user);
      echo '<p style="color:red">Please enter a gift card code.</p>';
    }
    else
    {
      print_this_html_header($c, $user);
    }
    show_form('redeem');
    print_html_footer2();
    print_html_footer_js();
    print_html_footer();
  }
  elseif($type == $USER_TYPE_MAPPING[1])
  {
    header('refresh:0; url=../supplier/redeem.php');
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
