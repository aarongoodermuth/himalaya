<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';

/******************/
/** END INCLUDES **/
/******************/

//------------------------------------------------------------------------------

/***************/
/** FUNCTIONS **/
/***************/

// (void)
function print_html_header()
{
  echo file_get_contents('header.html', true);
}

// (void)
function print_html_nav()
{
  include 'navigation.php';
}

function print_html_nav_supplier()
{
  echo file_get_contents('navigation_supplier.html', true);
}

// (void)
function print_html_footer()
{
  echo '</body></html>';
}

// (void)
function print_html_footer2()
{
  echo file_get_contents('footer.html', true);
}

function print_html_footer_js()
{
  echo '
<!-- Load JS here for greater good =============================-->
<script src="/design/js/jquery-1.8.2.min.js"></script>
<script src="/design/js/jquery-ui-1.10.0.custom.min.js"></script>
<script src="/design/js/jquery.dropkick-1.0.0.js"></script>
<script src="/design/js/custom_checkbox_and_radio.js"></script>
<script src="/design/js/custom_radio.js"></script>
<script src="/design/js/jquery.tagsinput.js"></script>
<script src="/design/js/bootstrap-tooltip.js"></script>
<script src="/design/js/jquery.placeholder.js"></script>
<script src="/design/js/application.js"></script>
<!--[if lt IE 8]>
<script src="/design/js/icon-font-ie7.js"></script>
<script src="/design/js/icon-font-ie7-24.js"></script>
<![endif]-->
';
}

// checks if there is a user logged in. If so, resets the cookie and returns
//    username. If not, returns null
// (string || null)
function check_logged_in_user($c)
{
  global $COOKIE_NAME, $COOKIE_TIMEOUT;
 
  // if already logged in
  if( isset($_COOKIE[$COOKIE_NAME]) )
  {
    // get username that is associated with the cookie
    $rn = mysql_get_username_from_cookie($c, $_COOKIE[$COOKIE_NAME]);
    if($rn == null)
    {
      // delete cookie
      setcookie($COOKIE_NAME, null, 1, '/');
    }
    else
    {
      // reset cookie
      setcookie($COOKIE_NAME, $_COOKIE[$COOKIE_NAME], time() + $COOKIE_TIMEOUT, '/');
    }

    return $rn;
  }
  else
  {
    return null;
  }
}

// checks if there is admin user logged in. If so, resets the cookie and returns
//    username. If not, returns null
// (string || null)
function check_logged_in_admin_user($c)
{
  global $ADMIN_COOKIE_NAME, $COOKIE_TIMEOUT;
  
  // if already logged in
  if( isset($_COOKIE[$ADMIN_COOKIE_NAME]) )
  {
    // get username that is associated with the cookie
    $rn = mysql_admin_get_username_from_cookie($c, $_COOKIE[$ADMIN_COOKIE_NAME]);
    if($rn == null)
    {
      // delete cookie
      setcookie($ADMIN_COOKIE_NAME, null, 1);
    }
    else
    {
      // reset cookie
      setcookie($ADMIN_COOKIE_NAME, $_COOKIE[$ADMIN_COOKIE_NAME], time() + $COOKIE_TIMEOUT);
    }

    return $rn;
  }
  else
  {
    return null;
  }
}

// (void)
function show_form($form_name)
{
  $data = file_get_contents('/home/goodermuth/dev/websites/himalaya/common/forms/' 
                                       . $form_name . '.form');
  echo $data;
}

// filters all inputs for SQL Injection
// (string)
function sanitize($input)
{
  //...
  return $input;
}

function sanitize2($conn, $input)
{
  $data = mysqli_real_escape_string($conn, $input);
  return $data;
}

// undo the effects of magic quotes, if it is enabled
function get_post_var($var)
{
	$val = $_POST[$var];
	if (get_magic_quotes_gpc())
		$val = stripslashes($val);
	return $val;
}

function int_to_condition($cond) 
{
	switch(intval($cond))
	{
	case 0: 
		$s = 'New';
		break;
	case 1: 
		$s = 'Used - Like New';
		break;
	case 2: 
		$s = 'Used - Very Good';
		break;
	case 3: 
		$s = 'Used - Good';
		break;
	case 4: 
		$s = 'Used - Acceptable';
		break;
	case 5: 
		$s = 'Used - For Parts Only';
		break;
	default:
		$s = NULL;
		break;
	}
	
	return $s;
}

// check if a date is properly formed
// (boolean)
function is_valid_date($dob) 
{
	if (($year = strtok($dob, '-')) === false) {
		return false;
	} elseif (($month = strtok('-')) === false) {
		return false;
	} elseif(($day = strtok('-')) === false) {
		return false;
	} elseif (!checkdate($month, $day, $year)) {
		return false;
	}
	
	return true;
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//-----------------------------------------------------------------------------

/**************/
/** PHP CODE **/
/**************/



/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
