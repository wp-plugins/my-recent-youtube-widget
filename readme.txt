=== Plugin Name ===
Contributors: csixty4
Donate link: http://catguardians.org/
Tags: youtube, video, embed
Requires at least: 2.9
Tested up to: 3.0.1
Stable tag: 0.4

Embed the most recent YouTube videos for a user in a sidebar

== Description ==

Embed the most recent YouTube videos for a user in a sidebar

== Installation ==

1. Upload this plugin's directory to the `/wp-content/plugins` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add the "My Recent YouTube" widget to a sidebar on the "Widgets" admin screen

== Frequently Asked Questions ==

= What is the current status of this plugin? =

Development has been resumed in February, 2010 after a long hiatus.

= Why does this plugin need WordPress 2.9 =

Version 0.1 used DavesFileCache, a caching class I developed a while back. Unfortunately, another
of my plugins uses that same cache and there were conflicts. As this plugin is still an early
development version (although I appreciate all the folks trying it out), I figured now would be a good
time to adopt the new Transient API in WordPress 2.9.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the directory of the stable readme.txt, so in this case, `/tags/4.3/screenshot-1.png` (or jpg, jpeg, gif)
2. This is the second screen shot

== Changelog ==

= 0.4 =
* Added "show titles" option

= 0.3 =
* Fixed opening the "advanced" settings panel in Safari

= 0.2 =
* Replaced DavesFileCache with WordPress Transient API
* Better filtering on settings

= 0.1 =
* Early pre-relase version. Didn't cache.

== Upgrade Notice ==

= 0.3 =
Fixed the "advanced" panel in the widget editor when using the Safari browser

= 0.2 =
Caching keeps your blog from bogging down YouTube's servers and makes them less likely to block you
