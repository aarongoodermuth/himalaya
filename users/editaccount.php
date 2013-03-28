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
function print_this_html_header($c, $username)
{
  print_html_header2();
  echo '<script type="text/javascript" 
           src="../common/javascript/editru2.js">
           </script>';
  echo '<body onload="populate(' . get_ru_args($c, $username) . ')">';
}

// gets the args from the database for this user formatted as a comma seperated
//     string
// (string)
function get_ru_args($c, $username)
{
  $row = mysql_member_get_ru_info($c, $username);
  $retval = '';

  for($i=0; $i<count($row) - 1; $i++)
  {
    $retval = $retval . "'" . $row[$i] . "', ";
  }
  return $retval . "'" . $row[$i] . "'";
}

// checks if all post values are set to non empty values
// (boolean)
function all_set()
{
  return 
            isset($_POST['name']) && isset($_POST['email']) && 
            isset($_POST['gender']) && isset($_POST['dob']) && 
            isset($_POST['income']) && isset($_POST['address']) &&
            isset($_POST['zip']) && isset($_POST['phone'])
    &&
            !empty($_POST['name']) && !empty($_POST['email']) &&
            !empty($_POST['dob']) &&  !empty($_POST['income']) &&
            !empty($_POST['address']) && !empty($_POST['zip']) &&
            !empty($_POST['phone']);
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
             isset($_POST['dob']) ||   isset($_POST['income']) ||
             isset($_POST['address']) || isset($_POST['zip']) ||
             isset($_POST['phone'])
           )
        &&
           ( !empty($_POST['name']) || !empty($_POST['email']) ||
             !empty($_POST['dob']) ||  !empty($_POST['income']) ||
             !empty($_POST['address']) || !empty($_POST['zip']) ||
             !empty($_POST['phone']) 
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
    if(all_set()) // post values found
    {
      // update DB
      $db_result = mysql_member_update_ru($c, $user, $_POST['name'], 
                                       $_POST['email'], 
                                       $_POST['gender'], $_POST['dob'], 
                                       $_POST['income'], $_POST['address'], 
                                       $_POST['zip'], $_POST['phone']);
      
      print_this_html_header($c, $user);
      // print status message
      if($db_result === true) // success
      {
        echo '<p style="color:red" onload="populate(' . get_ru_args($c, $user) . ')">Account successfully updated</p>';
      }
      else // failure
      {
        echo '<p style="color:red">The database barfed at your values. It might
                 be our fault, but it also might be yours.</p>';
      }
    }
    elseif(values_set())
    {
      print_this_html_header($c, $user);
      echo '<p style="color:red">All values must be entered</p>';
    }
    else
    {
      print_this_html_header($c, $user);
    }
    show_form('editregistereduser2');
    echo '<a href="dashboard.php">Return to Dashboard</a></div>';
    print_html_footer_js();
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
