<?php
// This is not actually called from within WordPress but we need the WP settings so we have to load wp-load.php
require_once('../../../wp-load.php');
// Get the group and topic IDs from the HTTP REQUEST
$convore_topic_id = $_REQUEST['topic_id'];
$convore_group_id = $_REQUEST['group_id'];
$convore_cursor = $_REQUEST['cursor'];

// Set up variables to connect to Convore
$url = 'https://convore.com/api/live.json';
$curl_get_fields = "group_id=$convore_group_id&topic_id=$convore_group_id";
if ( isset($_REQUEST['cursor']))
	$curl_get_fields .="&cursor=$convore_cursor";
$url .= '?' . $curl_get_fields;

// Connect to the Convore service
$curl_handle=curl_init();
curl_setopt($curl_handle,CURLOPT_URL,$url);
curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
$result = curl_exec($curl_handle);
curl_close($curl_handle);

$result = json_decode($result);
$result_messages = $result->{'messages'};

$count = 0;

// If we received any messages from Convore, iterate through them
if ( $result_messages != null ) {
	foreach ($result_messages as $result_message) {
		// Because the API doesn't actually filter by topic right now, there
		// may be a lot of irrelevant response messages.  So let's check
		// to make sure this message is one we want to process.
		if ( $result_message->{'kind'} == "message" &&
			$result_message->{'group'} == $convore_group_id &&
			$result_message->{'topic'}->{'id'} == $convore_topic_id) {
			
			// Now that we have decided we want to process this message
			// extract the results and place them in local variables
			$user = $result_message->{'user'}->{'username'};
			$userurl = $result_message->{'user'}->{'url'};		
			$userimg = $result_message->{'user'}->{'img'};
			$timestamp = $result_message->{'date_created'};
			$message = $result_message->{'message'};
			$cursor = $result_message->{'_id'};
		
			// And now add those local variables to the return data set
			$return_data[$count]->{'user'} = $user;
			$return_data[$count]->{'userurl'} = $userurl;
			$return_data[$count]->{'userimg'} = $userimg;			
			date_default_timezone_set(get_option('timezone_string'));
			$return_data[$count]->{'timestamp'} = date('D, M j, Y H:i:s', $timestamp);
			$return_data[$count]->{'message'} = $message;
			$return_data[$count]->{'cursor'} = $cursor;
		
			$count++;
		}
	}
}

echo json_encode($return_data);
?>