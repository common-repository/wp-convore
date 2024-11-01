<?php
/*
Plugin Name: Convore integration
Plugin URI: http://www.michaelridley.info/wp-convore-wordpress-convore-plugin/
Description: Creates new Convore topics in a specified group whenever new blog posts are published and links to the conversation from the post.
Version: 1.0.3
Author: Michael Ridley
Author URI: http://www.michaelridley.info/
License: GPL2
*/

/*  Copyright 2011  Michael Ridley  (email : michael@secretelite.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
 * Register and retrieve options
 */

add_option("convore_username");
add_option("convore_password");
add_option("convore_groupid");

$convore_username = get_option("convore_username");
$convore_password = get_option("convore_password");
$convore_groupid = get_option("convore_groupid");

include_once(dirname(__FILE__) . '/options.php');
include_once(dirname(__FILE__) . '/postpanel.php');
include_once(dirname(__FILE__) . '/widget.php');

function wpc_installed() {
	return get_option('convore_username') && get_option('convore_password') && get_option("convore_groupid");
}

/*
 * Create new topic when a post is published (should check for duplicates)
 */

add_action("publish_post", "wpc_create_topic");

function wpc_create_topic($post_id) {
	if ( !wpc_installed() )
		return;

	// If the post already has a convore topic associated with it, we don't want to create a new one.
	if ( get_post_meta($post_id, "convore_topic_id", true) != "" )
    	return;
    	
    // If the post meta data says not to create a Convore topic then just return
    if ( get_post_meta($post_id, 'create_convore_topic', true) == "false")
    	return;
    
  	$url = "https://convore.com/api/groups/" . get_option("convore_groupid") . "/topics/create.json";
  	$topic = html_entity_decode(get_the_title($post_id), ENT_QUOTES, 'UTF-8');
  	$topic = iconv('UTF-8', 'ASCII//TRANSLIT', $topic);
  	$curl_post_fields = 'name=' . urlencode( $topic );
  	$curl_handle=curl_init();
  	curl_setopt($curl_handle,CURLOPT_URL,$url);
	curl_setopt($curl_handle,CURLOPT_USERPWD,get_option("convore_username") . ":" . get_option("convore_password"));
	if ( get_option("convore_ignoressl") == "true") {
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
	}	
	curl_setopt($curl_handle,CURLOPT_POST,1);
	curl_setopt($curl_handle,CURLOPT_POSTFIELDS,$curl_post_fields);
	curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
	$result = json_decode(curl_exec($curl_handle));
	if ( $result == false)
	  echo curl_error($curl_handle);
	curl_close($curl_handle);
	$convore_topic_id = $result->{'topic'}->{'id'};
	add_post_meta($post_id, 'convore_topic_id', $convore_topic_id, true);
	if (get_post_meta($post_id, 'create_convore_message', true) == 'true')
		wpconvore_post_message($convore_topic_id, 'This topic automatically created via wp-convore for ' . get_permalink($post_id));
}

function wpconvore_post_message($topic_id, $message) {
	if ( !wpc_installed() )
		return;
		
	$url = 'https://convore.com/api/topics/' . $topic_id . '/messages/create.json';
	$curl_post_fields = 'message=' . urlencode($message);
  	$curl_handle=curl_init();
  	curl_setopt($curl_handle,CURLOPT_URL,$url);
	curl_setopt($curl_handle,CURLOPT_USERPWD,get_option("convore_username") . ":" . get_option("convore_password"));
	if ( get_option("convore_ignoressl") == "true") {
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
	}	
	curl_setopt($curl_handle,CURLOPT_POST,1);
	curl_setopt($curl_handle,CURLOPT_POSTFIELDS,$curl_post_fields);
	curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
	$result = json_decode(curl_exec($curl_handle));

	return($result);
}


add_filter("the_content", wpc_add_convore);

function wpc_add_convore($content) {
	global $post;
	
	// Return if not a single page
	if (!is_single()) 
		return($content);
	
	// If the option to add the link to the post is not set then just echo
	// the post content and return.
	if ( get_option("convore_addpostlink") == "false") {
		echo $content;
		return;
	}
	$convore_topic_id = get_post_meta($post->ID, "convore_topic_id", true);
	
	// If the Convore topic ID is not set for this post then just echo
	// the content and return.
	if ( $convore_topic_id == "" )
	{
	  echo $content;
	  return;
	}
	
	$convore_topic_url = get_convore_topic_url();
	// TODO Contact Convore about approval and getting a better logo rather than my screencap
	$convore_logo_url = plugins_url() . '/wp-convore/images/convore_logo_small.png';
	
	$convore_content = "<br />\n";
	$convore_content .= '<a href="' . $convore_topic_url . '"><img src="' . $convore_logo_url . '" alt="Follow the conversation on Convore" title="Follow the conversation on Convore"/></a>';
	$convore_content .= "<br />";
	return($content . $convore_content);
}


// TODO Figure out some way to allow comments and Convore to coexist

add_filter('comments_template', 'wpc_comments_template');

function wpc_comments_template($value) {
	global $post;
	// Check to see whether this post is really replacing their
	// comments with Convore
	if ( get_post_meta($post->ID,'convore_show_messages',true) == "true")
		return dirname(__FILE__) . '/comments.php';
	return $value;
}

add_action('init', 'wpc_init_method');

function wpc_init_method() {
	wp_register_script( 'wpc-comments', plugins_url() . '/wp-convore/js/comments.js', array('jquery'));
	wp_register_script( 'wpc-users', plugins_url() . '/wp-convore/js/users.js', array('jquery'));
    wp_enqueue_script( 'wpc-comments' );
    wp_enqueue_script( 'wpc-users' );
}

/*
 * Shared functions
 */

function get_convore_topic_url() {
	global $post;
	
	$convore_topic_id = get_post_meta($post->ID, "convore_topic_id", true);
	
	// If the Convore topic ID is not set for this post then return null
	if ( $convore_topic_id == "" )
	{
	  return(null);
	}
	
	$url = 'https://convore.com/api/topics/' . $convore_topic_id . '.json';
	$curl_handle=curl_init();
  	curl_setopt($curl_handle,CURLOPT_URL,$url);
	curl_setopt($curl_handle,CURLOPT_USERPWD,get_option("convore_username") . ":" . get_option("convore_password"));
	if ( get_option("convore_ignoressl") == "true") {
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
	}	
	curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
	$result = json_decode(curl_exec($curl_handle));
	curl_close($curl_handle);
	
	$convore_topic_url = 'http://www.convore.com' . $result->{'topic'}->{'url'};
	return($convore_topic_url);
}

function get_convore_group_url() {
	if ( !wpc_installed() )
		return(null);
	
	$url = 'https://convore.com/api/groups/' . get_option('convore_groupid') . '.json';
	$curl_handle=curl_init();
  	curl_setopt($curl_handle,CURLOPT_URL,$url);
	curl_setopt($curl_handle,CURLOPT_USERPWD,get_option("convore_username") . ":" . get_option("convore_password"));
	if ( get_option("convore_ignoressl") == "true") {
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
	}	
	curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
	$result = json_decode(curl_exec($curl_handle));
	curl_close($curl_handle);
	return('https://www.convore.com' . $result->{'group'}->{'url'});	
}
?>
