<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/functions.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql.php';

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
            isset($_POST['name']) && isset($_POST['email']) && 
            isset($_POST['gender']) && isset($_POST['age']) && 
            isset($_POST['income'])
    &&
            !empty($_POST['name']) && !empty($_POST['email']) &&
            !empty($_POST['age']) &&  !empty($_POST['income']);
}

// checks to see if any post values are set and not empty
// (boolean)
function values_set()
{
  return     
         isset($_POST['gender'])
    ||
        (
           ( isset($_POST['name']) || isset($_POST['email']) || 
             isset($_POST['age']) ||   isset($_POST['income']) 
           )
        &&
           ( !empty($_POST['name']) || !empty($_POST['email']) ||
             !empty($_POST['age']) ||  !empty($_POST['income']) 
           )
         );
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
  $type = mysql_get_type_from_username($c, $user);
  if($type == $USER_TYPE_MAPPING[0])
  {
    print_html_header();
    if(all_set()) // post values found
    {
      // update DB
      $db_result = mysql_update_ru($c, $_POST['name'], $_POST['email'], 
                                       $_POST['gender'], $_POST['age'], 
                                       $_POST['income']);

      // print status message
      if($db_result === true) // success
      {
        echo '<p style="color:red">Account successfully updated</p>';
      }
      else // failure
      {
        echo '<p style="color:red">The database barfed at your values. It might
                 be our fault, but it also might be yours.</p>';
      }
    }
    elseif(values_set())
    {
      echo '<p style="color:red">All values must be entered</p>';
    }
    show_form('editregistereduser');
    print_html_footer();
  }
  elseif($type == $USER_TYPE_MAPPING[1])
  {
    header('refresh:0; url=../supplier/editaccount.php');
  }
  else
  {
    header('refresh:0; url=../welcome/login.php');
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
