<?php
add_action('admin_menu', 'wpc_plugin_menu');

function wpc_plugin_menu() {
	if (is_admin()) {
		add_options_page('Convore Plugin Options', 'Convore', 'manage_options', 'convore-wordpress-plugin', 'wpc_options');
		add_action('admin_init', 'register_wpcsettings' );
	}
}

function register_wpcsettings() { // whitelist options
  register_setting( 'wpcoption-group', 'convore_username' );
  register_setting( 'wpcoption-group', 'convore_password' );
  register_setting( 'wpcoption-group', 'convore_groupid' );
  register_setting( 'wpcoption-group', 'convore_ignoressl' );
  register_setting( 'wpcoption-group', 'convore_addpostlink' ); 
  register_setting( 'wpcoption-group', 'convore_create_topic_default' );
  register_setting( 'wpcoption-group', 'convore_create_message_default' );
  register_setting( 'wpcoption-group', 'convore_show_messages_default' );
  register_setting( 'wpcoption-group', 'convore_topic_header' );
  register_setting( 'wpcoption-group', 'convore_topic_height' );
  register_setting( 'wpcoption-group', 'convore_topic_pagination' );
  register_setting( 'wpcoption-group', 'convore_topic_pagination_count' );
}

function wpc_options() {
	if ( get_option("convore_username") != "" && get_option("convore_password") != "" ) {
		$url = "https://convore.com/api/groups.json";
  		$curl_handle=curl_init();
  		curl_setopt($curl_handle,CURLOPT_URL,$url);
		curl_setopt($curl_handle,CURLOPT_USERPWD,get_option("convore_username") . ":" . get_option("convore_password"));
		if ( get_option("convore_ignoressl") == "true") {
			curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
		}	
		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
		$result = json_decode(curl_exec($curl_handle));
		if ( $result == false)
		  	echo curl_error($curl_handle);
		curl_close($curl_handle);
		$convore_group_list = $result->{'groups'};
	}
	
?>
<div class="wrap">
	<h2>Convore Plugin Options</h2>
	<form method="post" action="options.php"> 
		<?php settings_fields( 'wpcoption-group' ); ?>
		<table class="form-table">
        	<tr valign="top">
        		<th scope="row">Convore Username</th>
        		<td><input type="text" name="convore_username" value="<?php echo get_option('convore_username'); ?>" /></td>
        	</tr>
         
        	<tr valign="top">
        		<th scope="row">Convore Password</th>
        		<td><input type="password" name="convore_password" value="<?php echo get_option('convore_password'); ?>" /></td>
        	</tr>
        
        	<tr valign="top">
        		<th scope="row">Convore Group</th>
        		<td>
        		<select name="convore_groupid">
				<?php if ( $convore_group_list == false ) { ?>
        			<option disabled="disabled">Enter your username and password before selecting a group</option>
        		<?php } else {
        			foreach ($convore_group_list as $group) {
        				// For now let's only let people select groups that they created to cut down on spam
        				if ( $group->{'creator'}->{'username'} != get_option("convore_username") )
        				  continue;
        				if ( $group->{'id'} == get_option("convore_groupid") ) {
        					echo '<option value="' . $group->{'id'} . '" selected="selected">' . $group->{'name'} . "</option>\n";
        				} else {
        					echo '<option value="' . $group->{'id'} . '">' . $group->{'name'} . "</option>\n";
        				}
        			}
        		} ?>
        		</select>
        		</td>
        	</tr>

        	<tr valign="top">
        		<th scope="row">Disable SSL certificate checking</th>
        		<td>
        		<?php if ( get_option('convore_ignoressl') == "false" ) { ?>
        		
        		<input type="radio" name="convore_ignoressl" value="true" /> Yes
        		<input type="radio" name="convore_ignoressl" value="false" checked="checked"/> No
        		<?php } else { ?>
        		<input type="radio" name="convore_ignoressl" value="true" checked="checked"/> Yes
        		<input type="radio" name="convore_ignoressl" value="false" /> No
        		<?php } ?>
        		</td>
        	</tr>

        	<tr valign="top">
        		<th scope="row">Add Convore discussion link to posts</th>
        		<td>
        		<?php if ( get_option('convore_addpostlink') == "false" ) { ?>
        		
        		<input type="radio" name="convore_addpostlink" value="true" /> Yes
        		<input type="radio" name="convore_addpostlink" value="false" checked="checked"/> No
        		<?php } else { ?>
        		<input type="radio" name="convore_addpostlink" value="true" checked="checked"/> Yes
        		<input type="radio" name="convore_addpostlink" value="false" /> No
        		<?php } ?>
        		</td>
        	</tr>

        	<tr valign="top">
        		<th scope="row">Create Convore topic by default</th>
        		<td>
        		<?php if ( get_option('convore_create_topic_default') == "false" ) { ?>
        		
        		<input type="radio" name="convore_create_topic_default" value="true" /> Yes
        		<input type="radio" name="convore_create_topic_default" value="false" checked="checked"/> No
        		<?php } else { ?>
        		<input type="radio" name="convore_create_topic_default" value="true" checked="checked"/> Yes
        		<input type="radio" name="convore_create_topic_default" value="false" /> No
        		<?php } ?>
        		</td>
        	</tr>

        	<tr valign="top">
        		<th scope="row">Create message for new topics</th>
        		<td>
        		<?php if ( get_option('convore_create_message_default') == "false" ) { ?>
        		
        		<input type="radio" name="convore_create_message_default" value="true" /> Yes
        		<input type="radio" name="convore_create_message_default" value="false" checked="checked"/> No
        		<?php } else { ?>
        		<input type="radio" name="convore_create_message_default" value="true" checked="checked"/> Yes
        		<input type="radio" name="convore_create_message_default" value="false" /> No
        		<?php } ?>
        		</td>
        	</tr>

        	<tr valign="top">
        		<th scope="row">Replace comments with Convore by default</th>
        		<td>
        		<?php if ( get_option('convore_show_messages_default') == "false" ) { ?>
        		
        		<input type="radio" name="convore_show_messages_default" value="true" /> Yes
        		<input type="radio" name="convore_show_messages_default" value="false" checked="checked"/> No
        		<?php } else { ?>
        		<input type="radio" name="convore_show_messages_default" value="true" checked="checked"/> Yes
        		<input type="radio" name="convore_show_messages_default" value="false" /> No
        		<?php } ?>
        		</td>
        	</tr>

        	<tr valign="top">
        		<th scope="row">Convore topic header</th>
        		<?php if ( get_option('convore_topic_header') != '' ) { ?>
        			<td><input type="text" name="convore_topic_header" value="<?php echo get_option('convore_topic_header'); ?>" /></td>
        		<?php } else { ?>
        			<td><input type="text" name="convore_topic_header" value="Convore discussion" /></td>
        		<?php } ?>
        	</tr>

        	<tr valign="top">
        		<th scope="row">Convore topic height (in pixels, blank for no limit)</th>
        		<td><input type="text" name="convore_topic_height" value="<?php echo get_option('convore_topic_height'); ?>" /></td>
        	</tr>

        	<tr valign="top">
        		<th scope="row">Paginate Convore comments</th>
        		<td>
        		<?php if ( get_option('convore_topic_pagination') == "true" ) { ?>	
        		<input type="radio" name="convore_topic_pagination" value="true" checked="checked" /> Yes
        		<input type="radio" name="convore_topic_pagination" value="false" /> No
        		<?php } else { ?>
        		<input type="radio" name="convore_topic_pagination" value="true" /> Yes
        		<input type="radio" name="convore_topic_pagination" value="false" checked="checked" /> No
        		<?php } ?>
        		</td>
        	</tr>

        	<tr valign="top">
        		<th scope="row">Number of messages per page (if paginated)</th>
        		<td><input type="text" name=convore_topic_pagination_count value="<?php echo get_option('convore_topic_pagination_count'); ?>" /></td>
        	</tr>

        </table>
    
   	 	<p class="submit">
    		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    	</p>
	</form>
</div>
<?php
}
?>