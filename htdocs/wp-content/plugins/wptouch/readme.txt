=== Plugin Name ===
Contributors: BraveNewCode, duanestorey, dalemugford
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paypal%40bravenewcode%2ecom&item_name=WPtouch%20Beer%20Fund&no_shipping=0&no_note=1&tax=0&currency_code=CAD&lc=CA&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: wptouch, iphone, ipod, theme, apple, mac, bravenewcode, ajax, mobile, android, blackberry, smartphone
Requires at least: 2.7
Tested up to: 2.9.1
Stable tag: 1.9.7.4

WPtouch automatically transforms your WordPress blog into an iPhone application-style theme, complete with ajax loading articles and effects, when viewed from an iPhone, iPod touch, Android, Opera Mini, Palm Pre and BlackBerry Storm mobile devices.

== Description ==

With a single click, *WPtouch* transforms your WordPress blog into an iPhone application-style theme, complete with ajax loading articles and effects, when viewed from an iPhone, iPod touch, Android or BlackBerry touch mobile device.

The admin panel allows you to customize many aspects of its appearance, and deliver a fast, user-friendly and stylish version of your site to your iPhone, iPod touch, Android, Opera Mini mobile, Palm Pre and BlackBerry Storm visitors without modifying a single bit of code (or affecting) your regular desktop theme.

The theme also includes the ability for your visitors to easily switch between the *WPtouch* view and your site's regular theme.

== Changelog ==

= Version 1.9.7.4 =
* Fixed an issue where thumbnails wouldn't show


= Version 1.9.7.3 =
* Added support for thumbnails using James Lao's 'Simple Post Thumbnails' plugin
* Changed functions.php check from version (2.9) to whether function exists (WP Security Scan bypass)


= Version 1.9.7.2 =
* Added missing code to the theme's functions file for Post thumbs
* Resolves an issue where blank spaces would appear, not Post Thumbs


= Version 1.9.7.1 =
* Fix for broken switch links (missing images) on some installations when in regular theme view
* Added detect for custom field key 'Thumbnail' to work first before using WP 2.9 Thumbs, for those who've been using this method already


= Version 1.9.7 =
* Added Post Thumbnails View option (WordPress 2.9 only) for main post listings
* Added option to show neither post thumbnails nor calendar icons
* Added style option to choose the font of H2 elements on post listings, single entries and pages
* Various style & code improvements and fixes


= Version 1.9.6 =

* Fixed php logic in adsense-new.php (thx JeanPaulH)
* Added support for GigPress' Upcoming Shows to be a drop down in the header menu
* Changed relative comments logic, added function for GMT detection
* Adding padding, size to multipage links
* Removed WP version from footer (security vulnerability)
* Changed admin RSS feed from Support Topics to Twitter Topics
* Updated admin settings image
* Fixed cutoff tweets from WordTwit in the drop-down
* Verified WP 2.9 compatibility


= Version 1.9.5 =

* Added Twitter updates menu item support for WordTwit's new features
* Added CSS for TweetThis and AddThis plugins to be hidden
* Added page/post title to HEAD for Tweet purposes
* Added user agent detection for non-apple mobile devices to be served Twitter link correctly
* Added 180-degree animation to post-arrow dropper and removed excess JS and code
* Added new tab pane for the header (menu, tags, categories, account)
* Added more theme compatibility for other touch devices
* Added 'edit, delete, spam' links for admins on comments
* Changed default OBJECT and EMBED css to only apply to .post, not site-wide
* Changed comment ajax routine
* Changed 'Links' appearance adding support for link categories, removed favicon scripts
* Changed the way the header links are setup
* Small refinements in the theme CSS all-around
* Made progress towards reply and pagination in comments, not there yet


= Versions 1.9.4.x =

