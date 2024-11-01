<?php
add_action('add_meta_boxes', 'wpc_register_post_panel');
add_action('save_post', 'wpc_post_panel_save');

function wpc_register_post_panel() {
	add_meta_box( 'convore_post_panel', 'Convore', 'wpc_post_panel', 'post');
}


function wpc_post_panel() {
	global $post;
	echo 'Create Convore topic ';
	if ( get_post_meta($post->ID, 'create_convore_topic', 'true') == 'false' || ( get_option('convore_create_topic_default') == 'false') && get_post_meta($post->ID, 'create_convore_topic', 'true') !== 'true') {
		echo '<input type="radio" name="wpc_create_convore_topic" value="true"> Yes';
		echo '<input type="radio" name="wpc_create_convore_topic" value="false" checked="checked"> No';
	} else {
		echo '<input type="radio" name="wpc_create_convore_topic" value="true" checked="checked"> Yes';
		echo '<input type="radio" name="wpc_create_convore_topic" value="false"> No';
		
	}
	echo '<br />';
	echo 'Create Convore message';
	if ( get_post_meta($post->ID, 'create_convore_message', 'true') == 'false' || ( get_option('convore_create_message_default') == 'false') && get_post_meta($post->ID, 'create_convore_message', 'true') !== 'true') {
		echo '<input type="radio" name="wpc_create_convore_message" value="true"> Yes';
		echo '<input type="radio" name="wpc_create_convore_message" value="false" checked="checked"> No';
	} else {
		echo '<input type="radio" name="wpc_create_convore_message" value="true" checked="checked"> Yes';
		echo '<input type="radio" name="wpc_create_convore_message" value="false"> No';
		
	}
	
	echo '<br />';
	echo 'Replace comments with Convore ';
	if ( get_post_meta($post->ID, 'convore_show_messages', 'true') == 'false' || ( get_option('convore_show_messages_default') == 'false') && get_post_meta($post->ID, 'convore_show_messages', 'true') !== 'true') {
		echo '<input type="radio" name="wpc_convore_show_messages" value="true"> Yes';
		echo '<input type="radio" name="wpc_convore_show_messages" value="false" checked="checked"> No';
	} else {
		echo '<input type="radio" name="wpc_convore_show_messages" value="true" checked="checked"> Yes';
		echo '<input type="radio" name="wpc_convore_show_messages" value="false"> No';
		
	}
	
	
	// TODO Fix this security thing
	//wp_nonce_field('convore-post-panel', 'convore-post-panel-nonce');
	
}

function wpc_post_panel_save($post_id) {
	if ( !current_user_can( 'edit_post', $post_id ) )
      return $post_id;
//     if ( !check_admin_referer('convore-post-panel','convore-post-panel-nonce'))
//       return $post_id;
	$create_convore_topic = $_POST['wpc_create_convore_topic'];
	$create_convore_message = $_POST['wpc_create_convore_message'];
	$show_messages = $_POST['wpc_convore_show_messages'];
	
	if ( get_post_meta($post_id, 'create_convore_topic',true) == "")
		add_post_meta($post_id, 'create_convore_topic', $create_convore_topic, true);
	update_post_meta($post_id, 'create_convore_topic', $create_convore_topic);
	
	if ( get_post_meta($post_id, 'create_convore_message',true) == "")
		add_post_meta($post_id, 'create_convore_message', $create_convore_message, true);
	update_post_meta($post_id, 'create_convore_message', $create_convore_message);
	
	if ( get_post_meta($post_id, 'convore_show_messages',true) == "")
		add_post_meta($post_id, 'convore_show_messages', $show_messages, true);
	update_post_meta($post_id, 'convore_show_messages', $show_messages);
	
}
?>