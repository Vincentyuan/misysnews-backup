<?php
header('Content-Type: application/json');
require('../../../wp-load.php');


if( isset( $_GET['mode'] ) ) {
	$mode = $_GET['mode'];
	$feeds = file_get_contents('./feeds-' . $mode . '.json');
	if( $feeds ) {
		echo $feeds;
	}else{
		http_response_code(404);
	}
}else{
		if(get_option("misysnews_feeds_new")||get_option('misysnews_feeds_new') != ""){
			echo get_option('misysnews_feeds_new');
		}else {
			echo get_option('misysnews_feeds')['json_data'];
		}
}
/* Removed on 26/08/2016 */
/*
				{
          "id": "inside-misys 1",
          "feedURL": "http://home.insidemisys.com/_layouts/feed.aspx?xsl=1%26web=%2FNews%2Fnewsfeed%26page=a95fd203-81e5-4c1c-8c4f-4e16b3a9e636%26wp=040e3fde-267f-40af-a57f-6156ab6a5198",
          "feedName": "Inside Misys 1",
          "layout": "grid-with-no-images",
          "page": 1,
          "background": "http://misysnews/wp-content/plugins/misysnews/app/media/corpo_bg_01.jpg"
        },
        {
          "id": "inside-misys 2",
          "feedURL": "http://home.insidemisys.com/_layouts/feed.aspx?xsl=1%26web=%2FNews%2Fnewsfeed%26page=a95fd203-81e5-4c1c-8c4f-4e16b3a9e636%26wp=040e3fde-267f-40af-a57f-6156ab6a5198",
          "feedName": "Inside Misys 2",
          "layout": "grid-with-no-images",
          "page": 2,
          "background": "http://misysnews/wp-content/plugins/misysnews/app/media/corpo_bg_01.jpg"
        },
        */

?>
