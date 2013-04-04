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
  print_html_header();
  echo '<body>';
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//-----------------------------------------------------------------------------

/**************/
/** PHP CODE **/
/**************/

print_this_html_header();
echo '
<body>
<br>
<div class="container-fluid">
  <div class="row demo-tiles">
    <div class="span4">
      <div class="tile">
        <img class="tile-image big-illustration" alt="" src="/design/images/illustrations/infinity.png" />
	<h3 class="tile-title">Register As A Supplier</h3>
        <p>Sell items to users...</p>
        <a class="btn btn-primary btn-large btn-block" href="/welcome/createsupplier.php">Register</a>
      </div>
    </div>   
    <div class="span4">
      <div class="tile">
        <img class="tile-image big-illustration" alt="" src="/design/images/illustrations/gift.png" />
	<h3 class="tile-title">Register As An Individual</h3>
        <p>Buy and sell items...</p>
        <a class="btn btn-primary btn-large btn-block" href="/welcome/createuser.php">Register</a>
      </div>
    </div>
  </div>
<p><a href="login.php">Return to Login Screen</a></p>
</div>
';

print_html_footer2();
print_html_footer_js();
print_html_footer();

mysql_disconnect($c);

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
