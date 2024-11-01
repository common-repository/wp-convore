<?php
// This is not actually called from within WordPress but we need the WP settings so we have to load wp-load.php
require_once('../../../wp-load.php');

if ( !wpc_installed() )
	return;

$url = "https://convore.com/api/groups/" . get_option("convore_groupid") . "/online.json";
//$url = 'https://convore.com/api/account/online.json';
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
$return_json = array();
$return_json['group_url'] = $convore_group_url;
$return_json['user_count'] = $convore_user_count;

// Before printing the array we need to remove the element for the API user
for ( $i = 0; $i < count($convore_users_online); $i++) {
	// Don't print our own user name out because it doesn't mean anything as we are always online
	if ( $convore_users_online[$i]->{'username'} == get_option('convore_username') )
		unset($convore_users_online[$i]);
}

// Truncate the array before returning
if ( count($convore_users_online) > 10 ) {
	$convore_users_online_truncated = array_splice($convore_users_online, 10, count($convore_users_online));
}

// Need to reindex the array
if ( is_array($convore_users_online))
  $convore_users_online = array_values($convore_users_online);

$return_json['online_users'] = $convore_users_online;

echo json_encode($return_json);
?>