* Fixed space appearing below title on single post pages
* Added compressed js for the admin
* Updated Fancybox script & files to v1.2.5
* Updated Ajax Upload script to v3.6
* Fix for re-direct issues introduced with 1.9.3.4
* Fixes and changes for Adsense appearance
* Fix for direct messages not working with WPtouch exclusive mode
* Added spam check for Push direct messages via a nonce
* Added verification for Prowl API key
* Added ability to define custom user agents in the admin (go nuts!)
* Minor admin styling changes
* Dropped official support for WP 2.6 (sorry folks, you should update!)
* Dropped 'skins' references, they'll be a part of WPtouch 2.0
* Compressed style.css in theme for faster load times
* Removed unused files


= Versions 1.9.3.x =

* Fixed issues with adsense ads
* Fixed time since code bug on comments
* Fixed issue where comment count could read as 0 when there were no comments
* Fix for width bug in some situations when switching themes
* Fix for scenarios where a different uploads folder path or name is used
* Minor admin refinements
* Porting CSS styles to a global sheet applicable to all our plugins
* Push messages now come from your blogname not 'wptouch'
* Switch link now re-directs to the page the switch request came from, not back to home
* Changed downloads admin area to support topics rss feed
* Added additional icons in the admin
* Fixed international languages display of time since on comments
* Fixed logic with PUSH notification via Prowl
* Removed unneeded files
* Bug fixes
* Style fixes for the display of comments
* Fixed issue where regular theme view would not allow pinching for zoom
* Fixed JS bug in theme
* Possible fix for time since on comments not working in some situations
* Added option to disable Ajax comments for those who can't get them working


= Versions 1.9.2.x =

* Fixed issue where mobile switch to regular theme was always zoomed in and required refresh
* Fixed issue where pages changed from published to unpublished still showed in the WPtouch menu
* Fixed issue where 'Load More Entries' caused blank page or other issues. WPtouch now detect possible issues and defaults to pagination
* WPtouch now loads minified versions of its CSS and JS for the theme, speeding up load times
* Added check for wp-load.php before attempting ajax comments. Should fix WP comment posting issues. Working on better Disqus and Intense Debate support as well
* Removed 'Find in this page' button, until bugs are resolved
* Now WPtouch will not hide mobileSafari addressbar on single post pages on slower connections
* Fixed bug where WPtouch admin would report that CURL wasn't installed even if it was


= Version 1.9.1 =

* Fixed bug for admin panel Ajax not working in some situations
* Adsense re-enabled
* Fixed bug where SPAM comments were pushed via Prowl
* Push Notification options now announces if Curl is not available, instead of not appearing at all
* Removed text-size adjust option, now replaced with user-adjustable font sizes
* Minor code corrections


= Version 1.9 =

* Fully compatible with WordPress 2.6 - 2.8.x
* Major rewrites of theme files, css for simplicity, CoreTheme
* Adsense has been temporarily disabled until we update code for new Google API changes
* Added experimental support for Opera Mini & Palm Pre mobile browsers
* Added filter trackbacks and pingbacks from display in comment counts
* Added friendly 'noscript' display bezel for users with javascript Turned off
* Added support for Prowl Push notifications for comments, user registrations, and direct messages (if Curl exists on the server)
* Added ability to exclude categories
* Added native select for Tags
* Added theme switch confirm message, saves a cookie to not repeat
* Added jQuery color picker in admin hex areas for easy selections
* Added link to online icon generator in admin
* Added style declaration for images in comments
* Added 'My Account' button in the sub//header for logged in users replacing Logout
* Added theme skin selection support, still no other skins enabled yet
* Added post-options bar on single post page
* Added new background selections
* Updated plugin compatibility listing now loads from bravenewcode.com
* Updated style for comments, working on full support for WP 2.7 comments, Intense Debate
* Updated success message for ajax comments
* Updated font zoom replaced by font-size adjust button
* Updated ajax upload script to v3.1
* Updated fancybox to compressed v1.2.1
* Updated Ajax Upload script to 3.2
* Updated compatibility code (Various WordPress install scenarios) ~ thanks to Will Norris for the suggestions
* Updated a check if comments are open before showing the comments link
* Updated local jquery in exclusive mode to use WP, not Google
* Updated admin style and design
* Updated search now floats overtop the headerbar
* Updated the_content_rss() for excerpts, created a custom function which handles it nicely
* Updated several images in the core images folder, building more dependency on CoreTheme
* Removed support for WordPress 2.3, lowest known WordPress version supported: 2.6
* Removed 404 image with English text in it, replaced it with localized 404 text
* Removed depreciated or unused functions from previous releases
* Removed ability to disable jQuery; WPtouch Exclusive mode should fix JS issues
* Fixed home link in menu drop down now respects the logo/bookmark icon choice
* Fixed WP Spam Free users having had new comments blocked
* Fixed WPtouch appearing zoomed out and wide
* Fixed custom page icons not showing up on pages
* Fixed some domains not showing the beta download/news areas
* Fixed style issue for icons on pages
* Fixed the way javascript is called for a elements, should work better in other mobile browsers
* Fixed switch link issue where regular theme switch link was broken
* Fixed issue where chosen pages and icons did not appear in the drop down
* Fixed a variety of scenarios where paths to files and images were broken
* Fixed a few areas that had text not yet localized, improperly coded
* More preparation for languages support
* Preparation for WPtouch 2.0 and themes support (based on CoreTheme)
* Other minor optimizations, fixes, changes


