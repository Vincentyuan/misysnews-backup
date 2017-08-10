<?php
require_once("databaseoop.php");
require_once("tool.php");
$url = $_GET['url'];
//$url = filter_input(INPUT_GET, 'url');

if(!$url) {
	header("HTTP/1.0 400 Bad Request");
	return;
}
$ch = curl_init();
$url = str_replace(' ', '%20', $url);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERPWD, 'MISYSROOT\\ndemange:Password5');
$data = curl_exec($ch);

http_response_code( curl_getinfo($ch, CURLINFO_HTTP_CODE) );
header( 'Content-type: ' . curl_getinfo($ch, CURLINFO_CONTENT_TYPE));

if (curl_errno($ch)) {
	http_response_code(curl_getinfo($ch, CURLINFO_HTTP_CODE));
    echo "Error: " . curl_error($ch);
} else {
    echo $data;
}
curl_close($ch);


?>
