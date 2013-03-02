<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/mysql.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/functions.php';

/******************/
/** END INCLUDES **/
/******************/

//------------------------------------------------------------------------------

/***************/
/** FUNCTIONS **/
/***************/

// creates custom html header
// (void)
function print_this_html_header()
{
  echo '<html><head>';
  echo '<script type="text/javascript" 
           src="/himalaya/common/javascript/createuser.js">
           </script>';
  echo '</head>';
  echo '<body onload="disable_all()">';
}

// sees if at least one value is set
// (boolean)
function values_set()
{
  return isset($_POST['username']) || isset($_POST['password']) || isset($_POST['type']);
}

// see is all values are set
// (boolean)
function all_set()
{
  return false;
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//-----------------------------------------------------------------------------

/**************/
/** PHP CODE **/
/**************/

$c = mysql_make_connection();

if( values_set() )
{
  if(all_set())
  {
    // create the user
    //...
    
    // redirect to user page
    //...
  }
  else
  {
    print_this_html_header();
    // error message not allowing to be blank
    echo '<p style="color:red">All values must be filled out</p>';
  }
}
else
{
  print_this_html_header();
}

show_form('createuser');
print_html_footer();

mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