= Version 1.8.9.1 =

* Fixed refresh issue (some pages keep re-loading)
* Fixed mkdir issue on PHP4 installations
* Set viewport to fixed width for device to prevent some sites from loading wide
* Minor revisions to new CSS calendar icon styling
* Added exclusive mode feature to help in situations where other plugins are incompatible, load too many scripts/css files and both break and slow down WPtouch
* Added Fancybox for some feature descriptions in the admin


= Versions 1.8.x =

* Changed calendar icons from images to CSS-based only (they look sexay!)
* Refined styling of header logo, text shadow, general appearance
* Removed unneeded declarations from the WPtouch stylesheet
* Tested and works efficiently with WordPress MU when installed site-wide (Finally!)
* Disqus commenting plugins out-of-the-box styling enhancements
* Changed post nav on the single post page to prev/next post, instead of entry titles for length's sake
* Fixed bug related to RSS feeds being broken in some situations
* Fixed fatal error on line 153 undefined 'is_front_page' function for WP 2.3.x users
* Fixed jQuery failing to load for WP 2.3.x users
* Added option for font-zoom on rotate for accessibility, on by default
* Fixed various styling bugs
* Changed switch link in WPtouch to remain fixed width
* Fixed various content overflow issues in WPtouch theme files
* As a note for WordPress 2.3 users, WPtouch 1.9 will require WordPress 2.5+
* Fixed new switch link to work under different WordPress install scenarios
* Fixed switch link CSS style-sheet loading issues in some situations
* Fixed missing mime types for icon upload through IE7
* Fixed issues related to automatic favicon generation on a Links page
* Changed footer switch links to mimic iPhone settings app appearance
* Fixed misc scenarios for ajax-upload errors
* Fixed path issues related to custom icons (sites on windows servers)
* Fixed issues related to ajax comments not working in some situations
* Added check for 'Allow Comments' on pages
* Fixed Apache error (reported in logs)
* Fixed admin styling issues on IE7, Firefox
* Fixed issue with custom icons and the header logo
* Fixed issue with the Classic background not appearing
* Significant rewrite of core code for increased efficiency
* Changed database calls to use wpdb object, will hopefully work with wpmu
* Internationalization preparation of the admin and theme files (for WPtouch 1.9)
* Added ability to add/delete custom icons that survive WPtouch & WordPress upgrades
* Added ability to select left/full text justification, 3 font sizes
* Changed how WPtouch admin panel shows icons, more room for custom icons
* Added channel capability for Adsense
* Now suppresses banners created by the Ribbon Manger Plugin
* Minor tweaks to login, register, admin links, footer appearance
* Minor tweaks to drop down menus, header styling
* More refinements for search, categories & tag pages, 'load more' link
* Text & code refinements in the WPtouch admin
* Experimental support for the Blackberry Storm
* Fixed issue with WPtouch header title display issue
* Fixed issue related to login/logout/admin/register link path issues
* Fixed issue where Bookmarks link when Advanced JS is turned off
* Fixed issue with default icon case
* Fixed issue with switch code on systems with PHP4
* Fixed issue related to fresh installs
* Fixed issue with Android and the sub-header menu links not working


