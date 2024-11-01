var cursor = false;

function update_convore_topic(json) {
	
	// TODO Implement pagination

	if ( json == null )
		return;
	
	for (var i=0; i< json.length; i++) {
		var message = json[i];
		
		// In case we are somehow processing old messages, skip them
		// Not really sure if this code actually works since the cursor
		// is alphanumeric it's not really ordinal/comparable.
		if ( message.cursor <= cursor )
			continue;
		cursor = message.cursor;
		
		// Get the convore topic div element so we can add to it
		var convore_messages_div = document.getElementById('convore_messages');

		// Create a new message div and set the class
		var convore_message_div = document.createElement("div");
		convore_message_div.className = "convore_message";

		// Create a new HTML span for the message
		var convore_message_span = document.createElement('span');
		convore_message_span.className = 'convore_message';
		convore_message_span.innerHTML = message.message;
	
		// Create a new HTML span for the user's avatar image
		var convore_user_img_span = document.createElement('span');
		convore_user_img_span.className = 'convore_user_img';
	
		// Create a new HTML img for the user's avatar image
		convore_user_img = document.createElement('img');
		convore_user_img.width = '21';
		convore_user_img.height = '21';
		convore_user_img.src = message.userimg;
		convore_user_img.alt = 'Avatar for ' + message.user;
	
		// Create a new HTML span for the user name
		convore_user_span = document.createElement('span');
		convore_user_span.className = 'convore_user';
		
		// Create a new HTML a for the user name link
		var convore_user_span_link = document.createElement('a');
		convore_user_span_link.href = 'http://www.convore.com' + message.userurl;
		convore_user_span_link.setAttribute('href', 'http://www.convore.com' + message.userurl);
		convore_user_span_link.innerHTML = message.user;
		convore_user_span.appendChild(convore_user_span_link);
		
		// Create a new HTML span for the timestamp
		convore_timestamp_span = document.createElement('span');
		convore_timestamp_span.className = 'convore_timestamp';
		convore_timestamp_span.innerHTML = message.timestamp;

		// Add the children
		convore_message_div.appendChild(convore_message_span);
		convore_message_div.appendChild(document.createElement('br'));
		convore_user_img_span.appendChild(convore_user_img);
		convore_message_div.appendChild(convore_user_img_span);
		convore_message_div.appendChild(document.createTextNode(' '));
		convore_message_div.appendChild(convore_user_span);
		convore_message_div.appendChild(document.createTextNode(' '));
		convore_message_div.appendChild(convore_timestamp_span);

		// Now add the new message to the topic div
		convore_messages_div.appendChild(convore_message_div);	
		convore_messages_div.appendChild(document.createElement('br'));
		
	}
}