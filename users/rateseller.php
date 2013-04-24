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
  if($type == $USER_TYPE_MAPPING[0]) //RU
  {
    print_html_header();
    print_html_nav();
    echo '<div class="container-fluid">';
    echo '<p style="color:teal">';
    $_POST['desc'] = trim($_POST['desc']);
    if (isset($_POST['desc']) && !empty($_POST['desc']) && isset($_POST['seller_item']) && !empty($_POST['seller_item']) && post_rating($c, $_POST['seller_item'], $_POST['rating'], $_POST['desc']))
    {
      echo 'Rating Posted!';
    }
    echo '</p></div>';
    echo '<div class="container-fluid">';
    show_form('rateseller');
    echo '</div>';
    print_html_footer2();
    print_html_footer_js();
    print_html_footer();
  }
  else if ($type == $USER_TYPE_MAPPING[1]) //supplier
  {
    header('refresh:0; url=../supplier/dashboard.php');
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