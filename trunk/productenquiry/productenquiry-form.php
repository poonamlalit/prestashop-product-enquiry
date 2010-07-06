<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/productenquiry.php');

$productenquiry = new productEnquiry();
echo $productenquiry->displayFrontForm();

include(dirname(__FILE__).'/../../footer.php');

?>