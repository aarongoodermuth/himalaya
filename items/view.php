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

// checks if item get value is set to non-empty
// (boolean)
function value_set()
{
	return !empty($_GET['id']);
}

// prints HTML text in red
//  $s - string to print
function print_message($s)
{
	echo '<p style="color:red">' . $s . '</p>';
}

// gets information about the item from id
//  (string[])
function mysql_get_item_info($c, $id)
{
	global $ZIP_TABLE, $PRODUCTS_TABLE, $SALE_ITEMS_TABLE, 
	       $CATEGORY_TABLE, $ADDRESS_TABLE;

	$id = sanitize($id);  

	$str = "SELECT si.item_desc, si.product_id, si.username, 
		       si.scondition, si.url, si.posted_date, 
		       p.p_name, p.p_desc, p.category_id, 
		       c.category_name,
		       addr.zip,
		       z.city, z.zstate
	        FROM   $SALE_ITEMS_TABLE si, $PRODUCTS_TABLE p, 
		       $CATEGORY_TABLE c, $ADDRESS_TABLE addr, $ZIP_TABLE z
	        WHERE  si.item_id = ? AND si.product_id = p.product_id 
		       AND p.category_id = c.category_id 
		       AND si.username = addr.username AND addr.zip = z.zip";

	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'i', $id);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $item_desc, $product_id, $seller,
		                        $condition, $url, $posted_date, $p_name,
					$p_desc, $category_id, $category_name, 
					$zip, $city, $zstate);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
	} else {
		printf("Errormessage: %s\n", mysqli_error($c));
		return NULL;
	}

	return array($item_desc, $product_id, $seller, $condition, $url, 
	             $posted_date, $p_name, $p_desc, $category_id, 
	             $category_name, $zip, $city, $zstate);
}

// gets information about price from auctions or sales
//  (string[])
function mysql_get_price_info($c, $id)
{
	global $AUCTIONS_TABLE, $SALES_TABLE;

	$id = sanitize($id);

	$str = "SELECT COUNT(*) FROM $AUCTIONS_TABLE a, $SALES_TABLE sa WHERE
	a.item_id = ? OR sa.item_id = ?";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'ii', $id, $id);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $count);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
	} else {
		return NULL;
	}

	if ($count == 0) {
		return NULL;
	}

	$str = "SELECT a.recent_bid, a.end_date, a.recent_bidder 
	  FROM $AUCTIONS_TABLE a WHERE a.item_id = ?";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'i', $id);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $recent_bid, 
		                        $end_date, $recent_bidder);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
	} else {
		return NULL;
	}

	if (!empty($recent_bid) && !empty($end_date)) {
		return $retval = array('a', $recent_bid, 
		                       $end_date, $recent_bidder);
	}

	$str = "SELECT sa.price FROM $SALES_TABLE sa WHERE sa.item_id = ?";
	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 'i', $id);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $price);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
	} else {
		return NULL;
	}

	if (!empty($price)) {
		return $retval = array('s', $price);
	} else {
		return NULL;
	}
}

// gets average rating for the seller of this item
//  (array[float, int] on success, NULL failure)
function mysql_get_avg_rating($c, $username)
{
	global $REG_USER_TABLE, $RATINGS_TABLE; 

	$str = "SELECT   AVG(R.rating), COUNT(*)
	        FROM     $RATINGS_TABLE R
		WHERE    R.rated_user = ?
		GROUP BY R.rated_user";

	if ($stmt = mysqli_prepare($c, $str)) {
		mysqli_stmt_bind_param($stmt, 's', $username);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $avgrating, $count);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
	} else {
		printf("Errormessage: %s\n", mysqli_error($c));
		return NULL;
	}
	
	if (isset($avgrating) && isset($count))
		return array($avgrating, $count);
	else 
		return NULL;
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
$id = $_GET['id'];

