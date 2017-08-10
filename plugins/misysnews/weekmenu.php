<?php

header('Content-Type: application/json; charset=utf-8');
$url = 'http://wp.servicesbysfl.com/se-restaurer/restaurant/menus-de-la-semaine';

$htmlString = file_get_contents($url);
if($htmlString == false) {
	header("HTTP/1.0 404 Not Found");
	return;
} 
$doc = new DOMDocument();

libxml_use_internal_errors(true);
$doc->loadHTML($htmlString);
libxml_clear_errors();

$xpath = new DOMXpath($doc);
$elements = $xpath->query("//div[contains(@class,'field-name-field-collection-plats')]");

$menus = array();
if ($elements) {
  foreach ($elements as $element) {
  	$menuArray = getMenuArrayFromNode( $element );
  	if($menuArray) {
			array_push( $menus, $menuArray );
		}
  }
}

echo json_encode($menus);

function getMenuArrayFromNode( $node ) {
  if( $node->hasChildNodes() ){
  	$lis = $node->getElementsByTagName('li');
    $menu = array();
    foreach($lis as $li){
      array_push($menu, $li->nodeValue); 
    }
    return $menu;
  }
  return false;
}
?>