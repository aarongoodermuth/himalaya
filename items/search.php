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

// checks if all post values are set to non-empty, valid values
// (boolean)
function all_set($c)
{
  if(!empty($_POST['zipcode'])) // ensure ZIP code is valid
  {
    global $ZIP_TABLE;
  
    if($stmt = mysqli_prepare($c, "SELECT zip from $ZIP_TABLE WHERE zip=?")) // query successfully prepared
    {
      mysqli_stmt_bind_param($stmt, 's', $_POST['zipcode']);
    
      if(mysqli_stmt_execute($stmt)) // successful query
      {
        mysqli_stmt_bind_result($stmt, $zip);
        if(mysqli_stmt_fetch($stmt) == null) // no results - ZIP code is not in the database
        {
          print_message('Invalid ZIP code.');
          return false;
        }
      }
      else // unsuccessful query
      {
        print_message('ZIP code verification failed. Sorry about that!');
      }
    
      mysqli_stmt_close($stmt);
    }
    else // query couldn't be built
    {
      print_message('Invalid ZIP code format.');
    }
  }

  // zip code is valid - check rest of post values
  return !empty($_POST['searchterm']) &&
         !empty($_POST['itemtype'])   &&
         !empty($_POST['itemcond'])   &&
         !empty($_POST['sellertype']);
}

// checks to see if any post values are set and not empty
// (boolean)
function values_set()
{
  return !empty($_POST['searchterm']) ||
         !empty($_POST['itemtype'])   ||
         !empty($_POST['itemcond'])   ||
         !empty($_POST['sellertype']);
}

// returns the POST value in javascript-friendly format if set,
//   or 'null' otherwise
// $s - post value to check
// NOTE: returns the string 'null' since we're using this to
//       pass parameters to javascript
function post_or_null($s)
{
  if(!empty($s))
  {
    if(is_string($s))
      return '"' . $s . '"';  // wrap POST value in double quotes
    else if(is_array($s))
      return json_encode($s); // encode array so javascript can understand it
    else
      return $s;              // $s is a numeric value: no casting needed
  }

  return 'null';
}

// prints HTML text in red
// $s - string to print
function print_message($s)
{
  echo '<p style="color:red">' . $s . '</p>';
}

// builds query string for search results
// assumes all POST values are set
// returns query string for a prepared statement
function mysql_get_search_query()
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

  $i = 0;

  // note: this select clause will return bogus values for auction attributes when only
  //       sale items are specified, but these attributes are just ignored anyway
  $select_clause = 'SELECT P.p_name, S.scondition, M.username, S.item_id';
  $from_clause   = " FROM $PRODUCTS_TABLE P, $SALE_ITEMS_TABLE S, $MEMBERS_TABLE M";

  $where_clause  = ' WHERE ';

  // perform necessary joins
  $where_clause .= 'S.username = M.username AND P.product_id = S.product_id ';

  // find search term matches from product name or description
  $where_clause .= "AND (P.p_name LIKE ? OR P.p_desc LIKE ?) ";

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
  print_html_header();
  echo '<body>';
  print_html_nav();
  echo '<div class="container-fluid">';

  if(all_set($c)) // execute search
  {
    echo '<h3>Search Results</h3>';

    if($stmt = mysqli_prepare($c, mysql_get_search_query())) // query successfully prepared
    {
      $searchterm = '%' . $_POST['searchterm'] . '%';
      mysqli_stmt_bind_param($stmt, 'ss', $searchterm, $searchterm);

      if(mysqli_stmt_execute($stmt)) // successful query
      {
        $item_types = $_POST['itemtype'];
        $only_sales = (count($item_types) === 1 && $item_types[0] == 'sale');
        $only_aucts = (count($item_types) === 1 && $item_types[0] == 'auction');

        // apparently this is needed to make result data immediately available
        mysqli_stmt_store_result($stmt);

        if($only_sales) // only show product name, item cond., seller, and price
        {
          mysqli_stmt_bind_result($stmt, $prod_name, $i_cond, $seller_name, $item_id, $sale_price);
        }
        else if($only_aucts) // show auction information
        {
          mysqli_stmt_bind_result($stmt, $prod_name, $i_cond, $seller_name, $item_id, $recent_bid,
                                         $auc_end_time);
        }
        else // show auction or sale information where appropriate
        {
          mysqli_stmt_bind_result($stmt, $prod_name, $i_cond, $seller_name, $item_id, $sale_price,
                                         $recent_bid, $auc_end_time);
        }

        if(mysqli_stmt_num_rows($stmt) < 1)
        {
          echo '<p>No items match your search.</p>';
        }
        else // show results in a table
        {
          echo '<table cellpadding="5">
                <tr align="center">
                  <td><b>Product name</b></td>
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

          while(mysqli_stmt_fetch($stmt))
          {
            echo '<tr align="center">';
            echo "<td><a href=\"/items/view.php?id=$item_id\">$prod_name</a></td>";
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
  }
  else if(values_set()) // user missed some form input
  {
    show_form('search');

    // populate form with existing post values and alert user to missing inputs
    echo '<script src="search.js"></script>';
    echo '<script>populate(' . post_or_null($_POST['searchterm']) . ',' .
                               post_or_null($_POST['itemtype'])   . ',' .
                               post_or_null($_POST['itemcond'])   . ',' .
                               post_or_null($_POST['sellertype']) . ',' .
                               post_or_null($_POST['zipcheck'])   . ',' .
                               post_or_null($_POST['zipcode'])    . ');</script>';
  }
  else // no post values - populate form with default values
  {
    show_form('search');
    echo '<script src="search.js"></script>';
    echo '<script>populate_defaults();</script>';
    echo '<body OnLoad="document.search.searchterm.focus();">'; // give textbox focus
  }

  print_html_footer_js();
  print_html_footer();
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
