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
  return 
            isset($_POST['item_id']) && isset($_POST['item_name']) && 
            isset($_POST['price'])   && isset($_POST['shipping_zip'])
    &&
            !empty($_POST['item_name']) && !empty($_POST['price']) && !empty($_POST['shipping_zip']);
}

// checks to see if any post values are set and not empty
// (boolean)
function values_set()
{
	return 
	   ( isset($_POST['item_id']) || isset($_POST['item_name']) || 
	     isset($_POST['price']) ||   isset($_POST['shipping_zip'])
	   )
	&&
	   ( !empty($_POST['item_id']) || !empty($_POST['item_name']) ||
	     !empty($_POST['price']) ||  !empty($_POST['shipping_zip'])
	   )
	 ;
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
	$type = mysql_get_type_from_username($c, $user);
	if ($type == $USER_TYPE_MAPPING[1]) {
		header('refresh:0; url=/supplier/dashboard.php');
	} elseif($type != $USER_TYPE_MAPPING[0]) {
		header('refresh:0; url=../welcome/login.php');
	}
	
	print_this_html_header();
	
	if (all_set()) { // post values found
		// update DB
		$item_id = get_post_var('item_id');
		$item_name = get_post_var('item_name');
		$price = get_post_var('price');
		$seller = get_post_var('seller');
		$shipping_zip = get_post_var('shipping_zip');
		/* billing/shipping page */
		echo "
		<form name=\"purchase_billingshipping\" action=\"purchasesubmit.php\" method=\"post\">
		<input id=\"item_id\" type=\"hidden\" name=\"item_id\"/>
		<script>document.getElementById(\"item_id\").value=$item_id;</script>
		<input id=\"item_name\" type=\"hidden\" name=\"item_name\"/>
		<script>document.getElementById(\"item_name\").value=\"$item_name\";</script>
		<input id=\"shipping_zip\" type=\"hidden\" name=\"shipping_zip\"/>
		<script>document.getElementById(\"shipping_zip\").value=$shipping_zip;</script>
		<input id=\"price\" type=\"hidden\" name=\"price\"/>
		<script>document.getElementById(\"price\").value=$price;</script>
		<input id=\"seller\" type=\"hidden\" name=\"seller\"/>
		<script>document.getElementById(\"seller\").value=\"$seller\";</script>
		";
		
		show_form('purchase_billingshipping');
		
		/*if (!mysql_member_check_bid($c, get_post_var('item_id'), get_post_var('newbid') * 100)) {
			echo '<p style="color:red">The bid you entered was not at least $2.00 
				greater than the current bid (or was not at least the starting 
				bid). Please try again.</p>';
		} elseif (!mysql_member_place_bid($c, $user, get_post_var('item_id'), get_post_var('newbid') * 100)) {
			// unable to place bid
			echo '<p style="color:red">Unable to place bid. Please try again.</p>';
		} else {  // success
			printf("<p style=\"color:red\">Bid of $%.2f successfully placed!</p>", 
			       get_post_var('newbid'));
		}*/
		/*echo "<p style=\"color:red\">vals: id = " . get_post_var('item_id') .
		      ", name = " . get_post_var('item_name') . ", price = " . get_post_var('price') . ", zip = " . 
		      get_post_var('shipping_zip') . "</p>";*/
		      
		 
	} elseif (!values_set()) {
		echo '<p style="color:red">There was a problem. Please try again.</p>';
	}
	echo "<a href=\"/items/view.php?id=" . get_post_var('item_id') . 
	     "\">Return to the sale page</a><br>
	      </div>";
	print_html_footer2();
	print_html_footer_js();
	print_html_footer();
} else {
	header('refresh:0; url=../welcome/login.php');
}

mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>