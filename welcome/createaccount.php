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

// creates custom html header
// (void)
function print_this_html_header()
{
  echo '<html><head>';
  echo '<script type="text/javascript" 
           src="../common/javascript/createuser.js">
           </script>';
  echo '</head>';
  echo '<body onload="disable_all()">';
}

// sees if at least one value is set
// (boolean)
function values_set()
{
  return isset($_POST['username']) || isset($_POST['password']) ||
         isset($_POST['type']);
}

// see is all values are set
// (boolean)
function all_set()
{
  if( isset($_POST['type']) )
  {
    if($_POST['type'] == 0)
    {
      return valid('username') && valid('password') && valid('name') && 
             valid('email') && valid('address') && valid('zip') && 
             valid('phone') && valid('age') && valid('income');
    }
    else
    {
      return valid('username') && valid('password') && valid('company') && 
             valid('contact');
    }
  }
  else
  {
    return false;
  } 
}

// see if some of the values are actually numbers
// (boolean)
function type_check($c)
{
  $retval = true;
  $retval =  $retval && is_numeric($_POST['age']) && is_numeric($_POST['income']) && is_numeric($_POST['zip']);

  if($retval)
  {
    $retval = $retval && mysql_member_zip_exists($c, $_POST['zip']);
  }

  return $retval && (strlen($_POST['phone']) < 16);
}

// checks if value is set and not blank
// (boolean)
function valid($val)
{
  $val = $_POST[$val];

  if( isset($val) )
  {
    if(!empty($val))
    {
      return true;
    }
  }

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
    if(mysql_member_username_unique($c, $_POST['username']))
    {
      $goof = false;
 
      // create the user
      if($_POST['type'] == 0)
      {
        if(!type_check($c))
        {
          $goof = true;
        }
        // registered user
        elseif( !mysql_member_create_ru($c, $_POST['username'], $_POST['password'], 
                          $_POST['name'], $_POST['email'], $_POST['gender'], 
                          $_POST['address'], $_POST['zip'], $_POST['phone'],
                          $_POST['age'], $_POST['income'])
              )
        {
          $goof = true;
        }
        else
        {
          $goof = false;
        }
      }
      else
      {
        // supplier
        if( !mysql_member_create_supplier($c, $_POST['username'], $_POST['password'],
                                 $_POST['company'], $_POST['contact'])
          )
        {
          $goof = true;
        }
      }
      
      if(!$goof)
      {
        
        // redirect to user page
//        header('refresh:4; url=login.php');
        print_this_html_header();
        echo '<p style="color:red">Account Successfully Created. Redirecting</p>';
      }
      else
      {
        print_this_html_header();
        echo '<p style="color:red">Not all entries were valid</p>';
      }
    }
    else
    {
      print_this_html_header();
      echo '<p style="color:red">That username is already taken</p>';
    }
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
echo '<p><a href="login.php">Return to Login Screen</a></p>';
print_html_footer();

mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
