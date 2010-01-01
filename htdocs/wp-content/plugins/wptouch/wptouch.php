<?php
/*
   Plugin Name: WPtouch iPhone Theme
   Plugin URI: http://bravenewcode.com/wptouch
   Description: A plugin which formats your site with a mobile theme for the Apple <a href="http://www.apple.com/iphone/">iPhone</a> / <a href="http://www.apple.com/ipodtouch/">iPod touch</a>, <a href="http://www.android.com/">Google Android</a> and other touch-based smartphones.
	Author: Dale Mugford & Duane Storey (BraveNewCode)
	Version: 1.9.7.4
	Author URI: http://www.bravenewcode.com
   
	# Thanks to ContentRobot and the iWPhone theme/plugin
	# which the detection feature of the plugin was based on.
	# (http://iwphone.contentrobot.com/)
	
	# Also thanks to Henrik Urlund, who's "Prowl Me" plugin inspired
	# the Push notification additions.
	# (http://codework.dk/referencer/wp-plugins/prowl-me/)
	
	# All Admin and theme design / CSS is Copyright (c) 2007-2010
	# Duane Storey & Dale Mugford of BraveNewCode Inc.
	#
	# 'WPtouch' is an unregistered trademark of BraveNewCode Inc., 
	# and may not be used in conjuction with this software without permission.
	
	# The code in this plugin is free software; you can redistribute the code aspects of
	# the plugin and/or modify the code under the terms of the GNU Lesser General
	# Public License as published by the Free Software Foundation; either
	# version 2.1 of the License, or (at your option) any later version.
	
	# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	# EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	# MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
	# NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
	# LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
	# OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
	# WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE. 
	#
	# See the GNU lesser General Public License for more details.
*/


global $bnc_wptouch_version;
$bnc_wptouch_version = '1.9.7.4';

require_once( 'include/plugin.php' );
require_once( 'include/compat.php' );

define( 'WPTOUCH_PROWL_APPNAME', 'WPtouch');

function wp_touch_get_comment_count() {
	global $wpdb;
	global $post;
	
	$result = $wpdb->get_row( $wpdb->prepare( "SELECT count(*) as c FROM {$wpdb->comments} WHERE comment_type = '' AND comment_approved = 1 AND comment_post_ID = %d", $post->ID ) );
	if ( $result ) {
		return $result->c;
	} else {
		return 0;	
	}
}


//The WPtouch Settings Defaults
global $wptouch_defaults;
$wptouch_defaults = array(
	'header-title' => get_bloginfo('name'),
	'main_title' => 'Default.png',
	'enable-post-excerpts' => true,
	'enable-page-coms' => false,
	'enable-cats-button' => true,
	'enable-tags-button' => true,
	'enable-search-button' => true,
	'enable-login-button' => false,
	'enable-ajax-comments' => true,
	'enable-gravatars' => true,
	'enable-main-home' => true,
	'enable-main-rss' => true,
	'enable-main-name' => true,
	'enable-main-tags' => true,
	'enable-main-categories' => true,
	'enable-main-email' => true,
// Prowl
	'prowl-api' => '',
	'enable-prowl-comments-button' => false,
	'enable-prowl-users-button' => false,
	'enable-prowl-message-button' => false,
//
	'header-background-color' => '000000',
	'header-border-color' => '333333',
	'header-text-color' => 'eeeeee',
	'link-color' => '006bb3',
// New
	'post-cal-thumb' =>'calendar-icons',
	'h2-font' =>'Helvetica Neue',
//
	'style-text-justify' => 'full-justified',
	'style-background' => 'classic-wptouch-bg',
	'enable-regular-default' => false,
	'excluded-cat-ids' => '',
	'home-page' => 0,
	'enable-exclusive' => false,
	'sort-order' => 'name',
	'adsense-id' => '',
	'statistics' => '',
	'adsense-channel' => '',
	'custom-user-agents' => array(),
	'enable-show-tweets' => false,
	'enable-gigpress-button' => false
);

