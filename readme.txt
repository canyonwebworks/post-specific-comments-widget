=== Post-Specific Comments Widget (PSCW) ===
Contributors: littlepackage
Donate link: https://www.paypal.me/littlepackage
Tags: testimonial, feedback, comment, excerpt, guestbook
Requires at least: 4
Requires PHP: 5.4
Tested up to: 5.5
Stable tag: 2.2
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This lightweight plugin allows you to show comments for a *specific post or page* in a widget. 

This can come in very handy when trying to showcase the comments from a single post or page, such as customer feedback, testimonials, or guestbook. It can also be set to show all comments, with formatting options and optional avatars.

== Description ==

This lightweight plugin allows you to show comments for a *specific post or page* in a widget. 

This can come in very handy when trying to showcase the comments from a single post or page, such as customer feedback, testimonials, or guestbook. It can also be set to show all comments, with formatting options and optional avatars.

Allows you to list comments in default manner (by Author and Post Title) or by Author and a variable-character excerpt and ellipsis (or other chosen trailing characters). Two new post formats to version 1.1 are (Excerpt) and (Excerpt) - (Author). Version 1.2 added shortcodes so users can create their own formats! Version 1.2.4 adds an Avatar/Gravatar shortcode.

Thanks for downloading; I hope this can help you. If this really helped you, and especially if you can profit from it, please [leave feedback at Wordpress.org](https://wordpress.org/support/view/plugin-reviews/post-specific-comments-widget?filter=5) or [consider sending a dollar or two my way!](https://www.paypal.me/littlepackage)

== Installation ==

1. Upload the "post-specific-comments-widget" folder to the /wp-content/plugins/ directory
2. Activate plugin through the 'Plugins' menu in Wordpress
3. Edit settings under Appearance -> Widgets (Hint: add the Post-Specific Comments widget to your sidebar or footer widgets area).

You'll need a post or page ID number to set up the widget. The ID number can be found by hovering over the post or page title while in the "All Pages" or "All Posts" list view Wordpress panels. The number will show itself after "post=" in your browser's add-on bar (usually at the lower left hand corner of the screen). If you are editing the desired post, the ID number will show in the URL bar.

You don't have to limit the comments to one post or page, though. You can show all your posts/pages by typing in the number 0 (zero) instead of a post number.

== Frequently Asked Questions ==

If you have questions or requests, don't hesitate to ask. 

= How do I use the shortcodes? = 

If you want to customize the way your comment is printed out, you can use the following shortcodes as placeholders for the intended output: [AUTHOR], [TITLE], [LINKED-TITLE], [EXCERPT], [DATE].

So for example, if you choose the "other" format, you could then enter "Comment by ~[AUTHOR]~ on [DATE]" and the plugin would magically replace the shortcodes with the author name/link, surrounded by tildes/swiggles, and then the date. 

You can also use some limited HTML to further style the format.

= How do I use the avatar shortcode? =

[AVATAR] will display a 32px avatar.
[AVATAR 80] will display an 80px avatar.
[AVATAR 128] will display an 128px avatar.
[AVATAR48] and [AVATR 80] will not display avatars because they are not typed correctly.

Avatars will need styling depending on your Wordpress theme. There are ample CSS tags to get them where you want them. Avatar display depends on your avatar settings in Wordpress -> Settings -> Discussion

== Screenshots ==

1. Screenshot of the menu settings
2. Screenshot of how to find post/page ID number. In this case the ID is 1696 when hovering over post “Change Password.”

== Upgrade Notice ==

= 2.0.2 = 
Recommend upgrade to version 2.0.2 for security enhancements; compatible with WP 5.3.2

== Changelog ==

= 2.2 =
* Feature - Parent class WP_Widget HTML5 format now possible
* Feature - [LINKED-TITLE] shortcode now available for a linked title
* Feature - 'pscw_comment_title' and 'pscw_linked_comment_title' filters now available for filtering the comment title
* Feature - 'pscw_comment_author' now available for filtering comment author
* Tweak - Escape attributes in settings form
* Tweak - WP instance parameter used for unique widget ID naming
* Tweak - further sanitization of input and output values for security
* Testing with WP 5.5.3

= 2.1.2 =
* Reverse chronological order of changelog, clean up upgrade notices in readme.txt

= 2.1.1 - 19/Sep/2020 = 
* Testing to WP 5.5.1

= 2.1 - 21/Jan/2020 = 
* Testing to WP 5.3.2
* HTML escape translated text strings, sanitize string input more carefully
* Rename translation files to work, and update
* Remove .recentcomments CSS from wp_head
* Fix for missing $aRecentAvatar value when using "other format" style 
* use en dash instead of hyphen for "excerpt – author" format (more visible)
* labels work for radio input fields on backend
* 'pscw_filter_output' filter added to further adjust output before sending to screen

= 2.0.1 - 30/Jul/2019 =
* Testing to WP 5.2.2
* Update author & plugin URI

= 2.0 - 25/Mar/2019 =
* Testing to WP 5.1.1
* PHP error fixes
* Localization issue fixes

= 1.2.4 - 2/Feb/2017 =
* Testing to WP 4.7.2
* Add avatar/gravatar shortcode
* Allow &lt;br /&gt;&lt;p&gt;&lt;strong&gt;&lt;m&gt; html in custom entry
* Tidy code, update readme.txt

= 1.2.3 - 4/Jun/2016 =
* Checks for Wordpress 4.5.2
* Update to caching practices

= 1.2.2 - 11/Dec/2015 =
* Checks for Wordpress 4.4

= 1.2.1 - 7/Nov/2015 =
* unique function added so .recentcomments a CSS can be removed from wp_head

= 1.2.0 - 10/Aug/2015 =
* Shortcodes for custom comment output

= 1.1.0 - 7/Aug/2015 =
* Wordpress 4.3 ready
* ID tag unique identifier for HTML5
* Two additional formats, title/except and excerpt/author

= 1.0.6 - 3/May/2015 =
* ID tag fix to prevent page ID duplicates

= 1.0.5 - 30/Oct/2014 =
* Language files added and l10n code fixes
* Main class fixes

= 1.0.4 - 22/Oct/2014 =
* Tested with Wordpress 4.0

= 1.0.3 - 13/Dec/2013 =
* Tested with Wordpress 3.8
* Helpful screenshot added

= 1.0.2 - 20/Feb/2013 =
* Fixed issue with Post ID not working in some cases

= 1.0.1 - 19/Feb/2013 =
* Added menu option to set excerpt length and trailing character.

= 1.0 - 14/Feb/2013 =
* First version, adapted from "Recent comments widget with comment excerpts," "Wizzart Recent Comments, and "HTML Classified Recent Posts & Comments Widgets"
