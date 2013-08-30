=== media helpers ===
Contributors: fab1en
Tags: media
Requires at least: 3.0.1
Tested up to: 3.6
Stable tag: 1.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Adds several helpers to the default WP Media Management.

== Description ==

This plugin adds several helpers to the default WP Media Management. Each helper tries to be as tiny as possible (as a feature and as lines of code) and independent from the other helpers. Helpers can be enabled one by one.The aim is to provide a fine control and to avoid all possible conflicts. 

 - Use an external URL as the media file and save it to the database like an uploaded media. This allow external media providers to be used inside the Media Library. You can also attach a new media to a post without specifying any file (fake media). This allows you to work on the media without having the file yet.
 - Duplicate media
 - Limit the resolution of uploaded images (WP allows you to limit the filesize, but not the resolution)
 - Update a media permalink each time its title is changed (now included in core for WP 3.5)
 - Change the parent of a previously attached media
 - Change the file of a media without creating a new media entry

== Installation ==

Upload the plugin files in your wp-content/plugins directory and go to the Plugins menu to activate it. 
Go to Settings > Media menu to enable particular helpers.

== Changelog ==

= 1.0 =
* Initial published version

= 1.1 =
* Change main file name (/!\ Warning : this will disable the plugin when upgrading, you will have to re-enable it after upgrade)
* Make it compatible with the new Media Manager introduced in WP 3.5
* Add "image link" feature to replace text by image in menu links
* Add "Rich description" feature to use a rich editor for medias description
* Enhance URl media feature with the possibility to create a new Media from a URL directly from the Media Library
* Several bugfixes


