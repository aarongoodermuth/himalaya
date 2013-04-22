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
      
    echo "<font size=\"3\"><a href=\"browse.php?cid=$cat->id\">$cat->name</a></font><br>";
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

// checks if all post values are set to non-empty values
// (boolean)
function all_set()
{
  return !empty($_POST['itemtype'])   &&
         !empty($_POST['itemcond'])   &&
         !empty($_POST['sellertype']);
}

// checks to see if any post values are set and not empty
// (boolean)
function values_set()
{
  return !empty($_POST['itemtype'])   ||
         !empty($_POST['itemcond'])   ||
         !empty($_POST['sellertype']);
}

// builds query string for search results
// assumes all POST values are set
// browse_cat_id - id of the category currently being browsed
// cats - handle to categories XML object
// returns query string for a prepared statement
function mysql_get_search_query($browse_cat_id, $cats)
{
  global $PRODUCTS_TABLE;
  global $SALE_ITEMS_TABLE;
  global $AUCTIONS_TABLE;
  global $MEMBERS_TABLE;
  global $REG_USER_TABLE;
  global $SUPPLIERS_TABLE;
  global $SALES_TABLE;
  global $ORDERS_TABLE;

  $itemtypes   = $_POST['itemtype'];   // sale item types array (values: sale | auction)
  $itemconds   = $_POST['itemcond'];   // item condition array  (values: 0-5)
  $sellertypes = $_POST['sellertype']; // seller types array    (values: supplier | user)

  // note: this select clause will return bogus values for auction attributes when only
  //       sale items are specified, but these attributes are just ignored anyway
  $select_clause = 'SELECT P.p_name, S.scondition, M.username, S.item_id, P.category_id';
  $from_clause   = " FROM $PRODUCTS_TABLE P, $SALE_ITEMS_TABLE S, $MEMBERS_TABLE M";

  $where_clause  = ' WHERE ';

  // perform necessary joins
  $where_clause .= 'S.username = M.username AND P.product_id = S.product_id ';

  // ensure items are in the current category or a descendant category
  // construct list of descendant category ids
  $query_cats[] = $browse_cat_id;
  foreach($cats as $cat) // meow
  {
    if(!$found && $cat->id != $browse_cat_id) // haven't encountered starting category yet
      continue;
    
    if($cat->id == $browse_cat_id) // encountered starting category
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
    
    // add current category to the descendants list
    $query_cats[] = ((int)$cat->id);
  }
  $where_clause .= 'AND P.category_id IN (' . implode(',', $query_cats) . ') ';

  // ensure items in results have of one of the selected item conditions
  $where_clause .= 'AND S.scondition IN (' . implode(',', $itemconds) . ') ';

  // ensure items have not already been sold
  $where_clause .= "AND S.item_id NOT IN (SELECT item_id FROM $ORDERS_TABLE) ";

  // ensure results match seller types specified
  // (if both specified, no clause needed)
  if(count($sellertypes) === 1 && $sellertypes[0] == 'supplier') // suppliers only
  {
    $where_clause .= "AND (S.username IN (SELECT username FROM $SUPPLIERS_TABLE)) ";
  }
  else if(count($sellertypes) === 1 && $sellertypes[0] == 'user') // registered users only
  {
    $where_clause .= "AND (S.username IN (SELECT username FROM $REG_USER_TABLE)) ";
  }

  // ensure results are only sales or auctions, depending on choice
  if(count($itemtypes) === 2) // get matches from sales and auctions
  {
    // item ids corresponding to auctions are a subset of those corresponding to sale items.
    // to ensure there are no duplicates shown (i.e. one record for the sale item entry, one
    // for the auction entry), left join sale items with auctions on the item_id attribute.
    // requires rewrite of from clause
    // also need another left join to get sale prices
    $select_clause .= ', SALES.price, A.recent_bid, A.end_date';
    $from_clause    = " FROM $PRODUCTS_TABLE  P, $MEMBERS_TABLE M, " .
                      "$SALE_ITEMS_TABLE S LEFT JOIN $AUCTIONS_TABLE A ON S.item_id = A.item_id " .
                      "LEFT JOIN $SALES_TABLE SALES ON S.item_id = SALES.item_id";
  }
  else if(count($itemtypes) === 1 && $itemtypes[0] == 'sale') // get matches only from sales
  {
    $select_clause .= ', SALES.price';
    $from_clause   .= ", $SALES_TABLE SALES";
    $where_clause  .= "AND (S.item_id NOT IN (SELECT item_id FROM $AUCTIONS_TABLE )) " .
                      "AND S.item_id = SALES.item_id ";
  }
  else // get matches only from auctions
  {
    $select_clause .= ', A.recent_bid, A.end_date';
    $from_clause   .= ", $AUCTIONS_TABLE A";
    $where_clause  .= " AND A.item_id = S.item_id AND " .
                      "(S.item_id IN (SELECT item_id FROM $AUCTIONS_TABLE)) ";
  }
  
  return $select_clause . $from_clause . $where_clause;
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
  
  $browse_cat_id = ($cat_set ? ((int)$_GET['cid']) : 0);
  $browse_cat_name = cat_get_name($cats, $browse_cat_id);

  //print_html_header();
  echo '<html>';
  echo '<body>';
  //print_html_nav();
  //echo '<div class="container-fluid">';
  
  if($cat_set) // show title and category path at top of page as appropriate
  {
    echo "<h3>Browse - $browse_cat_name</h3>";
    print_cat_path($cats, $browse_cat_id);
  }
  else
  {
    echo '<h3>Browse</h3>';
  }
  
  // begin category list/options div (aka left bar)
  //echo '<div class="span" style="margin-right:20px">';
  echo '<div style="float:left">';
  
  // begin category list div
  //echo '  <div class="row">';
  echo '<div>';
  //echo '    <div class="tile" style="text-align:left; display:inline-block">' .
  echo '   <div>' .
       '      <h4>Climb higher!</h4>';

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
  
  echo '    </div>'; // tile - category list
  echo '  </div>'; // row - category list
  
  // begin list options div
  //echo '  <div class="row">';
  echo '<div>';
  //echo '    <div class="tile" style="text-align:left; display:inline-block; margin-top:20px">';
  echo '<div>';
  
  show_form('browse_basic');
  
  // change form destination to the current page
  echo "<script>document.browse.action=\"browse.php?cid=$browse_cat_id\"</script>";
  echo '<div id="errordiv"></div>'; // for displaying error messages though javascript
  
  // check for any posted values, populate defaults if none posted
  // populate any fields that previously posted values
  if(!values_set()) // set defaults
  {
    $_POST['itemtype'] = array('sale', 'auction');
    $_POST['itemcond'] = array('0', '1', '2', '3', '4', '5');
    $_POST['sellertype'] = array('supplier', 'user');
  }
  
  // update form elements
  echo '<script>';
  foreach($_POST['itemtype'] as $itemtype)
  {
    echo "document.getElementById(\"$itemtype\").checked = true;";
  }
  foreach($_POST['itemcond'] as $cond_num)
  {
    echo "document.getElementById(\"itemcond$cond_num\").checked = true;";
  }
  foreach($_POST['sellertype'] as $sellertype)
  {
    echo "document.getElementById(\"$sellertype\").checked = true;";
  }
  echo '</script>';
  
  echo '    </div>'; // tile - options
  echo '  </div>'; // row - options 
  echo '</div>'; // span - left bar
  
  // begin div for item list
  //echo '<div class="span" style="display:inline-block">';

  
  // show items
  if($stmt = mysqli_prepare($c, mysql_get_search_query($browse_cat_id, $cats))) // query successfully prepared
  {
    if(mysqli_stmt_execute($stmt)) // successful query
    {
      $item_types = $_POST['itemtype'];
      $only_sales = (count($item_types) === 1 && $item_types[0] == 'sale');
      $only_aucts = (count($item_types) === 1 && $item_types[0] == 'auction');

      // apparently this is needed to make result data immediately available
      mysqli_stmt_store_result($stmt);

      if($only_sales) // only show product name, item cond., seller, and price
      {
        mysqli_stmt_bind_result($stmt, $prod_name, $i_cond, $seller_name, $item_id, $cat_id, $sale_price);
      }
      else if($only_aucts) // show auction information
      {
        mysqli_stmt_bind_result($stmt, $prod_name, $i_cond, $seller_name, $item_id, $cat_id, $recent_bid,
                                       $auc_end_time);
      }
      else // show auction or sale information where appropriate
      {
        mysqli_stmt_bind_result($stmt, $prod_name, $i_cond, $seller_name, $item_id, $cat_id, $sale_price,
                                       $recent_bid, $auc_end_time);
      }

      if(mysqli_stmt_num_rows($stmt) < 1)
      {
        echo '<p>Sorry! There are currently no items available in this category.</p>';
      }
      else // show results in a table
      {
        echo '<table cellpadding="5px">
              <tr align="center">
                <td><b>Product name</b></td>
                <td><b>Category</b></td>
                <td><b>Item condition</b></td>
                <td><b>Seller</b></td>';
        if($only_sales)
        {
          echo '<td><b>Sale price</b></td>';
        }
        else if($only_aucts)
        {
          echo '<td><b>Recent bid</b></td>';
          echo '<td><b>Auction end time</b></td>';
        }
        else
        {
          echo '<td><b>Sale price/recent bid</b></td>
                <td><b>Auction end time</b></td>';
        } 
        echo '</tr>';

        $i = 0;
        while(mysqli_stmt_fetch($stmt))
        {
          /*if($i++ % 2 == 0) // alternate table row color
            echo '<tr style="background-color:#ecf0f5">';
          else
           */ echo '<tr>';
          
          //echo '<tr>';
          echo "<td><a href=\"/items/view.php?id=$item_id\">$prod_name</a></td>";
          echo "<td><a href=\"items/browse.php?cid=$cat_id\">" . cat_get_name($cats, $cat_id) . '</a></td>';
          echo '<td>' . int_to_condition($i_cond) . '</td>';
          echo "<td><a href=\"/users/view.php?username=$seller_name\">$seller_name</a></td>";

          if($only_sales) // show sale price
          {
            printf('<td>$%.2f</td>', $sale_price/100);
          }
          else if($only_aucts) // show auction information
          {
            printf('<td>$%.2f</td>', $recent_bid/100);
            echo "<td>$auc_end_time</td>";
          }
          else // show sale price or auction information where appropriate
          {
            if($auc_end_time == null) // item is a sale
            {
              printf('<td>$%.2f</td>', $sale_price/100);
            }
            else // item is an auction
            {
              printf('<td>$%.2f</td>', $recent_bid/100);
            }

            echo "<td>$auc_end_time</td>";
          }

          echo '</tr>';
        }

        echo '</table>';
      }
    }
    else // unsuccessful query
    {
      print_message('Bad search term. Nice try.');
    }

    // cleanup
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);
  }
  else // query couldn't be built
  {
    print_message('Ow! Query failed.');
  }
  
  echo '</div>'; // container-fluid
  
  //print_html_footer_js();
  //print_html_footer();
  echo '</body>';
  echo '</html>';
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
