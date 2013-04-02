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

// checks if all post values are set to non empty values
// (boolean)
function all_set()
{
  return 
            isset($_POST['number']) 
        && !empty($_POST['number']);
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

if($user != null)
{
  if(mysql_get_type_from_username($c, $user) == $USER_TYPE_MAPPING[0])
  {
    print_html_header(); // will change to nav header later
    echo '<h3>Redeem a Gift Card</h3>';

    if(all_set())
    {
      echo '<p style="color:red">';
      if( !mysql_member_redeem_gift_card($c, $_POST['number'], $user) )
      {
        echo 'Not a valid card number';
      }
      else
      {
        echo 'Gift Card successfully redeemed';
      }
      echo '</p>';
    }
    
    show_form('redeemgiftcard');
    echo '<p><a href="dashboard.php">Return to Dashboard</a></p>';
  }
  else
  {
    header('refresh:0; url=../suppliers/selling.php');
  }
}
else
{
  header('refresh:0; url=../welcome/login.php');
}

print_html_footer();
mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
