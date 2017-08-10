<?php
/**
* this file is used to check the front version and the latest version.
* if the client version is different with the latest version . then reload the page.
*/
require_once(__DIR__."/../../..".'/wp-config.php');
require_once("tool.php");
misysnews_tool_function::record_request_to_db();
misysnews_tool_function::checkIfDevicesDown();
$data  = array('version' =>get_option('misys_news_version') , 'serialnumber'=>get_option('misysnews_runnable_serial_number') );
echo json_encode($data);



?>
