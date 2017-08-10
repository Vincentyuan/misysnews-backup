<?php
header('Content-Type: application/json; charset=utf-8');
//$url = 'http://www.cheriefm.fr/le-mag-cherie-fm-107/horoscope-du-jour-52/horoscope/';
//$url = 'http://www.cheriefm.fr/horoscope';
$url = 'http://mfmradio.fr/horoscope';

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
//$elements = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' block half special ')]");
//$elements = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' caption ')]");
$elements = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' item-content ')]");

$horos = array();
if (!is_null($elements)) {
  foreach ($elements as $element) {
  	$horo = getHoroFromNode($element);
  	if($horo){
  		array_push($horos, $horo);
  	}
  }
}
// TODO: need more accurate method to recognize the horoscope data from the website 
echo json_encode( array_slice($horos,0,12));

function getHoroFromNode( $node ) {
	$horo = array();

	$signNode = $node->childNodes->item(1);
	$textNode = $node->childNodes->item(3);

	if(!$signNode || !$textNode){
		return false;
	}

	$horo['sign'] = trim($signNode->textContent);
	$horo['text'] = trim($textNode->textContent);

	return $horo;
}

?>
