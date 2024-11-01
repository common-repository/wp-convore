<?php
function widget_wpconvore($args) {
	if ( !wpc_installed() )
		return;

	extract($args);
	echo $before_widget;
		
  	$url = "https://convore.com/api/groups/" . get_option("convore_groupid") . "/online.json";
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

	if ( isset($result->{'count'}) && ($result->{'count'} > 1) ) {
		// Decrement the online user count by one because the API user will always be online
		$convore_user_count = $result->{'count'} - 1;
	} else {
		$convore_user_count = 'no';
	}
	$convore_users_online = $result->{'online'};
	
	$convore_group_url = get_convore_group_url();
?>
<?php echo $before_title;?>Convore Users Online<?php echo $after_title; ?>
<div id='convore_users_online'>
There are currently <?php echo $convore_user_count; ?> users online for this site's <a href="<?php echo $convore_group_url; ?>">Convore group</a>.
<ul>
<?php 
$count = 0;
foreach ( $convore_users_online as $convore_user) {
	if ($count > 9)
		break;
	// Don't print our own user name out because it doesn't mean anything as we are always online
	if ( $convore_user->{'username'} == get_option('convore_username') )
		continue;
?>
	<li>
		<img width="21" height="21" src="<?php echo $convore_user->{'img'}; ?>" alt="Avatar for <?php echo $convore_user->{'username'}; ?>" />
		<a href="https://www.convore.com<?php echo $convore_user->{'url'}?>"><?php echo $convore_user->{'username'}; ?></a>
	</li>
<?php } ?>
</ul>
</div>
<script type="text/javascript">
jQuery(document).ready(convore_live_users(jQuery));

function convore_live_users($) {

	var jsonUrl = "<?php echo plugins_url(); ?>/wp-convore/liveusers.php";
	   
	jQuery.getJSON(  
			jsonUrl,  
			function(json) {
				update_convore_users(json);
			}
	);
	setTimeout("convore_live_users($)", 10000);
}
</script>

<?php
	echo $after_widget;
}

// TODO Would be cool if you could configure the widget to change whether you see a list of people or a face grid and how many members are shown
function wpconvore_widget_init()
{
  register_sidebar_widget(__('Convore'), 'widget_wpconvore');
}
add_action("plugins_loaded", "wpconvore_widget_init");
?>