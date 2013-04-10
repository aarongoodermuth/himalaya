<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';
include_once '/home/goodermuth/dev/websites/himalaya/common/functions.php';

/******************/
/** END INCLUDES **/
/******************/

//------------------------------------------------------------------------------

/***************/
/** FUNCTIONS **/
/***************/



/*******************/
/** END FUNCTIONS **/
/*******************/

//-----------------------------------------------------------------------------

/**************/
/** PHP CODE **/
/**************/

print_html_header();
echo '<body>';
print_html_nav();
echo '<div class="container-fluid">';


echo file_get_contents('about_content.html', true);


echo '</div>';
print_html_footer_js();
print_html_footer2();
print_html_footer();

/******************/
/** END PHP CODE **/
/******************/

//------------------------------------------------------------------------------
?>
