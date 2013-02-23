<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql_admin.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/functions.php';

/******************/
/** END INCLUDES **/
/******************/

//------------------------------------------------------------------------------

/***************/
/** FUNCTIONS **/
/***************/

//...

/*******************/
/** END FUNCTIONS **/
/*******************/

//-----------------------------------------------------------------------------

/**************/
/** PHP CODE **/
/**************/

$c = mysql_make_connection();
$username = check_logged_in_admin_user($c);

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
    if($_POST['query'] == '')
    {
      echo '<p style="color:red">Cannot Send empty Query</p>';
    }
    else
    {
      echo '<p>Query sent to database:</p><p style="color:red">' 
              . $_POST['query'] . '</p>';
      echo '<p>Database response:</p><p style="color:red">' 
              . mysql_admin_db_query($c, $_POST['query']) . '</p>';
    }
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
