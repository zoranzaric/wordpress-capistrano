=== Clicky ===
Contributors: joostdevalk
Donate link: http://yoast.com/
Tags: analytics, statistics, clicky
Requires at least: 2.8
Tested up to: 2.9
Stable tag: 1.0.6

Integrates the Clicky web analytics service and optionally the Clicky.me short URL service into your blog.

== Description ==

Integrates the Clicky web analytics service and optionally the Clicky.me short URL service into your blog.

* Automatically adding your Clicky tracking code everywhere.
* Option to ignore admins.
* Option to store names of commenters.
* Option to track posts & pages as goals and assign a revenue to that page or post.
* An overview of your site's statistics on your dashboard.
* Integration with the [Clicky.me Short URL service](http://clicky.me/).
* Automatically create a short link for each now post and page.
* Option to automatically tweet posts and pages on publish (either for each and every one or on a post by post basis), with the message being a prefix of your choice, the post title and then the short URL.

== Installation ==

1. Upload the `clicky` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Enter your Site ID, Key and Admin Key, and if needed your Twitter username and password.

== Screenshots ==

1. The Clicky WordPress plugin settings panel.

== Changelog ==

= 1.0.6 =
* Auto-tweeting now only happens when a post is first published. 
* Made sure there are no spaces in site ID, site key and admin site key are always trimmed.
* Added extra check to make sure clicky.me returned a valid short URL before tweeting.

= 1.0.5 =
* Minor copy changes.

= 1.0.4 =
* Made sure there's no spaces in the Site ID when displaying it, should solve blank Dashboard Stats Page issue.

= 1.0.3 =
* Made all strings localizable (is that even a word).
* Added .pot file to allow localization.
* Added a Dutch translation.

= 1.0.2 =
* Added option to auto tweet articles, removing the checkbox from the add post screen. This makes sure auto tweet works when you're posting from within an external editor.

= 1.0.1 =
* Added prefix option for Tweets that are sent out on publish.

= 1.0 =
* Initial release.