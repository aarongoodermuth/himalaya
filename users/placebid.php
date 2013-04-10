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
function print_this_html_header()
{
  print_html_header();
  echo '<body>';  
  print_html_nav();
  echo '<div class="container-fluid">';
}

// checks if all post values are set to non empty values
// (boolean)
function all_set()
{
	return isset($_POST['item_id']) && !empty($_POST['item_id']) &&
	       isset($_POST['newbid'])  && !empty($_POST['newbid']);
}

// checks to see if any post values are set and not empty
// (boolean)
function values_set()
{
	return isset($_POST['item_id']) && !empty($_POST['item_id']) &&
	       isset($_POST['newbid']) && !empty($_POST['newbid']);
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

if ($user != null) {
	print_this_html_header();
	$type = mysql_get_type_from_username($c, $user);
	if ($type == $USER_TYPE_MAPPING[0]) {
		if (all_set()) { // post values found
			// update DB
			if (!mysql_member_check_bid($c, get_post_var('item_id'), get_post_var('newbid') * 100)) {
				echo '<p style="color:red">The bid you entered was not at least $2.00 
				        greater than the current bid (or was not at least the starting 
					bid). Please try again.</p>';
			} elseif (!mysql_member_place_bid($c, $user, get_post_var('item_id'), get_post_var('newbid') * 100)) {
				// unable to place bid
				echo '<p style="color:red">Unable to place bid. Please try again.</p>';
			} else {  // success
				printf("<p style=\"color:red\">Bid of $%.2f successfully placed!</p>", 
				       get_post_var('newbid'));
			}
		} elseif (!values_set()) {
			echo '<p style="color:red">No bid amount was entered. Please try again.</p>';
		}
		echo "<a href=\"/items/view.php?id=" . get_post_var('item_id') . 
		     "\">Return to the auction page</a>
		      </div>";
		print_html_footer2();
		print_html_footer_js();
		print_html_footer();
	} elseif ($type == $USER_TYPE_MAPPING[1]) {
		header('refresh:0; url=../supplier/dashboard.php');
	} else {
		header('refresh:0; url=../welcome/login.php');
	}
} else {
	header('refresh:0; url=../welcome/login.php');
}

mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