= Versions 1.7.x =

* Added option to do GZIP compression
* Suppressed warning about multiple gzhandlers
* Fixed user agent detection code
* Added ability to choose if WPtouch or regular version of your site is shown first 
* Fixed WP login/out button bugs
* Added login/out auto-detect for WP 2.7 or pre-WP 2.7 sites
* Fixed loading path issue that caused drop-down menu button to fail
* Added choice between alphabetical or page order sorting of the drop down menu
* Added clock icon
* Fixes for categories drop-down menu (now shows post #'s)
* Minor fix for categories drop-down menu
* Automatic detection & support for Peter's anti-spam plugin
* Built-in support for Adsense in posts
* Moved Stats tracking box beside Advertising Options
* Better WordPress version support detection
* More refined image auto-sizing with WP added images & galleries in posts / pages
* Fix for WordPress shortcodes appearing in excerpts
* Changed how WPtouch shows switch links
* Auto-adjusting width/height for MobileSafari plugin objects (YouTube, Quicktime)


= Versions 1.6.x =

* Auto-resizing images in posts/pages on orientation change!
* Auto-resizing WP image galleries
* Better support for captions on images, gallery items
* Added the ability to enable a quick login button w/ drop-down in the WPtouch header
* Added the ability to enable categories as a drop-down item in the WPtouch header
* Added the ability to disable WPtouch automatic homepage redirection (resolves white page issue)
* Added the ability to manually select a re-direct landing page
* Refinements in WPtouch admin
* Enhanced support for WordPress 2.7 admin
* Re-designed post comment bubble icon
* Input box to inject custom code (Google Analytics, MINT, etc) into WPtouch
* Basic support for Incognito & WebMate browsers on iPhone & iPod touch
* Other code fixes, cleanups & optimizations
* Other theme style cleanups and enhancements


= Versions 1.5.x =

* Added support for WordPress image galleries
* Added support for single post page split navigation
* Fixed admin footer links which did not locate WordPress install correctly
* Added basic Google Android support
* Changes in WPtouch admin appearance and styling
* Added donate message in WPtouch admin
* WPtouch now supports WordPress 2.3 or higher


= Versions 1.4.x =

* More jQuery tune-ups, now loads through wp_enqueue_script() or Google to prevent collisions
* Changed $J to $wptouch to prevent collisions using jQuery
* Offloaded jQuery loading from our folder to Google instead for WP > 2.5 sites
* Fixed a bug in wptouch.php on line 232, fixing drop-down menu display issue
* Fixed a bug where blank admin options were allowed instead of refused
* Fixed a bug with overriding the site title in the WPtouch admin
* Fixed some instances where ajax comments would not work
* Fixed a bug where the loading of javascript files would load in your site's default theme
* Enhanced drop-down menu appearance
* More compatibility with other plugins
* Code cleanups and optimizations


= Versions 1.3.x =

* Tweaks for the jQuery bugs
* No conflict setting added for jQuery
* Support for DISQUS 2.0.2-x Plugin
* Minor style edits and enhancements for the search dropdown
* Another fix for drop-down Menu not working
* Added ability to change the header border background color
* Fix for slashes appearing before apostrophes in the header title
* Admin wording changes, styling changes
* Minor style enhancements to the theme
* Fix for Menu not working on some installations
* Style enhancements for the menu, search, drop downs
* Style enhancements for comments, logged in users
* Font adjustments for titles
* Style changes for single post page heading, for better clarity
* Admin wording changes


= Versions 1.2.x =

* Fix for the theme appearing in Safari browsers
* Switch from Prototype to the more WordPress-native jQuery for javascript (much faster!)
* Fix for wrong re-directs happening unintentionally if you use a static home page
* Elimination of unneeded images, javascript (shaving more than 100KB!)
* More template file cleanups, image & code optimizations
* The addition of more comments in code templates to help you make custom modifications
* Option to enable comments on pages
* Option to manually enter in a new blog title (fixes cases where the blog title runs the length of the header and wraps)
* Option to hide/show excerpts by default on the home, search, and archive pages
* Switch code links are automatically injected into your regular theme's footer now, and is only seen on the iPhone/ipod touch
* In all, despite the addition of new features we've cut load times for WPtouch in half with this release over 1.2.x releases!
* The ability to disable Gravatars in comments (more control over optimization & speed)
* Redundant, unused template file cleanups (archive.php, search.php & page.php are now all just index.php)
* More style enhancements and tweaks, fixes
* Switched to Snoopy from CURL for the admin news section (thanks to Joost de Valk (yoast.com)


= Version 1.1 =

* The ability to disable advanced javascript effects (fixes effects not working for some, speeds up the load time considerably)
* Proper styling of embedded YouTube videos on mobileSafari in iPhone 2.0
* Fix for the switch code not working on some blog installations
* Redundant, unused code cleanups
* More style enhancements and tweaks, fixes
* the ability to enable/disable the default home, rss and email menu items
* support for WordPress installations that have static home pages
* dynamic WPtouch news in the administration panel
* the ability to modify the default hyperlink color
* major CSS & PHP cleaning, resulting in reduced size and faster load times
* the ability to enable/disable tags, categories and author names on the index, search and author pages
* support for DISQUS commenting
* CSS refinements for comments, the drop-down menu, and overall appearance
* styling for YouTube embedded videos
* bug fixes for blogs installed in directories other than root


= Version 1.0 = 

* Initial release


== Installation ==

= 2.6 and Older =
Sorry, we do not officially support installations on WordPress 2.6 or older. You can use WPtouch 1.9.3.4 or older on these installations, however.

= 2.7, 2.8+ =
You can install *WPtouch* directly from the WordPress admin! Visit the *Plugins/Add New* page and search for 'WPtouch'. Click to install.

Once installed and activated visit the WPtouch admin page (*Settings/WPtouch*) to customize your WPtouch appearance.

= WordPress MU =
If you'd like to use *WPtouch* with WordPress MU as a site-wide plugin, simple install the wptouch folder in the mu-plugins directory.  Once complete, either move wptouch.php back a directory (into the mu-plugins directory), or create a symbolic link to it.


Please visit http://www.bravenewcode.com/wptouch/ for comprehensive installation instructions.

You can also checkout our Support Forums at http://support.bravenewcode.com to post questions and learn tips and tricks for *WPtouch* and our other plugins.


== Frequently Asked Questions ==

= I thought the iPhone/iPod touch/Pre/Storm/Android shows my website fine the way it is now? =

Yes, that's true for the most part. However, not all websites are created equal, with some sites failing to translate well in the viewport of a small mobile device. Many WordPress sites today make heavy use of different javascript files which significantly increase the load time of pages, and drive your visitors on 3G/EDGE batty. So we've come up with *WPtouch*, a lightweight, fast-loading, feature-rich and highly-customized "theme application" which includes an admin interface to let you customize many aspects of your site's presentation.

= Well, what if my users don't like it and want to see my regular site? =

There's a mobile switch option in the footer on *WPtouch* for your users with browsers that support cookies to easily switch between the *WPtouch* view and your site's regular appearance. It's that easy. We even automatically put a little snippet of code into your current theme which will be shown only to iPhone, iPod touch, Android or BlackBerry touch mobile device visitors, giving them control to switch between the two site themes easily.

= Will it slow down my blog, or increase my server load? =

Not bloody likely! Unless of course you're getting slammed with all sorts of traffic because you've installed this sexy plugin. The entire theme files footprint for *WPtouch* is small. It was designed to be as lightweight and speedy as possible, while still serving your site's content in a richly presented way, sparing no essential features like search, login, categories, tags, comments etc.

== Screenshots ==

1. Posts on the front page
2. Post on the front page (w/ Post Thumbnails)
3. Drop down menu navigation
4. Push Messaging
5. WordTwit plugin Twitter integration
6. Single post page post meta, options bar, comments
7. Ajax comment form
8. Switch link in the footer
9. Archives page appearance (auto-generated if you have a page called 'Archives')
10. Sample regular page
