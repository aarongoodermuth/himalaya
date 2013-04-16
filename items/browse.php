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
function print_tree_starting_from_id($cats, $start_id)
{  
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

// prints category path as a series of links to the current category's ancestors
function print_cat_path($cats, $cur_id)
{
  // create array of ancestors, working up the category tree
  $nodes[0] = cat_get_name($cats, $cur_id);
  $id = $cur_id;
  
  while(($id = cat_get_parent_id($cats, $id)) != 0) // while we still haven't reached the root
  {
    // add a link to the current ancestor to the category array
    $name = cat_get_name($cats, $id);
    $nodes[] = "<a href=\"browse.php?cid=$id\">$name</a>";
  }
  
  // add a link to the root category
  $name = cat_get_name($cats, 0);
  $nodes[] = "<a href=\"browse.php?cid=0\">$name</a>";
  
  // reverse array and implode to create a string in the form of
  //   root -> cat1 -> cat2 -> ... -> cur_cat
  $nodes = array_reverse($nodes);
  $path = implode(' -> ', $nodes);
  
  echo $path . '<br><br>';
}

// returns the name of the category for the given category id
// assumes id is a valid category id
function cat_get_name($cats, $id)
{
  $catname = $cats->xpath("/category_tree/category[id=$id]/name");
  
  return $catname[0];
}

// returns the name of the parent category of the given category id
// assumes id is a valid category id
function cat_get_parent_name($cats, $id)
{
  $pid = cat_get_parent_id($cats, $id);
  $pname = $cats->xpath("/category_tree/category[id=$pid]/name");
  
  return $pname[0];
}

// returns the category id of the parent of the given category id
// assumes id is a valid category id
function cat_get_parent_id($cats, $id)
{
  $pid = $cats->xpath("/category_tree/category[id=$id]/pid");
  
  return $pid[0];
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
  $cats = new SimpleXMLElement('../common/categories.xml', null, true);
  $cat_set = !empty($_GET['cid']);
  
  if($cat_set) // get current category id and name
  {
    $browse_cat_id = ((int)$_GET['cid']);
    $browse_cat_name = cat_get_name($cats, $browse_cat_id);
  }

  print_html_header();
  echo '<body>';
  print_html_nav();
  echo '<div class="container-fluid">';
  
  if($cat_set) // show category path at top of page when appropriate
  {
    echo "<h3>Browse - $browse_cat_name</h3>";
    print_cat_path($cats, $browse_cat_id);
  }
  else
  {
    echo '<h3>Browse</h3>';
  }
  
  // create category list div
  echo '<div class="span3">' .
       '  <div class="tile" style="text-align:left">' .
       '    <h4>Climb higher!</h4>';

  if(!$cat_set) // basic browse page - show complete category list
  {
    echo 'Choose a category to browse.<br><br>';
    print_tree_starting_from_id($cats, 0);
  }
  else // show items, sorting options, and subcategories
  {
    echo 'Under ' . cat_get_name($cats, $browse_cat_id) . ':<br><br>';
    print_tree_starting_from_id($cats, $browse_cat_id);    
  }
  
  // end category list div
  echo '</div>'; // tile
  echo '</div>'; // span3
  echo 'some main body text hopefully';
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
