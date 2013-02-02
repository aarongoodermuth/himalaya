<?php

/**************/
/** INCLUDES **/
/**************/

include_once '/home/goodermuth/dev/websites/himalaya/common/constants.php';

/******************/
/** END INCLUDES **/
/******************/

//----------------------------------------------------------------------------------------

/***************/
/** FUNCTIONS **/
/***************/

//print HTML header
// (void)
function print_html_header()
{
  echo "<html><head></head><body>";
}

//print HTML footer
// (void)
function print_html_footer()
{
  echo "</body></html>";
}

// show the password form
// (void)
function show_password_form()
{
  $data = file_get_contents('/home/goodermuth/dev/websites/himalaya/common/login.form');
  echo $data;
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//----------------------------------------------------------------------------------------
?>

