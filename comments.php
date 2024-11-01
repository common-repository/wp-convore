<?php
    global $post;
    
    // Support pagination
    if (isset($_GET['convore_page'])) {
      $convore_page = $_GET['convore_page'];
    } else {
    	$convore_page = 1;
    }
    if (get_option('convore_topic_pagination') == 'true') {
    	if (get_option('convore_topic_pagination_count') != "") {
    		$pagination_count = get_option('convore_topic_pagination_count');
    	} else {
    		$pagination_count = 10;
    	}
    }
    
    $convore_topic_id = get_post_meta($post->ID, 'convore_topic_id', true);
	$url = 'https://convore.com/api/topics/' . $convore_topic_id . '/messages.json';	
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

	$convore_topic_url = get_convore_topic_url();
	$messages = $result->{'messages'};
?>
<style type="text/css">
div.convore_messages {
	clear: both;
	border-top: 2px solid;
<?php if ( get_option('convore_topic_height') != '' ) { ?>
	height: <?php echo get_option('convore_topic_height'); ?>px;
	overflow: scroll;
<?php } ?>
}

div.convore_message {
	margin-top: 5px;
}

span.convore_empty_message {
	font-style: italic;
}

div.convore_nav_legend {
	width: 20%;
	float: left;
}

div.convore_nav_prev {
	width: 40%;
	float: left;
}

div.convore_nav_next {
	width: 40%;
	float: right;
}
</style>

<div class="convore_topic">
	<h2><?php echo get_option('convore_topic_header'); ?></h2>

<?php
	if ( get_option('convore_topic_pagination') == 'true') {
		echo "<div class='convore_nav'>";
		echo "<div class='convore_nav_legend'>";
		if ( count($messages) > $pagination_count)
		  echo "Go to page";
		echo '</div>';
		
		if ( $convore_page > 1 ) {
			
			// Build the $has_first_url link
			// Don't know if we are the first GET query or not
			if (  strpos(get_permalink($post->ID), '?') ) {
				$has_first_url = get_permalink($post->ID) . '&convore_page=1';
			} else {
				$has_first_url = get_permalink($post->ID) . '?convore_page=1';	
			}

			// Build the $has_previous_url link
			// Don't know if we are the first GET query or not
			if ( strpos(get_permalink($post->ID), '?') ) {
				$has_previous_url =  get_permalink($post->ID) . '&convore_page=' . ($convore_page - 1);
			} else {
				$has_previous_url =  get_permalink($post->ID) . '?convore_page=' . ($convore_page - 1);	
			}
						
			echo "<div class='convore_nav_prev'>";
			if ( $convore_page > 2 )
				echo '<a id="convore_nav_first_link" href="' . $has_first_url . '" title="First page of Convore messages">First</a> ';
			echo '<a id="convore_nav_prev_link" href="' . $has_previous_url . '" title="Previous page of Convore messages">Previous</a>';
			echo '</div>';
		}
		if ( count($messages) > ($convore_page * $pagination_count) ) {
			
			// Build $has_last_url link
			// Don't know if we are the first GET query or not
			if ( strpos(get_permalink($post->ID), '?') ) {
				$has_last_url = get_permalink($post->ID) . '&convore_page=' . ceil(count($messages) / $pagination_count);
			} else {
				$has_last_url = get_permalink($post->ID) . '?convore_page=' . ceil(count($messages) / $pagination_count);	
			}

			// Don't know if we are the first GET query or not
			if ( strpos(get_permalink($post->ID), '?') ) {
				$has_next_url = get_permalink($post->ID) . '&convore_page=' . ($convore_page + 1);
			} else {
				$has_next_url = get_permalink($post->ID) . '?convore_page=' . ($convore_page + 1);
			}		
		  	
		  	echo "<div class='convore_nav_next'>";
			echo '<a id="convore_nav_next_link" href="' . $has_next_url . '" title="Next page of Convore messages">Next</a>';
			if ( ceil(count($messages) / $pagination_count) > ($convore_page + 1) )
			  echo ' <a id="convore_nav_last_link" href="' . $has_last_url . '" title="Last page of Convore messages">Last</a><br />';
			echo '</div>';
		}
			echo '</div>';		  
?>
<?php 
	}
?>
	
	<div class="convore_messages" id='convore_messages'>
<?php
	// TODO Implement pagination using $message->{'id'} and until_id in API call
	if ( $messages != null )
	{
		for ( $i=0; $i < count($messages); $i++) {
			$message = $messages[$i];
			// Pagination support
			if (get_option('convore_topic_pagination') == 'true') {
				// Because the API has no from_id we have to skip the previous pages' entries
		  		if ( $i < ( ($convore_page - 1) * $pagination_count) )
		  			continue;
			  		
				// Only show $pagination_count messages
				if ( $i >= ( ( ($convore_page - 1) * $pagination_count) + $pagination_count ) )
					break;
			}
			
			$user = $message->{'user'}->{'username'};
			$userurl = 'http://www.convore.com' . $message->{'user'}->{'url'};
			$userimg = $message->{'user'}->{'img'};
			date_default_timezone_set(get_option('timezone_string'));
			$timestamp = date('D, M j, Y H:i:s', $message->{'date_created'});
			$message = $message->{'message'};
?>
		<div class="convore_message">
		  <span class="convore_message"><?php echo $message; ?></span><br />
		  <span class="convore_user_img">
		  	<img width="21" height="21" src="<?php echo $userimg; ?>" alt="Avatar for <?php echo $user; ?>" /> </span>
		  	<span class="convore_user"><a href="<?php echo $userurl; ?>"><?php echo $user; ?></a></span> 
		  	<span class="convore_timestamp"><?php echo $timestamp; ?></span><br /><br />
		</div>
<?php
		}
	} else {?>
		<div class="convore_message">
		  <span class="convore_empty_message">No messages from Convore yet</span><br />
		</div>
	
	<?php }?>
	</div>
	<a href="<?php echo $convore_topic_url; ?>">Join the conversation on Convore</a>

</div>

<script type="text/javascript">
// Initially implemented this using standard closure syntax but it didn't work.  Don't know why.  Doesn't matter.
jQuery(document).ready(convore_live_update(jQuery));

function convore_live_update($) {
	// Only want to live update the messages on the last page
	if ( document.getElementById('convore_nav_next_link') )
		return;
	
	var jsonUrl = "<?php echo plugins_url(); ?>/wp-convore/livecomments.php";
	var convore_group_id = <?php echo get_option('convore_groupid');?>;
	var convore_topic_id = <?php echo get_post_meta($post->ID, 'convore_topic_id', true);?>;
	jsonUrl += "?group_id=" + convore_group_id + "&topic_id=" + convore_topic_id;
	if ( cursor != false )
		jsonUrl += "&cursor=" + cursor;
	   
	jQuery.getJSON(  
			jsonUrl,  
			function(json) {
				update_convore_topic(json);
			}
	);
	setTimeout("convore_live_update($)", 10000);
}
</script>