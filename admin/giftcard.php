<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/functions.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql_admin.php';

/******************/
/** END INCLUDES **/
/******************/

//------------------------------------------------------------------------------

/***************/
/** FUNCTIONS **/
/***************/

// checks POST values to see if any fields are set
// (boolean)
function values_set()
{
  return
    ( isset($_POST['number']) && notempty($_POST['number']) ) ||
    ( isset($_POST['amount']) && notempty($_POST['amount']) ) ;
}

// checks POST values to see if all fields are set
// (boolean)
function all_set()
{
  return
    ( isset($_POST['number']) && notempty($_POST['number']) ) &&
    ( isset($_POST['amount']) && notempty($_POST['amount']) ) ;
}

// checks if a values is the empty string
// (boolean)
function notempty($val)
{
  return ($val != '');
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//-----------------------------------------------------------------------------

/**************/
/** PHP CODE **/
/**************/

$c = mysql_make_connection();
$user = check_logged_in_admin_user($c);

if($user != null)
{
  $type = mysql_admin_get_type($c, $user);
  if($type === $ADMIN_USER_TYPE_MAPPING[1] ||
     $type === $ADMIN_USER_TYPE_MAPPING[2] ||
     $type === $ADMIN_USER_TYPE_MAPPING[7] )
  {
    echo '<h3>Create Gift Card Record</h3>';
    if(all_set())
    {
      // insert gift card into DB
      $answer = mysql_admin_insert_gift_card($c, $_POST['number'], $_POST['amount']*100);

      // display success or failure
      echo '<p style="color:red">';
      if($answer)
      {
        echo 'Gift Card successfully created.';
      }
      else
      {
        echo 'The database goofed. It might be our fault, but it also might be yours.';
      }
      echo '</p>';
    }
    elseif(values_set())
    {
      // display error (all must be filled in)
      echo '<p style="color:red">All values must be filled in</p>';
    }
    show_form('gift_card_retailer');
  }
  else
  {
    header('refresh:0; url=dashboard.php');
  }
}
else
{
  header('refresh:0; url=login.php');
}

print_html_footer();
mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
