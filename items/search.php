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

// checks if all post values are set to non-empty values
// (boolean)
function all_set()
{
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

// builds and executes query to get search results
// assumes all POST values are set
// $c - mysql database link
// returns query results
// seatbelts, everyone
function mysql_get_search_results($c)
{
  global $PRODUCTS_TABLE;
  global $SALE_ITEMS_TABLE;
  global $AUCTIONS_TABLE;
  global $MEMBERS_TABLE;
  global $REG_USER_TABLE;
  global $SUPPLIERS_TABLE;

  $searchterm  = sanitize($_POST['searchterm']); // search term via text field
  $itemtypes   = $_POST['itemtype'];             // sale item types array (values: sale | auction)
  $itemconds   = $_POST['itemcond'];             // item condition array  (values: 0-5)
  $sellertypes = $_POST['sellertype'];           // seller types array    (values: supplier | user)

  $i = 0;

  // note: this select clause will return bogus values for auction attributes when only
  //       sale items are specified, but these attributes are just ignored anyway
  $select_clause = 'SELECT P.p_name, S.scondition, M.username';
  $from_clause   = ' FROM ' . $PRODUCTS_TABLE . ' P, ' . $SALE_ITEMS_TABLE . ' S, ' .
                              $MEMBERS_TABLE . ' M';

  $where_clause  = ' WHERE ';

  // perform necessary joins
  $where_clause .= 'S.username = M.username AND P.product_id = S.product_id ';

  // find search term matches from product name or description
  $where_clause .= 'AND (P.p_name LIKE \'%' . $searchterm . '%\' OR
                         P.p_desc LIKE \'%' . $searchterm . '%\') ';

  // ensure items in results have of one of the selected item conditions
  $where_clause .= 'AND (';
  for($i = 0; $i < count($itemconds) - 1; $i++) // stop after second to last element
  {
    $where_clause .= 'S.scondition = ' . $itemconds[$i] . ' OR ';
  }
  $where_clause .= 'S.scondition = ' . $itemconds[$i] . ') ';

  // ensure results match seller types specified
  // (if both specified, no clause needed)
  if(count($sellertypes) === 1 && $sellertypes[0] == 'supplier') // suppliers only
  {
    $where_clause .= 'AND (S.username IN (SELECT username FROM ' . $SUPPLIERS_TABLE . ')) ';
  }
  else if(count($sellertypes) === 1 && $sellertypes[0] == 'user') // registered users only
  {
    $where_clause .= 'AND (S.username IN (SELECT username FROM ' . $REG_USER_TABLE . ')) ';
  }

  // ensure results are only sales or auctions, depending on choice
  if(count($itemtypes) === 2) // get matches from sales and auctions
  {
    // item ids corresponding to auctions are a subset of those corresponding to sale items.
    // to ensure there are no duplicates shown (i.e. one record for the sale item entry, one
    // for the auction entry), left join sale items with auctions on the item_id attribute.
    // requires rewrite from clause
    $select_clause .= ', A.recent_bid, A.end_date';
    $from_clause    = ' FROM ' . $PRODUCTS_TABLE . ' P, ' . $MEMBERS_TABLE . ' M, ' .
                                 $SALE_ITEMS_TABLE . ' S LEFT JOIN ' . 
                                 $AUCTIONS_TABLE . ' A ON S.item_id = A.item_id ';
  }
  else if(count($itemtypes) === 1 && $itemtypes[0] == 'sale') // get matches only from sales
  {
    $where_clause .= 'AND (S.item_id NOT IN (SELECT item_id FROM ' . $AUCTIONS_TABLE . ')) ';
  }
  else // get matches only from auctions
  {
    $select_clause .= ', A.recent_bid, A.end_date';
    $from_clause   .= ', ' . $AUCTIONS_TABLE . ' A';
    $where_clause  .= ' AND A.item_id = S.item_id AND (S.item_id IN (SELECT item_id FROM ' . 
                      $AUCTIONS_TABLE . ')) ';
  }

  return mysqli_query($c, $select_clause . $from_clause . $where_clause);
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

  if(all_set()) // execute search
  {
    if($results = mysql_get_search_results($c)) // successful query
    {
      if(mysqli_num_rows($results) < 1)
      {
        echo '<p>No items match your search.</p>';
      }
      else // show results in a table
      {
        $item_types = $_POST['itemtype'];
        $only_sales = (count($item_types) === 1 && $item_types[0] == 'sale');

        // table header row
        echo '<table cellpadding="5">
              <tr align="center">
                <td><b>Product name</b></td>
                <td><b>Item condition</b></td>
                <td><b>Seller</b></td>';
        if(!$only_sales)
        {
          echo '<td><b>Recent bid</b></td>
                <td><b>Auction end time</b></td>';
        } 
        echo '</tr>';

        $n = $only_sales ? 3 : 5; // if only sales specified, ignore auction attributes
        while($row = mysqli_fetch_row($results))
        {
          echo '<tr align="center">';
          for($i = 0; $i < $n; $i++) // get each attribute for the current record
          {
            $s = null;
            
            if($i == 1) // display correct string for item condition
            {
              switch(intval($row[$i]))
              {
              case 0: $s = 'New';
              break;
              case 1: $s = 'Used - Like New';
              break;
              case 2: $s = 'Used - Very Good';
              break;
              case 3: $s = 'Used - Good';
              break;
              case 4: $s = 'Used - Acceptable';
              break;
              case 5: $s = 'Used - For Parts Only';
              break;
              }

              echo '<td>' . $s . '</td>';
            }
            else
            {
              echo '<td>' . $row[$i] . '</td>';
            }
          }
          echo '</tr>';
        }

        echo '</table>';
      }
    }
    else // query failed
    {
      print_message('Query failed');
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
                               post_or_null($_POST['sellertype']) . ');</script>';
  }
  else // no post values - populate form with default values
  {
    show_form('search');
    echo '<script src="search.js"></script>';
    echo '<script>populate_defaults();</script>';
    echo '<body OnLoad="document.search.searchterm.focus();">'; // give textbox focus
    //echo '</body>';
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