if($user != NULL) {
  print_html_header();
  echo '<body>';
  print_html_nav();
  echo '<div class="container-fluid">';

  if($id != NULL) { // get item info
	if (($price_info = mysql_get_price_info($c, $id)) == NULL) {
		echo "couldn't get price info";
		// couldn't find an item
	} elseif (($item_info = mysql_get_item_info($c, $id)) == NULL) {
		echo "couldn't get item info";
		// couldn't get item info
	} else {
		// got all needed info
		// array('a', recent_bid, end_date, recent_bidder) or array('s', price)
		/* 
		 * array($item_desc, $product_id, $seller, $condition, $url, 
                 *       $posted_date, $p_name, $p_desc, $category_id, 
	         *       $category_name, $zip, $city, $zstate);
	         */
		$condition = int_to_condition($item_info[3]);
		echo "<h3>$item_info[6]</h3>\n";
		
		if ($price_info[0] == 's') {  /* sale */
			echo "<form name=\"purchase\" action=\"/users/purchase.php\" method=\"post\">";
		} else {                      /* auction */
			echo "<form name=\"placebid\" action=\"/users/placebid.php\" method=\"post\">";
		}

		echo
		"<input id=\"item_id\" type=\"hidden\" name=\"item_id\"/>
		<script>document.getElementById(\"item_id\").value=$id;</script>
		<input id=\"item_name\" type=\"hidden\" name=\"item_name\"/>
		<script>document.getElementById(\"item_name\").value=\"$item_info[6]\";</script>
		<input id=\"shipping_zip\" type=\"hidden\" name=\"shipping_zip\"/>
		<script>document.getElementById(\"shipping_zip\").value=$item_info[10];</script>
		<table>
		  <tr>
		    <td height=\"30\">Item Condition:</td>
		    <td>&nbsp;</td>
		    <td height=\"30\">$condition</td>
		  </tr>
		  <tr>
		    <td>Product Id:</td>
		    <td>&nbsp;</td>
		    <td>$item_info[1]</td>
		  </tr>
		  <tr>
		    <td>Item Id:</td>
		    <td>&nbsp;</td>
		    <td>$id</td>
		  </tr>
		  
		  <tr>
		    <td height=\"30\">Seller:</td>
		    <td>&nbsp;</td>
		    <td height=\"30\">
		      <a href=\"/users/view.php?username=$item_info[2]\">$item_info[2]</a>";
		
		if (($rating_info = mysql_get_avg_rating($c, $item_info[2])) != NULL) {
			printf("&nbsp;&nbsp;(%.1f/10 rating)", $rating_info[0]);
			echo "</td></tr>
			<tr>
			  <td>&nbsp;</td>
			  <td>&nbsp;</td>
			  <td>based on $rating_info[1] customer reviews";
		}
		
		echo '</td></tr>';
		
		if ($price_info[0] == 's') {  // sale
			echo 
			"<tr>
			  <td height=\"30\">Price:</td>
			  <td>&nbsp;</td>
			  ";
			printf("<td height=\"30\">$%.2f</td>", $price_info[1] / 100);
			echo 
			"<input id=\"price\" type=\"hidden\" name=\"price\"/>
			<script>document.getElementById(\"price\").value=$price_info[1];</script>
			</tr>
			<tr>
			  <td>&nbsp;</td>
			  <td>&nbsp;</td>
			  <td height=\"30\">
			    <input class=\"btn btn-primary btn-large btn-block\" value=\"Purchase\" type=\"submit\"/>
			  </td>
			</tr>";
		} elseif ($price_info[3] == NULL){    // auction, no bids yet
			echo 
			"<tr>
			  <td height=\"30\">Starting Bid:</td>
			  <td>&nbsp;</td>
			  ";
			printf("<td height=\"30\">$%.2f</td>", $price_info[1] / 100);
			echo
			"</tr>
			<tr>
			  <td><input type=\"text\" name=\"newbid\" placeholder=\"Bid Amount (USD)\"/></td>
			  <td>&nbsp;</td>
			  <td>
			    <div class=\"span2\">
			    <input class=\"btn btn-primary btn-large btn-block\" value=\"Place Bid\" type=\"submit\"/>
			    </div>
			  </td>
			  <td>&nbsp;</td>
			</tr>";
		} else {                      // auction, bids exist
			echo 
			"<tr>
			  <td height=\"30\">Current Bid:</td>
			  <td>&nbsp;</td>
			";
			printf("<td height=\"30\">$%.2f</td>", $price_info[1] / 100);
			echo
			"</tr>
			<tr>
			  <td height=\"30\"><input type=\"text\" name=\"newbid\" placeholder=\"Bid Amount (USD)\"/></td>
			  <td height=\"30\"><input class=\"btn btn-primary btn-large btn-block\" value=\"Place Bid\" type=\"submit\"></input></td>
			  <td>&nbsp;</td>
			</tr>
			<tr>
			";
			printf("<td height=\"30\">($%.2f or above)</td>", ($price_info[1] + 200) / 100);
			echo
			" <td>&nbsp;</td>
			  <td height=\"30\"></td>
			</tr>";
		}
		
		echo "</table></form><br><br>";
		echo "<h4>Product Description</h4>";
		echo "$item_info[7]<br><br>";
		echo "<h4>Item Description</h4>";
		echo "<p>$item_info[0]</p>
		      </div>";
	}
  } else { // no get value - redirect to 404 page
    header('refresh:0; url=/notfound.html');
  }

  print_html_footer2();
  print_html_footer_js();
  print_html_footer();
} else { // user not logged in
  header('refresh:0; url=/welcome/login.php');
}

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
