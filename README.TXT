=== Plugin Name ===
Contributors: rotsee
Donate link: http://xn--ssongsmat-v2a.nu
Tags: recipes, food, recept, mat, Google Recipes, microdata, Säsongsmat 
Requires at least: 3.0
Tested up to: 3.1.3
Stable tag: 0.132

Connects recipes with the food database at Säsongsmat.nu, adding semantic micro data and information about seasonality.

== Description ==
The plugin will draw information from the Säsongsmat database API to (optionally) add to the recipes. It also allows the author, using some extra buttons in the mce editor, to mark up the recipe with HTML5 microdata tags of the type that e.g. Google Recipes and Bing recognizes (known as ”rich snippets”). These are also the tags Säsongsmat.nu uses to find recipes on the web.

The plugin is probably of limited use to authors not writing in Swedish, though some might like the microdata markup even if not using the database information from Säsongsmat. http://säsongsmat.nu can be written as http://xn--ssongsmat-v2a.nu if your browser does not handle ”ä”. 

Detailed information in Swedish at [Säsongsmat](http://säsongsmat.nu/artikel/Wordpress "Säsongsmats Wordpressplugin")

== Installation ==
1. Download and extract the zip file on your computer
1. Upload this directory 'sasongsmat' to your plugin directory, normally '/wp-content/plugins/'
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Start using the new buttons in the WYSIWYG editor to mark up your recipes

== Frequently Asked Questions ==

= How do I know if the micro data tags work? =

You can use [Googles rich snippets testing tool](http://www.google.com/webmasters/tools/richsnippets "Googles rich snippets testing tool") to confirm that the correct micro data tags are in your recipes.

= I like the idea, but the plugin sucks =

You are more than welcome to contribute to this plugin to make it better. Please send an email to mail@sasongsmat.nu.

== Screenshots ==

1. New buttons in the WYSIWYG editor

== Changelog ==

= 0.13 =
* Don't run parsing functions in admin mode
* Fixed javascript bug that could possibly effect the widgets settings page
* Added a simple widget showing ingrediens currently in season (Swedish). Work in progress

= 0.121 =
* Buggfix: Sätt inte ut author-tag när författare saknas

= 0.12 =
* Images for most buttons
* Implemented setting to prevent the plugin to add any tags automatically
* Added setting to change (or remove) the icon for ingrediens in season
* Checks to see that ingredients do not already have a link before linking them
* Allows html in ingredients
* Using Wordpress JQuery library instead of our own copy
* Using Wordpress JSON library instead of our own copy
* Plugin ready for translation
* Some cleaning up

= 0.11 =
* Automatically adding recipe types from categories
* Javascript moved to external server

= 0.1 =
* First beta

== Upgrade Notice ==

= 0.13 =
Bug affecting the widget settings page fixed
Simple widget added (Swedish)

= 0.12 =
More options in the settings page

= 0.11 =
This version loads slightly quicker
