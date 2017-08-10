<?php
// this script is used for handling the http request from the front end from angular part
require_once('feeds_tool.php');

if(isGet()){
  $requestType = parserRequestType();

  executeRequests($requestType);
}else {
  // post method
  $jsonData = file_get_contents('php://input');
  if(!get_option('misysnews_feeds_new')){
    add_option('misysnews_feeds_new');
    update_option('misysnews_feeds_new',$jsonData);
    misysnews_feeds_tool_function::increaseSerialNumber();
  }else {
    update_option('misysnews_feeds_new',$jsonData);
    misysnews_feeds_tool_function::increaseSerialNumber();
  }
}



//parser the parameter to check what is the request is
function parserRequestType(){
  return $_GET["requesttype"];
}

function isGet(){
  //check the method of request
  $method = $_SERVER['REQUEST_METHOD'];
  if($method == 'GET'){
    return true;
  }else {
    return false;
  }
}

//response to the request
function executeRequests($requestType){

  switch ($requestType) {
    case RequestType::$whole:
      $latestFeeds = misysnews_feeds_tool_function::getWholeFeeds();
      echo ($latestFeeds);
      break;

    case RequestType::$customized:
      $publishedCustomized  = misysnews_feeds_tool_function::getCustomizedPostsDataFromPostType("misysnews_feeds");
      echo json_encode($publishedCustomized);

      break;
    default:
      echo "";
      break;
  }
}
//this static field should keep the same with the class requesttype infrontend ts file original.entity.ts
class RequestType{
  public static $customized = "0";
  public static $whole = "1";

}


?>
