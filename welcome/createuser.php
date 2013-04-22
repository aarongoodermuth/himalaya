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
  return isset($_POST['username']) || isset($_POST['password']);
}

// see is all values are set
// (boolean)
function all_set()
{
  return valid('username') && valid('password') && valid('name') && 
         valid('email') && valid('address') && valid('zip') && 
         valid('phone') && valid('dob') && valid('income');
}

// see if some of the values are actually numbers
// (boolean)
function type_check($c)
{
  $retval = true;
  $retval =  $retval && is_numeric($_POST['income']) && is_numeric($_POST['zip']);

  if($retval)
  {
    $retval = $retval && mysql_member_zip_exists($c, $_POST['zip']);
  }

  return $retval && (strlen($_POST['phone']) < 16);
}

// checks if value is set and not blank
// (boolean)
function valid($val)
{
  $val = $_POST[$val];

  if( isset($val) )
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

if( values_set() )
{
  if(all_set())
  {  
    if(mysql_member_username_unique($c, $_POST['username'])) {
      $goof = false;
 
      // create the user
      if (!type_check($c)) {
        $goof = true;
      } elseif (!is_valid_date(get_post_var('dob'))) {  // check date format
        $goof = true;
      } elseif(!mysql_member_create_ru($c, get_post_var('username'), get_post_var('password'), 
                        get_post_var('name'), get_post_var('email'), get_post_var('gender'), 
                        get_post_var('address'), get_post_var('zip'), get_post_var('phone'),
                        get_post_var('dob'), get_post_var('income'))) {
        $goof = true;
      } else {
        $goof = false;
      }
     
      if(!$goof) {
        // redirect to user page
        header('refresh:4; url=login.php');
        print_this_html_header();
        echo '<div class="container-fluid">';
        echo '<p style="color:red">Account Successfully Created. Redirecting</p>';
      } else {
        print_this_html_header();
        echo '<div class="container-fluid">';
        echo '<p style="color:red">Not all entries were valid</p>';
      }
    } else {
      print_this_html_header();
      echo '<div class="container-fluid">';
      echo '<p style="color:red">That username is already taken</p>';
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

show_form('createuser');
echo '<p><a href="login.php">Return to Login Screen</a></p>';
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
