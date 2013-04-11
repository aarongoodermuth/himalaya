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
            isset($_POST['item_id'])    && isset($_POST['item_name']) && 
            isset($_POST['price'])      && isset($_POST['shipping_zip']) &&
            isset($_POST['cardnumber']) && isset($_POST['cardname']) &&
            isset($_POST['expmonth'])   && isset($_POST['expyear']) &&
            isset($_POST['shiptoname']) && isset($_POST['shiptostreet']) &&
            isset($_POST['shiptozip'])  && isset($_POST['shiptophone'])
    &&
            !empty($_POST['item_name'])     && !empty($_POST['price'])         && 
	    !empty($_POST['shipping_zip'])  && !empty($_POST['cardnumber'])    && 
	    !empty($_POST['cardname'])      && !empty($_POST['expmonth'])      &&
            !empty($_POST['expyear'])       && !empty($_POST['shiptoname'])    && 
	    !empty($_POST['shiptostreet'])  &&     !empty($_POST['shiptozip']) &&
	    !empty($_POST['shiptophone']);
}

// checks to see if any post values are set and not empty
// (boolean)
function values_set()
{
	return 
	   ( isset($_POST['item_id'])    || isset($_POST['item_name'])    || 
	     isset($_POST['price'])      || isset($_POST['shipping_zip']) ||
	     isset($_POST['cardnumber']) || isset($_POST['cardname'])     ||
	     isset($_POST['expmonth'])   || isset($_POST['expyear'])      || 
	     isset($_POST['shiptoname']) || isset($_POST['shiptostreet']) ||
	     isset($_POST['shiptozip'])  || isset($_POST['shiptophone'])
	   )
	&&
	   ( !empty($_POST['item_id'])    || !empty($_POST['item_name'])    ||
	     !empty($_POST['price'])      || !empty($_POST['shipping_zip']) || 
	     !empty($_POST['cardnumber']) || !empty($_POST['cardname'])     || 
	     !empty($_POST['expmonth'])   || !empty($_POST['expyear'])      || 
	     !empty($_POST['shiptoname']) || !empty($_POST['shiptostreet']) || 
	     !empty($_POST['shiptozip'])  || !empty($_POST['shiptophone'])
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
		$shipping_zip = get_post_var('shipping_zip');
		$cardnumber = get_post_var('cardnumber');
		$cardname = get_post_var('cardname');
		$expmonth = get_post_var('expmonth');
		$expyear = get_post_var('expyear');
		$shiptoname = get_post_var('shiptoname');
		$shiptostreet = get_post_var('shiptostreet');
		$shiptozip = get_post_var('shiptozip');
		$shiptophone = get_post_var('shiptophone');
		
		/* get city and state for a ZIP code */
		if (($shipfrom = get_zip_info($c, $shipping_zip)) == NULL) {
			/* unable to get city and state for this ZIP code */
			echo '<p style="color:red">There was a problem. Please try again.</p>';
		} elseif(($shipto = get_zip_info($c, $shiptozip)) == NULL) {
			/* unable to get city and state for this ZIP code */
			echo '<p style="color:red">The address you provided seems to be invalid. 
			      Please try again.</p>';
		} else {
			echo "
			<h2>Order Summary</h2>
			<h3>Item Information</h3>
			<table>
			  <tr>
			    <td>Item Name:</td>
			    <td>$item_name</td>
			  </tr>
			  <tr>
			    <td>Item Price:</td>
			";
			printf("<td>$%.2f</td>", $price / 100);
			echo "
			  </tr>
			  <tr>
			    <td>Shipping from:</td>
			    <td>$shipfrom[0], $shipfrom[1] $shipping_zip</td>
			  </tr>
			</table>
			
			<h3>Billing Information</h3>
			<table>
			  <tr> 
			    <td>Name on Card:</td>
			    <td>$cardname</td>
			  </tr>
			  <tr>
			    <td>Card Number:</td>
			    <td>$cardnumber</td>
			  </tr>
			  <tr>
			    <td>Expiration Date:</td>
			    <td>$expmonth/$expyear</td>
			  </tr>
			</table>
			
			<h3>Shipping Information</h3>
			<table>
			  <tr>
			    <td>Full Name:</td>
			    <td>$shiptoname</td>
			  </tr>
			  <tr>
			    <td>Phone Number:</td>
			    <td>$shiptophone</td>
			  </tr>
			  <tr>
			    <td>Street Address:</td>
			    <td>$shiptostreet</td>
			  </tr>
			  <tr>
			    <td>City</td>
			    <td>$shipto[0]</td>
			  </tr> 
			  <tr>
			    <td>State</td>
			    <td>$shipto[1]</td>
			  </tr>
			  <tr>
			    <td>ZIP code</td>
			    <td>$shiptozip</td>
			  </tr>
			</table>
			<br>
			";
			
			echo "
			<form name=\"purchase_billingshipping\" action=\"purchaseconfirm.php\" method=\"post\">
			<input id=\"item_id\" type=\"hidden\" name=\"item_id\"/>
			<script>document.getElementById(\"item_id\").value=$item_id;</script>
			<input id=\"item_name\" type=\"hidden\" name=\"item_name\"/>
			<script>document.getElementById(\"item_name\").value=\"$item_name\";</script>
			<input id=\"shipping_zip\" type=\"hidden\" name=\"shipping_zip\"/>
			<script>document.getElementById(\"shipping_zip\").value=$shipping_zip;</script>
			<input id=\"cardnumber\" type=\"hidden\" name=\"cardnumber\"/>
			<script>document.getElementById(\"cardnumber\").value=$cardnumber;</script>
			<input id=\"cardname\" type=\"hidden\" name=\"cardname\"/>
			<script>document.getElementById(\"cardname\").value=$cardname;</script>
			<input id=\"expmonth\" type=\"hidden\" name=\"expmonth\"/>
			<script>document.getElementById(\"expmonth\").value=$expmonth;</script>
			<input id=\"expyear\" type=\"hidden\" name=\"expyear\"/>
			<script>document.getElementById(\"expyear\").value=$expyear;</script>
			<input id=\"shiptoname\" type=\"hidden\" name=\"shiptoname\"/>
			<script>document.getElementById(\"shiptoname\").value=$shiptoname;</script>
			<input id=\"shiptostreet\" type=\"hidden\" name=\"shiptostreet\"/>
			<script>document.getElementById(\"shiptostreet\").value=$shiptostreet;</script>
			<input id=\"shiptozip\" type=\"hidden\" name=\"shiptozip\"/>
			<script>document.getElementById(\"shiptozip\").value=$shiptozip;</script>
			<input id=\"shiptophone\" type=\"hidden\" name=\"shiptophone\"/>
			<script>document.getElementById(\"shiptophone\").value=$shiptophone;</script>
			<table>
			<tr>
			<td><input class=\"btn btn-primary btn-large btn-block\" value=\"Submit Order\" type=\"submit\"/></td>
			</tr>
			</table>
			</form>
			";
			
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
		}
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