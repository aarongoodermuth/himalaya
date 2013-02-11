<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql_admin.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/functions.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/forms.php';

/******************/
/** END INCLUDES **/
/******************/

//------------------------------------------------------------------------------

/***************/
/** FUNCTIONS **/
/***************/

// takes in the resutls of the DB query and formats a string that 
//    shows the response of the DB
// (string)
function result_handler($db_results)
{
  //...
  return '*sticks tounge out*';
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

if( mysql_admin_get_type($c, $username) == $ADMIN_USER_TYPE_MAPPING[1])
{
  print_html_header();
  echo '<h3>Modify Database</h3><p>This page provides you with a web interface
           to the MySQL database. Please note that all statements will be
           executed into the database. Use at your own risk.</p><p>Please 
           execute no more than one query at a time. Semicolons not 
           required.</p>';

  //check if we are already adding a user
  if(isset($_POST['query']))
  {
    $results = mysql_admin_db_query($c, $_POST['query']);
    echo '<p>Query sent to database:</p><p style="color:red">' 
            . $_POST['query'] . '</p>';
    echo '<p>Database response:</p><p style="color:red">' 
            . result_handler($results) . '</p>';
  }
 
  show_form('dbquery');
}
else
{
  header('Refresh:0; url=login.php');
}

echo '<p><a href="dashboard.php">Return to the Dashboard</a></p>';
print_html_footer();
mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
