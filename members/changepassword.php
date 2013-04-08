<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/functions.php';
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
function print_this_html_header()
{
  print_html_header();
  echo '<body>';
  print_html_nav();
}

// sees if at least one value is set
// (boolean)
function values_set()
{
	return isset($_POST['oldpass']) || isset($_POST['newpass']) || isset($_POST['connewpass']);
}

// see is all values are set
// (boolean)
function all_set()
{
	return valid('oldpass') && valid('newpass') && valid('connewpass');
}

// checks if value is set and not blank
// (boolean)
function valid($val)
{
  $val = $_POST[$val];

  if(isset($val))
  {
    if(!empty($val))
    {
      return true;
    }
  }

  return false;
}

// check if new passwords match each other
// (boolean)
function pass_match($newpass, $connnewpass)
{
	return $newpass == $connnewpass;
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//-----------------------------------------------------------------------------

/**************/
/** PHP CODE **/
/**************/

$c = mysql_make_connection();
$username = check_logged_in_user($c);

if (values_set()) {
	if (all_set()) {
	/* 	echo get_post_var('oldpass') . ', ';
		echo get_post_var('newpass') . ', ';
		echo get_post_var('connewpass') */;
		if (!mysql_login_test($c, $username, get_post_var('oldpass'))) {
			print_this_html_header();
			echo '<div class="container-fluid">';
			echo '<p style="color:red">Your old password was incorrect.</p>';
		} elseif (!pass_match(get_post_var('newpass'), get_post_var('connewpass'))) {
			print_this_html_header();
			echo '<div class="container-fluid">';
			echo '<p style="color:red">New passwords do not match.</p>';
		} elseif (!mysql_change_password($c, $username, get_post_var('newpass'))) {
			print_this_html_header();
			echo '<div class="container-fluid">';
			echo '<p style="color:red">There was an error changing your password. Please try again.</p>';
		} else {  //all good
			print_this_html_header();
			echo '<div class="container-fluid">';
			echo '<p style="color:red">Password Successfully Changed!</p>';
		}
	} else {
		print_this_html_header();
		echo '<div class="container-fluid">';
		// error message not allowing to be blank
		echo '<p style="color:red">All values must be filled out</p>';
	}
} else {
	print_this_html_header();
	echo '<div class="container-fluid">';
}

show_form('changepassword');
echo '</div>';
print_html_footer2();
print_html_footer_js();
print_html_footer();

mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