function wptouch_get_plugin_dir_name() {
	global $wptouch_plugin_dir_name;
	return $wptouch_plugin_dir_name;
}

function wptouch_delete_icon( $icon ) {
	if ( !current_user_can( 'upload_files' ) ) {
		// don't allow users to delete who don't have access to upload (security feature)
		return;	
	}
			
	$dir = explode( 'wptouch', $icon );
	$loc = compat_get_upload_dir() . "/wptouch/" . ltrim( $dir[1], '/' );

	unlink( $loc );
}

function wptouch_init() {	
	if ( isset( $_GET['delete_icon'] ) ) {
		wptouch_delete_icon( $_GET['delete_icon'] );
		header( 'Location: ' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=wptouch/wptouch.php#available_icons' );
		die;
	}	

}

function wptouch_include_adsense() {
	global $wptouch_plugin;
	$settings = bnc_wptouch_get_settings();
	if ( bnc_is_iphone() && $wptouch_plugin->desired_view == 'mobile' && isset( $settings['adsense-id'] ) && strlen( $settings['adsense-id'] ) && is_single() ) {
		global $wptouch_settings;
		$wptouch_settings = $settings;
		
		include( 'include/adsense-new.php' );
	}
}

function wptouch_content_filter( $content ) {
	global $wptouch_plugin;
	$settings = bnc_wptouch_get_settings();
	if ( bnc_is_iphone() && $wptouch_plugin->desired_view == 'mobile' && isset($settings['adsense-id']) && strlen($settings['adsense-id']) && is_single() ) {
		global $wptouch_settings;
		$wptouch_settings = $settings;
		
		ob_start();
		include( 'include/adsense-new.php' );
		$ad_contents = ob_get_contents();
		ob_end_clean();
		
		return  '<div class="wptouch-adsense-ad">' . $ad_contents . '</div>' . $content;	
	} else {
		return $content;
	}
}

	add_filter('init', 'wptouch_init');

	function WPtouch($before = '', $after = '') {
		global $bnc_wptouch_version;
		echo $before . 'WPtouch ' . $bnc_wptouch_version . $after;
	}

//Add a link to settings on the plugin listings page
function wptouch_settings_link( $links, $file ) {
 	if( $file == 'wptouch/wptouch.php' && function_exists( "admin_url" ) ) {
		$settings_link = '<a href="' . admin_url( 'options-general.php?page=wptouch/wptouch.php' ) . '">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link ); // before other links
	}
	return $links;
}
 
// WP Admin stylesheets & javascript
function wptouch_admin_files() {		
	if ( isset( $_GET['page'] ) && $_GET['page'] == 'wptouch/wptouch.php' ) {
		echo "<link rel='stylesheet' type='text/css' href='" . compat_get_plugin_url( 'wptouch' ) . "/admin-css/wptouch-admin.css' />\n";
		echo "<link rel='stylesheet' type='text/css' href='" . compat_get_plugin_url( 'wptouch' ) . "/admin-css/bnc-global.css' />\n";
		echo "<link rel='stylesheet' type='text/css' href='" . compat_get_plugin_url( 'wptouch' ) . "/admin-css/bnc-compressed-global.css' />\n";
		echo "<script type='text/javascript' src='" . compat_get_plugin_url( 'wptouch' ) . "/js/ajax_upload_3.6.js'></script>\n";
		echo "<script type='text/javascript' src='" . compat_get_plugin_url( 'wptouch' ) . "/js/colorpicker_1.4.js'></script>\n";
		echo "<script type='text/javascript' src='" . compat_get_plugin_url( 'wptouch' ) . "/js/fancybox_1.2.5.js'></script>\n";
		echo "<script type='text/javascript' src='" . compat_get_plugin_url( 'wptouch' ) . "/js/admin_1.9.js'></script>\n";
	}
}

function bnc_wptouch_get_user_agents() {
	$useragents = array(		
		"iphone",  				 // Apple iPhone
		"ipod", 					 // Apple iPod touch
		"aspen", 				 // iPhone simulator
		"dream", 				 // Pre 1.5 Android
		"android", 			 // 1.5+ Android
		"cupcake", 			 // 1.5+ Android
		"blackberry9500",	 // Storm
		"blackberry9530",	 // Storm
		"opera mini", 		 // Experimental
		"webos",				 // Experimental
		"incognito", 			 // Other iPhone browser
		"webmate" 			 // Other iPhone browser
	);
	
	$settings = bnc_wptouch_get_settings();
	if ( isset( $settings['custom-user-agents'] ) ) {
		foreach( $settings['custom-user-agents'] as $agent ) {
			if ( !strlen( $agent ) ) continue;
			
			$useragents[] = $agent;	
		}	
	}
	
	asort( $useragents );

	// WPtouch User Agent Filter
	$useragents = apply_filters( 'wptouch_user_agents', $useragents );
	
	return $useragents;
}

function bnc_wptouch_is_prowl_key_valid() {
	require_once( 'include/class.prowl.php' );		
		
	$settings = bnc_wptouch_get_settings();
				
	if ( isset( $settings['prowl-api'] ) ) {
		$api_key = $settings['prowl-api'];
			
		$prowl = new Prowl( $api_key, $settings['header-title'] );	
		$verify = $prowl->verify();
		return ( $verify === true );
	}
	
	return false;
}
  
class WPtouchPlugin {
	var $applemobile;
	var $desired_view;
	var $output_started;
	var $prowl_output;
	var $prowl_success;
		
	function WPtouchPlugin() {
		$this->output_started = false;
		$this->applemobile = false;
		$this->prowl_output = false;
		$this->prowl_success = false;

		add_action( 'plugins_loaded', array(&$this, 'detectAppleMobile') );
		add_filter( 'stylesheet', array(&$this, 'get_stylesheet') );
		add_filter( 'theme_root', array(&$this, 'theme_root') );
		add_filter( 'theme_root_uri', array(&$this, 'theme_root_uri') );
		add_filter( 'template', array(&$this, 'get_template') );
		add_filter( 'init', array(&$this, 'bnc_filter_iphone') );
		add_filter( 'wp', array(&$this, 'bnc_do_redirect') );
		add_filter( 'wp_head', array(&$this, 'bnc_head') );
		add_filter( 'query_vars', array( &$this, 'wptouch_query_vars' ) );
		add_filter( 'parse_request', array( &$this, 'wptouch_parse_request' ) );
		add_action( 'comment_post', array( &$this, 'wptouch_handle_new_comment' ) );
		add_action( 'user_register', array( &$this, 'wptouch_handle_new_user' ) );
		
		$this->detectAppleMobile();
	}
	
	function wptouch_cleanup_growl( $msg ) {
		$msg = str_replace("\r\n","\n", $msg);
		$msg = str_replace("\r","\n", $msg);
		return $msg;	
	}
	
	function wptouch_send_prowl_message( $title, $message ) {
		require_once( 'include/class.prowl.php' );		
		
		$settings = bnc_wptouch_get_settings();
				
		if ( isset( $settings['prowl-api'] ) ) {
			$api_key = $settings['prowl-api'];
			
			$prowl = new Prowl( $api_key, $settings['header-title'] );
				
			$this->prowl_output = true;
			$result = $prowl->add( 	1, $title, $this->wptouch_cleanup_growl( stripslashes( $message ) ) );	
			
			if ( $result ) {
				$this->prowl_success = true;
			} else {				
				$this->prowl_success = false;
			}		
		} else {
			return false;	
		}
	}
	
	function wptouch_handle_new_comment( $comment_id, $approval_status = '1' ) {
		$settings = bnc_wptouch_get_settings();
		
		if ( $approval_status != 'spam' 
		&& isset( $settings['prowl-api'] ) 
		&& isset( $settings['enable-prowl-comments-button'])
		&& $settings['enable-prowl-comments-button'] == 1 ) {
			
			$api_key = $settings['prowl-api'];
			
			require_once( 'include/class.prowl.php' );
			$comment = get_comment( $comment_id );
			$prowl = new Prowl( $api_key, $settings['header-title'] );
			
			if ( $comment->comment_type != 'spam' && $comment->comment_approved != 'spam' ) {
				if ( $comment->comment_type == 'trackback' || $comment->comment_type == 'pingback' ) {
					$result = $prowl->add( 	1, 
						__( "New Ping/Trackback", "wptouch" ),
						'From: '. $this->wptouch_cleanup_growl( stripslashes( $comment->comment_author ) ) . 
						"\nPost: ". $this->wptouch_cleanup_growl( stripslashes( $comment->comment_content ) ) 
					);			
			 	} else {
					$result = $prowl->add( 	1, 
						__( "New Comment", "wptouch" ),
						'Name: '. $this->wptouch_cleanup_growl( stripslashes( $comment->comment_author ) ) . 
						"\nE-Mail: ". $this->wptouch_cleanup_growl( stripslashes( $comment->comment_author_email ) ) .
						"\nComment: ". $this->wptouch_cleanup_growl( stripslashes( $comment->comment_content ) )
					);		 
			 	}
			}
		 }

	}
	

	function wptouch_handle_new_user( $user_id ) {
		$settings = bnc_wptouch_get_settings();
		
		if ( isset( $settings['prowl-api'] ) 
		&& isset( $settings['enable-prowl-users-button'] ) 
		&& $settings['enable-prowl-users-button'] == 1 ) {

			global $wpdb;			
			$api_key = $settings['prowl-api'];
			require_once( 'include/class.prowl.php' );
			global $table_prefix;
			$sql = $wpdb->prepare( "SELECT * from " . $table_prefix . "users WHERE ID = %d", $user_id );
			$user = $wpdb->get_row( $sql );
			
			if ( $user ) {
				$prowl = new Prowl( $api_key, $settings['header-title'] );	
				$result = $prowl->add( 	1, 
					__( "User Registration", "wptouch" ),
					'Name: '. $this->wptouch_cleanup_growl( stripslashes( $user->user_login ) ) . 
					"\nE-Mail: ". $this->wptouch_cleanup_growl( stripslashes( $user->user_email ) )
				);			
			}
		}
	}

	function wptouch_query_vars( $vars ) {
		$vars[] = "wptouch";
		return $vars;
	}

	function wptouch_parse_request( $wp ) {
		if  (array_key_exists( "wptouch", $wp->query_vars ) ) {
			switch ( $wp->query_vars["wptouch"] ) {
				case "upload":
					include( 'ajax/file_upload.php' );	
					break;
				case "plugins":
					include( 'ajax/load-plugins.php' );
					break;
			}
			exit;
		}	
	}

	function bnc_head() {
		if ( $this->applemobile && $this->desired_view == 'normal' ) {
			echo "<link rel='stylesheet' type='text/css' href='" . compat_get_plugin_url( 'wptouch' ) . "/themes/core/core-css/wptouch-switch-link.css'></link>\n";
			echo "<meta name=\"viewport\" content=\"width=device-width,initial-scale=0,user-scalable=yes\" /> \n";
		}		
	}

	function bnc_do_redirect() {
	   global $post;
				
		// check for wptouch prowl direct messages	
		$nonce = '';
		if ( isset( $_POST['_nonce'] ) ) {
			$nonce = $_POST['_nonce'];	
		}
			
		if ( isset( $_POST['wptouch-prowl-message'] ) && wp_verify_nonce( $nonce, 'wptouch-prowl' )  ) {
			$name = $_POST['prowl-msg-name'];
			$email = $_POST['prowl-msg-email'];
			$msg = $_POST['prowl-msg-message'];
			
			$title = __( "Direct Message", "wptouch" );
			$prowl_message = 'From: '. $this->wptouch_cleanup_growl( $name ) . 
				"\nE-Mail: ". $this->wptouch_cleanup_growl( $email ) .
				"\nMessage: ". $this->wptouch_cleanup_growl( $msg );
				"\nIP: " . $_SERVER["REMOTE_ADDR"] .
				
			$this->wptouch_send_prowl_message( $title, $prowl_message );
		}		   
	   
	   if ( $this->applemobile && $this->desired_view == 'mobile' ) {
			$version = (float)get_bloginfo('version');
			$is_front = 0;
			$is_front = (is_front_page() && (bnc_get_selected_home_page() > 0));

			if ( $is_front ) {
	    	     $url = get_permalink( bnc_get_selected_home_page() );
	        	 header('Location: ' . $url);
	         	die;
	   	     }
	   }
	}

	function bnc_filter_iphone() {				
		$key = 'wptouch_switch_cookie';
		
	   if (isset($_GET['theme_view'])) {
	  		if ($_GET['theme_view'] == 'mobile') {
				setcookie($key, 'mobile', 0); 
			} elseif ($_GET['theme_view'] == 'normal') {
				setcookie($key, 'normal', 0);
			}
			
			$redirect_location = get_bloginfo( 'siteurl' );
// fix by cybrstudd
			if ( isset( $_GET['wptouch_redirect'] ) ) {
				$protocol = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
				$redirect_location = $protocol . $_GET['wptouch_redirect'];
			}
			
			header( 'Location: ' . $redirect_location );
			die;
		}

		$settings = bnc_wptouch_get_settings();
		if (isset($_COOKIE[$key])) {
			$this->desired_view = $_COOKIE[$key];
		} else {
			if ( $settings['enable-regular-default'] ) {
				$this->desired_view = 'normal';
			} else {
		  		$this->desired_view = 'mobile';
			}
		}		
	}
	
	function detectAppleMobile($query = '') {
		$container = $_SERVER['HTTP_USER_AGENT'];
		// The below prints out the user agent array. Uncomment to see it shown on the page.
		// print_r($container); 
		// Add whatever user agents you want here to the array if you want to make this show on another device.
		// No guarantees it'll look pretty, though!
		$useragents = bnc_wptouch_get_user_agents();

		$devfile =  compat_get_plugin_dir( 'wptouch' ) . '/include/developer.mode';
		$this->applemobile = false;
		foreach ( $useragents as $useragent ) {
			if ( eregi( $useragent, $container ) || file_exists($devfile) ) {
				$this->applemobile = true;
			} 	
		}
	}

	function get_stylesheet( $stylesheet ) {
		if ($this->applemobile && $this->desired_view == 'mobile') {
			return 'default';
		} else {
			return $stylesheet;
		}
	}
		  
	function get_template( $template ) {
		$this->bnc_filter_iphone();
		if ($this->applemobile && $this->desired_view === 'mobile') {
			return 'default';
		} else {	   
			return $template;
		}
	}
		  
	function get_template_directory( $value ) {
		$theme_root = compat_get_plugin_dir( 'wptouch' );
		if ($this->applemobile && $this->desired_view === 'mobile') {
				return $theme_root . '/themes';
		} else {
				return $value;
		}
	}
		  
	function theme_root( $path ) {
		$theme_root = compat_get_plugin_dir( 'wptouch' );
		if ($this->applemobile && $this->desired_view === 'mobile') {
			return $theme_root . '/themes';
		} else {
			return $path;
		}
	}
		  
	function theme_root_uri( $url ) {
		if ($this->applemobile && $this->desired_view === 'mobile') {
			$dir = compat_get_plugin_url( 'wptouch' ) . "/themes";
			return $dir;
		} else {
			return $url;
		}
	}
}
  
global $wptouch_plugin;
$wptouch_plugin = new WPtouchPlugin();

//Thanks to edyoshi:
function bnc_is_iphone() {
	global $wptouch_plugin;
	$wptouch_plugin->bnc_filter_iphone();
	return $wptouch_plugin->applemobile;
}
  
// The Automatic Footer Template Switch Code (into "wp_footer()" in regular theme's footer.php)
function wptouch_switch() {
	global $wptouch_plugin;
	if ( bnc_is_iphone() && $wptouch_plugin->desired_view == 'normal' ) {
		echo '<div id="wptouch-switch-link">';
		_e( "Mobile Theme", "wptouch" ); 
		echo "<a onclick=\"document.getElementById('switch-on').style.display='block';document.getElementById('switch-off').style.display='none';\" href=\"" . get_bloginfo('siteurl') . "/?theme_view=mobile&wptouch_redirect=" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . "\"><img id=\"switch-on\" src=\"" . compat_get_plugin_url( 'wptouch' ) . "/themes/core/core-images/on.jpg\" alt=\"on switch image\" class=\"wptouch-switch-image\" style=\"display:none\" /><img id=\"switch-off\" src=\"" . compat_get_plugin_url( 'wptouch' ) .  "/themes/core/core-images/off.jpg\" alt=\"off switch image\" class=\"wptouch-switch-image\" /></a>";
 		echo '</div>';
	}
}
  
function bnc_options_menu() {
	add_options_page( __( 'WPtouch iPhone Theme', 'wptouch' ), 'WPtouch', 9, __FILE__, bnc_wp_touch_page );
}

function bnc_wptouch_get_settings() {
	return bnc_wp_touch_get_menu_pages();
}

function bnc_validate_wptouch_settings( &$settings ) {
	global $wptouch_defaults;
	foreach ( $wptouch_defaults as $key => $value ) {
		if ( !isset( $settings[$key] ) ) {
			$settings[$key] = $value;
		}
	}
}

function bnc_wptouch_is_exclusive() {
	$settings = bnc_wptouch_get_settings();
	return $settings['enable-exclusive'];
}

function bnc_can_show_tweets() {
	$settings = bnc_wptouch_get_settings();
	return $settings['enable-show-tweets'];
}

function bnc_wp_touch_get_menu_pages() {
	$v = get_option('bnc_iphone_pages');
	if (!$v) {
		$v = array();
	}
	
	if (!is_array($v)) {
		$v = unserialize($v);
	}
	
	bnc_validate_wptouch_settings( $v );

	return $v;
}

function bnc_get_selected_home_page() {
   $v = bnc_wp_touch_get_menu_pages();
   return $v['home-page'];
}

function wptouch_get_stats() {
	$options = bnc_wp_touch_get_menu_pages();
	if (isset($options['statistics'])) {
		echo stripslashes($options['statistics']);
	}
}
  
function bnc_get_title_image() {
	$ids = bnc_wp_touch_get_menu_pages();
	$title_image = $ids['main_title'];

	if ( file_exists( compat_get_plugin_dir( 'wptouch' ) . '/images/icon-pool/' . $title_image ) ) {
		$image = compat_get_plugin_url( 'wptouch' ) . '/images/icon-pool/' . $title_image;
	} else if ( file_exists( compat_get_upload_dir() . '/wptouch/custom-icons/' . $title_image ) ) {
		$image = compat_get_upload_url() . '/wptouch/custom-icons/' . $title_image;
	}

	return $image;
}

function wptouch_excluded_cats() {
	$settings = bnc_wptouch_get_settings();
	return stripslashes($settings['excluded-cat-ids']);
}

function bnc_excerpt_enabled() {
	$ids = bnc_wp_touch_get_menu_pages();
	return $ids['enable-post-excerpts'];
}	

function bnc_is_page_coms_enabled() {
	$ids = bnc_wp_touch_get_menu_pages();
	return $ids['enable-page-coms'];
}		

function bnc_is_cats_button_enabled() {
	$ids = bnc_wp_touch_get_menu_pages();
	return $ids['enable-cats-button'];
}	

function bnc_is_tags_button_enabled() {
	$ids = bnc_wp_touch_get_menu_pages();
	return $ids['enable-tags-button'];
}	

function bnc_is_search_enabled() {
	$ids = bnc_wp_touch_get_menu_pages();
	return $ids['enable-search-button'];
}	

function bnc_is_gigpress_enabled() {
	$ids = bnc_wp_touch_get_menu_pages();
	return $ids['enable-gigpress-button'];
}	

function bnc_is_login_button_enabled() {
	$ids = bnc_wp_touch_get_menu_pages();
	return $ids['enable-login-button'];
}		
	
function bnc_is_gravatars_enabled() {
	$ids = bnc_wp_touch_get_menu_pages();
	return $ids['enable-gravatars'];
}	

function bnc_is_ajax_coms_enabled() {
	$ids = bnc_wp_touch_get_menu_pages();
	return $ids['enable-ajax-comments'];
}	

function bnc_show_author() {
	$ids = bnc_wp_touch_get_menu_pages();
	return $ids['enable-main-name'];
}

function bnc_show_tags() {
	$ids = bnc_wp_touch_get_menu_pages();
	return $ids['enable-main-tags'];
}

function bnc_show_categories() {
	$ids = bnc_wp_touch_get_menu_pages();
	return $ids['enable-main-categories'];
}

function bnc_is_home_enabled() {
	$ids = bnc_wp_touch_get_menu_pages();
	return $ids['enable-main-home'];
}	

function bnc_is_rss_enabled() {
	$ids = bnc_wp_touch_get_menu_pages();
	return $ids['enable-main-rss'];
}	

function bnc_is_email_enabled() {
	$ids = bnc_wp_touch_get_menu_pages();
	return $ids['enable-main-email'];
}

// Prowl Functions
function bnc_is_prowl_direct_message_enabled() {
	$settings = bnc_wptouch_get_settings();
	return ( isset( $settings['enable-prowl-message-button'] ) && $settings['enable-prowl-message-button'] && $settings['prowl-api'] );
}

function bnc_prowl_did_try_message() {
	global $wptouch_plugin;
	return $wptouch_plugin->prowl_output;
}

function bnc_prowl_message_success() {
	global $wptouch_plugin;
	return $wptouch_plugin->prowl_success;
}
// End prowl functions
  
function bnc_wp_touch_get_pages() {
	global $table_prefix;
	global $wpdb;
	
	$ids = bnc_wp_touch_get_menu_pages();
	$a = array();
	$keys = array();
	foreach ($ids as $k => $v) {
		if ($k == 'main_title' || $k == 'enable-post-excerpts' || $k == 'enable-page-coms' || 
			 $k == 'enable-cats-button'  || $k == 'enable-tags-button'  || $k == 'enable-search-button'  || 
			 $k == 'enable-login-button' || $k == 'enable-gravatars' || $k == 'enable-ajax-comments' || 
			 $k == 'enable-main-home' || $k == 'enable-main-rss' || $k == 'enable-main-email' || 
			 $k == 'enable-main-name' || $k == 'enable-main-tags' || $k == 'enable-main-categories' || 
			 $k == 'enable-prowl-comments-button' || $k == 'enable-prowl-users-button' || 
			 $k == 'enable-prowl-message-button' || $k == 'enable-gigpress-button') {
			} else {
				if (is_numeric($k)) {
					$keys[] = $k;
				}
			}
	}
	 
	$menu_order = array(); 
	$results = false;

	if ( count( $keys ) > 0 ) {
		$query = "select * from {$table_prefix}posts where ID in (" . implode(',', $keys) . ") and post_status = 'publish' order by post_title asc";
		$results = $wpdb->get_results( $query, ARRAY_A );
	}

	if ( $results ) {
		foreach ( $results as $row ) {
			$row['icon'] = $ids[$row['ID']];
			$a[$row['ID']] = $row;
			if (isset($menu_order[$row['menu_order']])) {
				$menu_order[$row['menu_order']*100 + $inc] = $row;
			} else {
				$menu_order[$row['menu_order']*100] = $row;
			}
			$inc = $inc + 1;
		}
	}

	if (isset($ids['sort-order']) && $ids['sort-order'] == 'page') {
		asort($menu_order);
		return $menu_order;
	} else {
		return $a;
	}
}

function bnc_get_header_title() {
	$v = bnc_wp_touch_get_menu_pages();
	return $v['header-title'];
}

function bnc_get_header_background() {
	$v = bnc_wp_touch_get_menu_pages();
	return $v['header-background-color'];
}
  
function bnc_get_header_border_color() {
	$v = bnc_wp_touch_get_menu_pages();
	return $v['header-border-color'];
}

function bnc_get_header_color() {
	$v = bnc_wp_touch_get_menu_pages();
	return $v['header-text-color'];
}

function bnc_get_link_color() {
	$v = bnc_wp_touch_get_menu_pages();
	return $v['link-color'];
}

function bnc_get_h2_font() {
	$v = bnc_wp_touch_get_menu_pages();
	return $v['h2-font'];
}

require_once( 'include/icons.php' );
  
function bnc_wp_touch_page() {
	if (isset($_POST['submit'])) {
		echo('<div class="wrap"><div id="bnc-global"><div id="wptouchupdated" style="display:none"><p class="saved"><span>');
		echo __( "Settings saved", "wptouch");
		echo('</span></p></div>');
		} 
	elseif (isset($_POST['reset'])) {
		echo('<div class="wrap"><div id="bnc-global"><div id="wptouchupdated" style="display:none"><p class="reset"><span>');
		echo __( "Defaults restored", "wptouch");
		echo('</span></p></div>');
	} else {
		echo('<div class="wrap"><div id="bnc-global">');
}
?>

<?php $icons = bnc_get_icon_list(); ?>

	<?php require_once( 'include/submit.php' ); ?>
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<?php require_once( 'html/head-area.php' ); ?>
		<?php require_once( 'html/general-settings-area.php' ); ?>
		<?php require_once( 'html/advanced-area.php' ); ?>
		<?php require_once( 'html/push-area.php' ); ?>
		<?php require_once( 'html/style-area.php' ); ?>
		<?php require_once( 'html/icon-area.php' ); ?>
		<?php require_once( 'html/page-area.php' ); ?>
		<?php require_once( 'html/ads-stats-area.php' ); ?>
		<?php require_once( 'html/plugin-compat-area.php' ); ?>		
		<input type="submit" name="submit" value="<?php _e('Save Options', 'wptouch' ); ?>" id="bnc-button" class="button-primary" />
	</form>
	
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<input type="submit" onclick="return confirm('<?php _e('Restore default WPtouch settings?', 'wptouch' ); ?>');" name="reset" value="<?php _e('Restore Defaults', 'wptouch' ); ?>" id="bnc-button-reset" class="button-highlighted" />
	</form>
		
		<?php echo('' . WPtouch('<div class="bnc-plugin-version"> This is ','</div>') . ''); ?>

	<div class="bnc-clearer"></div>
</div>
<?php 
echo('</div>'); } 
add_action('wp_footer', 'wptouch_switch');
add_action('admin_head', 'wptouch_admin_files');
add_action('admin_menu', 'bnc_options_menu'); 
add_filter( 'plugin_action_links', 'wptouch_settings_link', 9, 2 );
?>