function update_convore_users(json) {
	
	if ( json == null )
		return;
	
	// Get the group URL so we can link back to it
	var $convore_group_url = json['group_url'];
	
	// Get the convore users online div element so we can replace its contents
	var convore_users_div = document.getElementById('convore_users_online');

	// Create new div to replace old one with
	var convore_users_div_new = document.createElement('div');
	convore_users_div_new.id = 'convore_users_online';
	
	// Create a new text node
	var convore_users_online_count = document.createElement('span');
	if ( json.user_count > 0 ) {
		user_count = json.user_count;
	} else {
		user_count = "no";
	}
	convore_users_online_count.appendChild(document.createTextNode('There are currently ' + json.user_count + ' users online for this site\'s '));
	var convore_group_link = document.createElement('a');
	convore_group_link.href = $convore_group_url;
	convore_group_link.setAttribute('href', $convore_group_url);
	convore_group_link.innerHTML = 'Convore group';
	convore_users_online_count.appendChild(convore_group_link);
	convore_users_online_count.appendChild(document.createTextNode('.'));
	convore_users_div_new.appendChild(convore_users_online_count);

	// Create a new unordered list for the online users
	var convore_users_ul = document.createElement('ul');
	
	for (var i=0; i< json.online_users.length; i++) {
		var user = json.online_users[i];
				
		// Create a new list item for the online user
		var convore_user_li = document.createElement('li');
		
		// Created a new image for the user's avatar
		var convore_user_img = document.createElement('img');
		convore_user_img.width = '21';
		convore_user_img.height = '21';
		convore_user_img.src = user.img;
		convore_user_img.alt = 'Avatar for ' + user.username;
		
		// Create a link for the user's name and profile
		var convore_user_link = document.createElement('a');
		convore_user_link.href = 'https://www.convore.com/' + user.url;
		convore_user_link.setAttribute('href', 'https://www.convore.com/' + user.url);
		convore_user_link.innerHTML = user.username;
		
		convore_user_li.appendChild(convore_user_img);
		convore_user_li.appendChild(document.createTextNode(' '));
		convore_user_li.appendChild(convore_user_link);

		convore_users_ul.appendChild(convore_user_li);		
	}
	
	convore_users_div_new.appendChild(convore_users_ul);
	
	convore_users_div.parentNode.replaceChild(convore_users_div_new, convore_users_div);
}