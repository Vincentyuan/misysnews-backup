<?php

add_action( 'rss2_item', 'add_post_featured_image_as_rss_item_enclosure' );
function add_post_featured_image_as_rss_item_enclosure() {
	if ( ! has_post_thumbnail() )
		return;

	$thumb_id = get_post_thumbnail_id(get_the_ID());    
	$thumb_data = wp_get_attachment_image_src( $thumb_id, 'full');
	if ( !$thumb_data )
		return;

	printf( 
		'<enclosure url="%s" type="%s" />',
		$thumb_data[0], 
		get_post_mime_type( $thumb_id ) 
	);
}

?>