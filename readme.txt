=== Plugin Name ===
Contributors: mridley
Donate link: http://www.michaelridley.info/wp-convore-wordpress-convore-plugin
Tags: Convore
Requires at least: 3.1
Tested up to: 3.1
Stable tag: 1.0.3

This plugin allows your WordPress site to interface with Convore

== Description ==

This plugin allows a WordPress site to interface with Convore (http://www.convore.com).

The plugin will create a Convore topic for each WordPress post and link to the Convore topic at the bottom of the post content.

The plugin can optionally replace the comments section of the post with the Convore discussion which will be updated in real time via AJAX.

The plugin also has a WordPress widget that allows you to display the number of Convore users logged in who are members of your site's group and display the avatar image and username of the first 10 users.

== Installation ==

1. Upload `wp-convore.zip` to the `/wp-content/plugins/` directory
2. Unzip 'wp-convore.zip'
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Select Convore from the Settings menu and configure your user account and default options

== Configuration ==

After the plugin is installed you must go to the Convore settings menu which will be installed by the plugin and enter your Convore username and password.  Once you save the page you will have a drop-down list of Convore groups which you have created (if you don't have one for your blog, go to Convore and create a new group first).  You can then configure the plugin's default behavior.

* Disable SSL certificate checking

This option specifies whether to enforce SSL certificate checking or not.  This might be necessary to disable for some shared hosting environments.

* Add Convore discussion link to posts

This option specifies whether to add a link at the bottom of each post to the Convore topic.  The link will not be added if the post does not have an associated Convore topic.

* Create Convore topic by default

This option specifies whether to add a Convore discussion topic for each post as it's published (this is also configurable per-post and can be overridden from the Post screen).

* Replace comments with Convore by default

This option specifies whether to replace the post's comments section with the Convore topic discussion.  In the future it would be nice if the two could coexist but for now they can't.  This setting is just for the default behavior but each post can be configured on a per-post basis from the Post screen.

* Convore topic header

This option specifies the text in the header tag before the Convore topic messages are printed below the post in place of the comments.  The default text is "Convore discussion".

* Convore topic height (in pixels, blank for no limit)

This option specifies the height of the HTML div that contains the topic messages.  Conversations that are larger than the specified height will scroll within the div.  This allows site owners to keep very active conversations from making their WordPress pages super long.  If no value is specified the comments will not be limited.

== Frequently Asked Questions ==

= How can I use wp-convore with my WordPress Pages? =

Pages are not currently supported although it wouldn't be too hard to add for a future version.

= How can people chat on Convore from my WordPress site? =

There is no way given the current Convore API to allow users to add to the discussion from the WordPress site - they have to follow the Convore link and add to the topic on Convore itself.

= Why is my Convore group list empty? =

Right now the plugin only allows you send topics to a Convore group that you have created so as to minimize spam and observe good Internet etiquette.  So if you have not created any
Convore groups your list will be empty.

= Why am I not seeing real-time updates? =

It could be that JavaScript is disabled in your browser or it could also be that your group is marked "private".  This plugin is really meant for public Convore groups although it will work with private ones however the AJAX auto-updating won't work.

= Where did my comments go after I installed wp-convore? =

They didn't go anywhere, however right now there is no way to have both Convore and WordPress comments on the same post.  I agree that that's not ideal.  You can get the comments back by simply opening the edit post screen and setting the "Replace comments with Convore" option to "No".  Of course then you lose your Convore discussion embedding. 

= Why do I not show up in my list of Convore users online? =

Because the system is using your API key, you will *always* show up online.  So it filters you out and decrements the total number of users by one because it's not really meaningful.  If that makes you sad, I suggest creating an “admin” Convore account and having that account own the group and be configured as the WordPress Convore user so that your real account presence will be reflected accurately.

== Screenshots ==

1. Convore panel in post editing screen
2. Convore plugin options
3. Link to Convore icon and showing comments replaced with Convore discussion

== Room For Future Improvement ==

* Have the post metadata indicate the number of Convore messages associated with the post where it would show comment count.

* Right now if you delete a Convore topic that's associated with a post it won't automatically get recreated, but the post also will always think that it should have a Convore topic.  Probably should do something smarter there.

* Would be good to be able to have both Convore and traditional comments enabled at the same time.

== Changelog ==

= 1.0.3 =
Fixed the readme.txt so the screen shots match the descriptions

= 1.0.2 =
Fixed a problem where the version in the readme.txt was not maching the version in wp-convore.php
= 1.0.1 =
Fixed a bug where the online users would chop off the first user because of an array indexing problem

= 1.0 =
* Initial release.

== Upgrade Notice ==

N/A - initial version