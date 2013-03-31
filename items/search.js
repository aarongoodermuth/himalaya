// sets default values for certain fields in the search form
function populate_defaults()
{
  document.getElementById("sale").checked = true;      // sale items
  document.getElementById("itemcond0").checked = true; // new items
  document.getElementById("supplier").checked = true;  // items sold by suppliers
}

// sets all form values for the nav search option
function populate_all()
{
  // sales & auctions
  document.getElementById("navsale").checked = true;
  document.getElementById("navauction").checked = true;

  // items of all conditions
  for(i = 0; i < 6; i++)
  {
    document.getElementById("navitemcond" + i).checked = true;
  }

  // items sold by suppliers & users
  document.getElementById("navsupplier").checked = true;
  document.getElementById("navuser").checked = true;
}

// sets field values based on non-null arguments
// alerts the user as to which inputs are missing (null)
function populate(searchterm, itemtypes, itemconds, sellertypes)
{
  var i;

  document.write("<p style=\"color:red\">Please enter values for the following fields:");

  if(searchterm === null)
  {
    document.write("<br>Search term");
  }
  else
  {
    document.search.searchterm.value = searchterm;
  }

  if(itemtypes === null)
  {
    document.write("<br>Item type");
  }
  else
  {
    for(i = 0; i < itemtypes.length; i++) // restore item type checkbox values
    {
      document.getElementById(itemtypes[i]).checked = true;
    }
  }

  if(itemconds === null)
  {
    document.write("<br>Item condition");
  }
  else
  {
    for(i = 0; i < itemconds.length; i++) // restore item cond. checkbox values
    {
      document.getElementById("itemcond" + itemconds[i]).checked = true;
    }
  }

  if(sellertypes === null)
  {
    document.write("<br>Seller type");
  }
  else
  {
    for(i = 0; i < sellertypes.length; i++) // restore seller type checkbox values
    {
      document.getElementById(sellertypes[i]).checked = true;
    }
  }

  document.write("</p>");
}
