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

// prints a link for each category that is a child of start_id
// prints links depth first in a div along the left-hand side of the page
function print_tree_starting_from_id($start_id)
{
  $cats = new SimpleXMLElement('../common/categories.xml', null, true);
  
  foreach($cats as $cat) // meow
  {
    if(!$found && $cat->id != $start_id) // haven't encountered starting category yet
      continue;
    
    if($cat->id == $start_id) // encountered starting category
    {
      $found = true;
      $start_depth = ((int)$cat->depth);
      continue;
    }
    
    $cur_depth = ((int)$cat->depth);
    if($found && $cur_depth <= $start_depth) // passed all children categories
    {
      break;
    }
    
    $children = true; // if we get to this point, there is a child category
    
    for($i = $start_depth + 1; $i < $cur_depth; $i++)
      echo '&nbsp;&nbsp;&nbsp;&nbsp;';
      
    echo "<font size=\"4\"><a href=\"browse.php?cid=$cat->id\">$cat->name</a></font><br>";
  }
  
  if(!$children) // there were no child categories
    echo "No further sub-categories.<br>";
}

// returns the name of the category for the given category id
// assumes id is a valid category id
function get_cat_name_by_id($id)
{
  $cats = new SimpleXMLElement('../common/categories.xml', null, true);
  $catname = $cats->xpath("/category_tree/category[id=$id]/name");
  
  return $catname[0];
}

// prints HTML text in red
// $s - string to print
function print_message($s)
{
  echo '<p style="color:red">' . $s . '</p>';
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
  print_html_header();
  echo '<body>';
  print_html_nav();
  echo '<div class="container-fluid">';

  echo '<h3>Browse</h3>';

  if(empty($_GET['cid'])) // basic browse page - show complete category list
  {
    echo '<h4>Climb higher!</h4>Choose a category to browse.<br><br>';
    print_tree_starting_from_id(0);
  }
  else // show items, sorting options, and subcategories
  {
    $browse_cat = $_GET['cid'];
    
    echo '<h4>Climb higher!</h4>';
    echo 'Categories under ' . get_cat_name_by_id($browse_cat) . ':<br><br>';
    print_tree_starting_from_id($browse_cat);
  }
}
else // user not logged in
{
  header('refresh:0; url=../welcome/login.php');
}

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
