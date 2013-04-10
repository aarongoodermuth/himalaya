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
}

// sees if at least one value is set
// (boolean)
function values_set()
{
	return isset($_POST['username']);
}

// see is all values are set
// (boolean)
function all_set()
{
	return valid('username');
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

/*******************/
/** END FUNCTIONS **/
/*******************/

//-----------------------------------------------------------------------------

/**************/
/** PHP CODE **/
/**************/

$c = mysql_make_connection();

if (values_set()) {
	if (all_set()) {
		if (($newpass = reset_password($c, get_post_var('username'))) == NULL) {
			print_this_html_header();
			echo '<div class="container-fluid">';
			echo '<p style="color:red">There was a problem resetting your password. Please try again.</p>';
		} else {
			print_this_html_header();
			echo '<div class="container-fluid">';
			echo '<p style="color:red">New password created. Check your email for the new password.</p>';
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

show_form('recover');
echo '<a href="/welcome/login.php">Return to the login page</a>';
echo '</div><br>';
print_html_footer2();
print_html_footer_js();
print_html_footer();

mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
