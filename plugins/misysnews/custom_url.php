<?php

function mnews_add_custom_url_meta_box() {

		add_meta_box(
			'mnews_custom_url', 'Lien personnalisé',
			'mnews_custom_url_meta_box_callback',
			'post'
		);
}
add_action( 'add_meta_boxes_post', 'mnews_add_custom_url_meta_box' );


function mnews_custom_url_meta_box_callback( $post ) {
	wp_nonce_field( 'mnews_custom_url_meta_box', 'mnews_custom_url_meta_box_nonce' );
	$value = get_post_meta( $post->ID, 'mnews_custom_url', true );

	echo '<label class="screen-reader-text" for="mnews_custom_url_field">';
	echo 'Lien personnalis&eacute; pour cet article';
	echo '</label> ';
	echo '<input type="url" id="mnews_custom_url_field" name="mnews_custom_url_field" value="' . esc_attr( $value ) . '" style="width:98%" />';
  	echo '<p>Si renseign&eacute;, le lien personnalis&eacute; sera utilis&eacute; &agrave; la place du lien Wordpress de l\'article.</p>';
}

function mnews_save_custom_url_meta_box_data( $post_id ) {

	if ( ! isset( $_POST['mnews_custom_url_meta_box_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['mnews_custom_url_meta_box_nonce'], 'mnews_custom_url_meta_box' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
  
	if ( isset( $_POST['post_type'] ) && 'post' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}
	
	if ( ! isset( $_POST['mnews_custom_url_field'] ) ) {
		return;
	}

	$mnews_custom_url = esc_url( $_POST['mnews_custom_url_field'] );

	update_post_meta( $post_id, 'mnews_custom_url', $mnews_custom_url );
}
add_action( 'save_post', 'mnews_save_custom_url_meta_box_data' );

function mnews_apply_custom_url( $url, $post, $leavename ) {
  
	$value = get_post_meta( $post->ID, 'mnews_custom_url', true );
  
  if ( ! isset($value) || !$value || '' == $value ) {
    return $url;
  }else{
  	return $value;  
  }
  
}
add_filter( 'post_link', 'mnews_apply_custom_url', 10, 3 );