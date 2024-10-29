=== Plugin Name ===
Contributors: kubi23
Tested up to: 5.9.3
Stable tag: 5.0.0
Requires at least: 2.6
Tags: file, picture, upload, permission, thumbnail, files, pictures, thumbnails, upload


This plugin changes the file permissions of thumbnails, pictures and files after the upload.

== Description ==

When WordPress is installed on certain webhosters (i.e. german webhoster "Domainfactory") there 
might be problems when uploading a file. This *might* also occur when the webserver ist running PHP
as CGI-Module. Unfortunatly the file permissions are not set correctly, which makes the files inaccessible.
This plugin changes the file permissions of pictures, thumbnails and files after the files have been uploaded.

== Installation ==

1. Unzip
2. Copy complete folder to wp-content/plugins
3. Activate plugin 
4. Check correct root path under "Upload file permissions" within settings

== Screenshots ==

1. Settings in WP-Admin

== Version History ==

* Version 5.0.0
	* Major update
* Version 4.0.0
	* Major update
* Version 3.5
	* Fixed Bug when uploading pictures to old postings/pages
* Version 3.4
	* Fixed Bug when enabling logging
* Version 3.3
	* Fixed Bug that producead a warning when creating year and month based folders
* Version 3.2
	* Fixed Bug when creating year and month based folders
* Version 3.1
	* Fixed Bug when saving WP-Path
	* Fixed Bug when testing WP-Path
	* Updated language file
* Version 3.0
	* Added compatibility to WordPress 2.7
	* Now requires at least WordPress 2.6
	* Updated language file
	* Updated readme file
* Version 2.8
	* Fixed bug when using year-based upload folders
* Version 2.7
	* Minor code cleanup
* Version 2.6
	* Fixed bug when using WP 2.6
* Version 2.5
	* Deactivation function was not working correct
* Version 2.4
	* Minor changes for WP automatic plugin update
	* Minor code changes
	* Updated language file
* Version 2.3
	* Fixed incompatiblity with WordPress 2.5.1
	* Added language files
	* Check if class exists
* Version 2.2
	* Fixed bug when adding a picture to the editor
* Version 2.1
	* Plugin settings are deleted upon deactivation
* Version 2.0
	* Complete class-based redesign
	* Fixed Bug when only one log entry was available 
	* Last log entry is now displayed first
	* Plugin now works if same filename already exists
	* Plugin now handles files with their server stored name and not the original one
	* Plugin now requires WP 2.1 or higher
* Version 1.7
	* Fixed Bug when uploading a file as image 
	* Plugin now works with 2.5 and prior
* Version 1.5.1
	* Fixed Bug when deactivating logging
    * Deleted double declared variable
* Version 1.5
	* Minor Security Fix
    * Some code review
    * Optical adjustments
* Version 1.4
	* Added logging function
    * Fixed Problem with capitals in filenames
* Version 1.3
    * Plugin now works when saving files not in year and month based folder structure
* Version 1.2
    * Added check for user permissions to change settings
    * Only the uploaded file is handled
* Version 1.1
    * Added page for settings
    * Non-Standard upload-folders are checked
    * Root-Path to upload-folder can be set
    * Added test function
* Version 1.0beta
    * Initial verion