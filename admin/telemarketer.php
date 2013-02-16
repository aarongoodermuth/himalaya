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

// prints a row of the table for a Registered User
// (html formatted string)
function print_report_row($c, $row)
{
  $i = 0;

  echo '<tr>';
  while($i < count($row) - 1) // 1 for the gift card balance,
  {

    echo '<td>' . $row[$i] . '</td>';
    $i++;
  }
  
  $phones = mysql_admin_phone($c, $row[0]);
  $address = mysql_admin_get_address($c, $row[0]);  

  echo '<td>' . $phones[0][0] . '</td>';
  echo '<td>' . $address[0][0] . '</td>';
  echo '<td>' . get_alt_phones($phones) . '</td>';
  echo '</tr>';
}

// gets all alternate phones as newline seperated string
// (string)
function get_alt_phones($row)
{
  $i = 1;
  $retval = '';

  while($i < count($row) - 1)
  {
    $retval = $retval . $row[$i][0] . '<br />';
    $i++;
  }
  
  //make sure we have enough phone numbers to break everything
  if(count($row) > 1)
  {
    $retval = $retval . $row[$i][0];
  }
  else
  {
    $retval = '';
  }

  return $retval;
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//-----------------------------------------------------------------------------

/**************/
/** PHP CODE **/
/**************/

$c = mysql_make_connection();
$username = check_logged_in_user($c);
$user_type = mysql_admin_get_type($c, $username);

if( $user_type == $ADMIN_USER_TYPE_MAPPING[1] ||
    $user_type == $ADMIN_USER_TYPE_MAPPING[2] ||
    $user_type == $ADMIN_USER_TYPE_MAPPING[3] )
{
  print_html_header();
  echo '<h3 style="text-align:center">Telemarketer Report</h3>';
  echo '<table style="text-align:center" align="center" border="1">';
  echo '<tr style="font-weight:bold; text-align:center"><td>Username</td><td>Name</td>
           <td>Email</td><td>Gender</td><td>Age</td><td>Income</td><td>Address</td>
           <td>Phone</td><td>Additional Phones</td></tr>';
  // run telemarketer report
  //...
  $rows = mysql_admin_tele_report($c);
  $i = 0;
  while( $rows[$i] != NULL && $rows[$i] != '')
  {
    print_report_row($c, $rows[$i]);
    $i++;
  }
  ///...
  echo '</table>';
  echo '<p><strong>Users: </strong>' . mysql_admin_tele_users($c) . '</p>';
  echo '<p><a href="dashboard.php">Return to Dashboard</a></p>';
  print_html_footer();
}
else
{
  header('refresh:0; url=dashboard.php');
}

mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
