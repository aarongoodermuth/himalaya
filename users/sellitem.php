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

// trims all leading and trailing whitespace from POST fields
// (no retval)
function trim_all()
{
    $_POST['desc']      = trim($_POST['desc']);
    $_POST['prod_id']   = trim($_POST['prod_id']);
    $_POST['category']  = trim($_POST['category']);
    $_POST['condition'] = trim($_POST['condition']);
    $_POST['url']       = trim($_POST['url']);
    $_POST['zip']       = trim($_POST['zip']);
    $_POST['saletype']  = trim($_POST['saletype']);
    $_POST['abid']      = trim($_POST['abid']);
    $_POST['areserve']  = trim($_POST['areserve']);
    $_POST['sprice']    = trim($_POST['sprice']);
}

// makes sure all necessary POST fields are set for submission to database
// (boolean)
function all_set()
{
    return (!empty($_POST['desc']) && !empty($_POST['prod_id']) && !empty($_POST['category']) &&
    (!empty($_POST['condition']) || $_POST['condition'] == 0)  && !empty($_POST['zip']) && !empty($_POST['saletype'])
    && (($_POST['saletype'] == 'A' && !empty($_POST['abid'])) || ($_POST['saletype'] == 'S' && !empty($_POST['sprice']))));
}

// checks to see if the user tried to submit the form with any data (aka not redirected here from another page)
// (boolean)
function any_set()
{
    return (!empty($_POST['desc']) || !empty($_POST['prod_id']) || !empty($_POST['category']) ||
    !empty($_POST['condition']) || !empty($_POST['url']) || !empty($_POST['zip']) || !empty($_POST['saletype']) ||
    !empty($_POST['saletype']) || !empty($_POST['abid']) || !empty($_POST['areserve']) || !empty($_POST['sprice']));
}

// print for debugging purposes
// (no retval)
function print_post_vals()
{
    echo 'desc:' . $_POST['desc'] . ' prod_id:' . $_POST['prod_id'] . ' category:' . $_POST['category'] . 
        ' condition:' . $_POST['condition'] . ' url:' . $_POST['url'] . ' zip:' . $_POST['zip'] . 
        ' saletype:' . $_POST['saletype'] . ' abid:' . $_POST['abid'] . ' areserve:' . $_POST['areserve'] . 
        ' sprice:' . $_POST['sprice'] . ' ||| ' . all_set();
}

// print for debugging purposes
// (no retval)
function print_errors()
{
    echo '<p style="color:red">';
    if (empty($_POST['desc']))
    {
        echo 'no description <br>';
    }
    if (empty($_POST['prod_id']))
    {
        echo 'no product id <br>';
    }
    if (empty($_POST['category']))
    {
        echo 'no category <br>';
    }
    if (empty($_POST['condition']))
    {
        echo 'no condition <br>';
    }
    if (empty($_POST['url']))
    {
        echo 'no URL (optional) <br>';
    }
    if (empty($_POST['zip']))
    {
        echo 'no ZIP code <br>';
    }
    if (empty($_POST['saletype']) || ($_POST['saletype'] != 'A' && $_POST['saletype'] != 'S'))
    {
        echo 'no saletype <br>';
    }
    if ($_POST['saletype'] == 'A' && empty($_POST['abid']))
    {
        echo 'auction selected but no starting bid set <br>';
    }
    if ($_POST['saletype'] == 'A' && empty($_POST['areserve']))
    {
        echo 'auction selected but no reserve price set (optional) <br>';
    }
    if ($_POST['saletype'] == 'S' && empty($_POST['sprice']))
    {
        echo 'sale selected but no price set <br> ';
    }
    echo '</p>';
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
trim_all();

if($user != null)
{
  $type = mysql_get_type_from_username($c, $user);
  if($type == $USER_TYPE_MAPPING[0] || $type == $USER_TYPE_MAPPING[1])
  {
    // is a Member
    print_html_header();
    echo '<body>';
    print_html_nav();
    echo '<div class="container-fluid">';
    echo '<p style="color:teal">';
    //print_post_vals();
    if (any_set())
    {
      if (all_set())
      {
        post_item($c, $_POST['desc'], $_POST['prod_id'], $_POST['category'], $_POST['condition'], $_POST['url'], $_POST['zip'], $_POST['saletype'], $_POST['abid']*100, $_POST['areserve']*100, $_POST['sprice']*100);
      }
      else
      {
        print_errors();
      }
    }
    echo '</p></div>';
    echo '<div class="container-fluid">';
    show_form('sellitem');
    echo '</div>';
    print_html_footer2();
    print_html_footer_js();
    print_html_footer();
  }
  else
  {
    die('The database done goofed!');
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