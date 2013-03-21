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
  echo '<html><head>';
  echo '<script type="text/javascript" 
           src="../common/javascript/editsupplier.js">
           </script>';
  echo '</head>';
  echo '<body onload="populate(' . get_supplier_args($c, $username) . ')">';
}

// gets the args from the database for this user formatted as a comma seperated
//     string
// (string)
function get_supplier_args($c, $username)
{
  $row = mysql_member_get_supplier_info($c, $username);
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
            isset($_POST['contact']) && isset($_POST['company']) 
    &&
            !empty($_POST['contact']) && !empty($_POST['company']); 
}

// checks to see if any post values are set and not empty
// (boolean)
function values_set()
{
  return     
        (
           ( isset($_POST['contact']) || isset($_POST['company']) ) 
        &&
           ( !empty($_POST['contact']) || !empty($_POST['company']) )
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
  if($type == $USER_TYPE_MAPPING[1])
  {
    if(all_set()) // post values found
    {
      // update DB
      $db_result = mysql_member_update_supplier($c, $user, $_POST['company'], $_POST['contact']); 

      print_this_html_header($c, $user);
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
      print_this_html_header($c, $user);
      echo '<p style="color:red">All values must be entered</p>';
    }
    else
    {
      print_this_html_header($c, $user);
    }
    show_form('editsupplier');
    echo '<p><a href="dashboard.php">Return to Dashboard</a></p>';
    print_html_footer();
  }
  elseif($type == $USER_TYPE_MAPPING[0])
  {
    header('refresh:0; url=../user/editaccount.php');
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
