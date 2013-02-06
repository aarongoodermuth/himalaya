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

// (void)
function show_form($form_name)
{
  $data = file_get_contents('/home/goodermuth/dev/websites/himalaya/common/forms/' 
                                 . $form_name . '.form');
  echo $data;
 
}

/*******************/
/** END FUNCTIONS **/
/*******************/

//----------------------------------------------------------------------------------------
?>
