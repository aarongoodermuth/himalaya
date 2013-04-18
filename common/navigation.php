<?php

echo "<div class=\"row demo-row\">
<div class=\"span9\">
  <div class=\"navbar navbar-inverse\">
    <div class=\"navbar-inner\">
      <div class=\"container\">
	<button type=\"button\" class=\"btn btn-navbar\" data-toggle=\"collapse\" data-target=\".nav-collapse\">
	  <span class=\"icon-bar\"></span>
	  <span class=\"icon-bar\"></span>
	  <span class=\"icon-bar\"></span>
	</button>
	<div class=\"nav-collapse collapse\">
	  <ul class=\"nav\">
	    <li>
	      <a href=\"/users/dashboard.php\">
		Dashboard
		<span class=\"navbar-unread\">1</span>
	      </a>
	    </li>
	    <li class=\"nav\">
	      <a href=\"#\">
		Navigation
		<span class=\"navbar-unread\">1</span>
	      </a>
	      <ul>
		<li>
		  <a href=\"/items/search.php\">Search</a>
		  <ul>
		    <li> 
		    <div class=\"row\"><div class=\"span4\"><div class=\"todo-search\">
		      <form style=\"margin:0; padding:0;\" name=\"navsearch\" action='/items/search.php' method='post'>
                      <input id=\"navsearchterm\" class=\"todo-search-field\" type=\"search\" value=\"\" placeholder=\"Search\" name=\"searchterm\"/>
                      <input id=\"navsale\" type=\"hidden\" name=\"itemtype[]\" value=\"sale\"/>
                      <input id=\"navauction\" type=\"hidden\" name=\"itemtype[]\" value=\"auction\"/>
                      <input id=\"navitemcond0\" type=\"hidden\" name=\"itemcond[]\" value=\"0\"/>
                      <input id=\"navitemcond1\" type=\"hidden\" name=\"itemcond[]\" value=\"1\"/>
                      <input id=\"navitemcond2\" type=\"hidden\" name=\"itemcond[]\" value=\"2\"/>
                      <input id=\"navitemcond3\" type=\"hidden\" name=\"itemcond[]\" value=\"3\"/>
                      <input id=\"navitemcond4\" type=\"hidden\" name=\"itemcond[]\" value=\"4\"/>
                      <input id=\"navitemcond5\" type=\"hidden\" name=\"itemcond[]\" value=\"5\"/>
                      <input id=\"navseller\" type=\"hidden\" name=\"sellertype[]\" value=\"supplier\"/>
                      <input id=\"navuser\" type=\"hidden\" name=\"sellertype[]\" value=\"user\"/>
		      <input type=\"submit\" style=\"position: absolute; left: -9999px; width: 1px; height: 1px;\"/>
		      </form>
          <script>search_populate_all();</script>
        </div></div></div>
		    </li>
		  </ul>
		</li>
    <li>
      <a href=\"/items/browse.php\">Browse</a>
      <ul>
        <script></script>
      </ul>
    </li>
		<li>
		  <a href=\"#\">Account</a>
		  <ul>
		    <li><a href=\"/users/editaccount.php\">Edit Account Info</a></li>
		    <li><a href=\"/members/changepassword.php\">Change Password</a></li>
		    <li><a href=\"/users/redeem.php\">Redeem Gift Cards</a></li>
		  </ul> <!-- /Sub menu -->
		</li>
		<li>
		  <a href=\"#\">Orders</a>
		  <ul>
		    <li><a href=\"/users/purchased.php\">Purchased Items</a></li>
		    <li><a href=\"/users/sold.php\">Sold Items</a></li> 
		  </ul>
		</li>
		<li><a href=\"/users/sellitem.php\">Sell An Item</a></li>
		<li><a href=\"/welcome/logout.php\">Log Out</a></li>
	      </ul> <!-- /Sub menu -->
	    </li>
	    <li>
	      <a href=\"/about.php\">
		About
		<span class=\"navbar-unread\">1</span>
	      </a>
	    </li>
	  </ul>
	</div><!--/.nav-collapse -->
      </div>
    </div>
  </div>
</div>
</div> <!-- /row -->
"
?>