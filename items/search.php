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
  if(!empty($_POST['zipcheck']) && empty($_POST['zipcode'])) // zip code search specified, but no
    return false;                                            //   zip code entered

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
  global $ZIP_TABLE;
  global $EARTH_RADIUS;

  $itemtypes   = $_POST['itemtype'];   // sale item types array (values: sale | auction)
  $itemconds   = $_POST['itemcond'];   // item condition array  (values: 0-5)
  $sellertypes = $_POST['sellertype']; // seller types array    (values: supplier | user)

  $i = 0;

  // note: this select clause will return bogus values for auction attributes when only
  //       sale items are specified, but these attributes are just ignored anyway
  $select_clause = 'SELECT P.p_name, S.scondition, M.username, S.item_id, P.category_id';
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
  
  // if zip code search specified, ensure items are within the given radius of the given zip code
  if(!empty($_POST['zipcode']))
  {
    $where_clause .= "AND S.item_id IN (" . 
                       "SELECT s.item_id " .
                       "FROM $SALE_ITEMS_TABLE s INNER JOIN $ZIP_TABLE z ON s.shipping_zip = z.zip " .
                       "WHERE ($EARTH_RADIUS * acos(cos(radians(( " .
                          "SELECT lat FROM $ZIP_TABLE WHERE zip = ? " .
                          "))) * cos(radians(z.lat)) * cos(radians(( " .
                          "SELECT lon FROM $ZIP_TABLE WHERE zip = ? " .
                          ")) - radians(z.lon)) + sin(radians(( " .
                          "SELECT lat FROM $ZIP_TABLE WHERE zip = ? " .
                          "))) * sin(radians(z.lat)))) <= ?) ";
  }

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

  return $select_clause . $from_clause . $where_clause . 'ORDER BY P.p_name';
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
  $cats = new SimpleXMLElement('../common/categories.xml', null, true); // get categories XML file

  print_html_header();
  echo '<body>';
  print_html_nav();
  echo '<script src="../common/javascript/sorttable.js"></script>'; // JS for sortable tables
  echo '<div class="container-fluid">';

  if(all_set($c)) // execute search
  {
    echo '<h3>Search Results</h3>';

    if($stmt = mysqli_prepare($c, mysql_get_search_query())) // query successfully prepared
    {
      $searchterm = '%' . $_POST['searchterm'] . '%';
      
      if(!empty($_POST['zipcode'])) // bind zip inputs for zip code search
      {
        $zipcode = $_POST['zipcode'];
        $radius = ((int)$_POST['radius']);
        mysqli_stmt_bind_param($stmt, 'sssssi', $searchterm, $searchterm, $zipcode, $zipcode,
                                                $zipcode, $radius);
      }
      else // no zip code search
      {
        mysqli_stmt_bind_param($stmt, 'ss', $searchterm, $searchterm);
      }

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
          echo '<p>No items match your search.</p>';
        }
        else // show results in a table
        {
          echo '<table class="sortable" cellpadding="5" style="margin-bottom:20px">
                <tr align="center">
                  <th><b>Product name</b></th>
                  <th><b>Category</b></th>
                  <th><b>Item condition</b></th>
                  <th><b>Seller</b></th>';
          if($only_sales)
          {
            echo '<th><b>Sale price</b></th>';
          }
          else if($only_aucts)
          {
            echo '<th><b>Recent bid</b></th>';
            echo '<th><b>Auction end time</b></th>';
          }
          else
          {
            echo '<th><b>Sale price/recent bid</b></th>
                  <th><b>Auction end time</b></th>';
          } 
          echo '</tr>';

          $now = new DateTime('now'); // current date in MySQL format for getting time differences for auctions
          while(mysqli_stmt_fetch($stmt))
          {            
            echo '<tr>';
            
            echo "<td><a href=\"/items/view.php?id=$item_id\">$prod_name</a></td>";
            $cat_name = $cats->xpath("/category_tree/category[id=$cat_id]/name");
            echo "<td><a href=\"/items/browse.php?cid=$cat_id\">$cat_name[0]</a></td>";
            echo '<td>' . int_to_condition($i_cond) . '</td>';
            echo "<td><a href=\"/members/view.php?username=$seller_name\">$seller_name</a></td>";

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
                echo '<td align="center"></td>'; // no auction end time
              }
              else // item is an auction
              {
                printf('<td>$%.2f</td>', $recent_bid/100);
              
                // get amount of time until the auction ends
                $time = datetime::createfromformat("Y-m-d H:i:s", $auc_end_time);
                $diff = $now->diff($time);
                if($diff->d == 0) // auction ends today - show more precise time info
                {
                  echo '<td>';
                  if($diff->h == 1)
                    echo '1 hour, ';
                  else
                    echo "$diff->h hours, ";
                
                  if($diff->i == 1)
                    echo '1 minute, ';
                  else
                    echo "$diff->i minutes, ";
                  
                  if($diff->s == 1)
                    echo '1 second';
                  else
                    echo "$diff->s seconds";
                  echo '</td>';                  
                }
                else // auction ends at least one day from now - show less precise time info
                {
                  echo '<td>';
                  if($diff->d == 1)
                    echo '1 day, ';
                  else
                    echo "$diff->d days, ";
                  
                  if($diff->h == 1)
                    echo '1 hour';
                  else
                    echo "$diff->h hours";
                  echo '</td>';              
                }
              }
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
  print_html_footer2();
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
