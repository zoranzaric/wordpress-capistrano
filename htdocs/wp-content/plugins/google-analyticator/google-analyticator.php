<?php 
/*
 * Plugin Name: Google Analyticator
 * Version: 6.0.2
 * Plugin URI: http://ronaldheft.com/code/analyticator/
 * Description: Adds the necessary JavaScript code to enable <a href="http://www.google.com/analytics/">Google's Analytics</a>. After enabling this plugin visit <a href="options-general.php?page=google-analyticator.php">the settings page</a> and enter your Google Analytics' UID and enable logging.
 * Author: Ronald Heft
 * Author URI: http://ronaldheft.com/
 * Text Domain: google-analyticator
 */

define('GOOGLE_ANALYTICATOR_VERSION', '6.0.2');

// Constants for enabled/disabled state
define("ga_enabled", "enabled", true);
define("ga_disabled", "disabled", true);

// Defaults, etc.
define("key_ga_uid", "ga_uid", true);
define("key_ga_status", "ga_status", true);
define("key_ga_admin", "ga_admin_status", true);
define("key_ga_admin_disable", "ga_admin_disable", true);
define("key_ga_admin_level", "ga_admin_level", true);
define("key_ga_adsense", "ga_adsense", true);
define("key_ga_extra", "ga_extra", true);
define("key_ga_extra_after", "ga_extra_after", true);
define("key_ga_event", "ga_event", true);
define("key_ga_outbound", "ga_outbound", true);
define("key_ga_outbound_prefix", "ga_outbound_prefix", true);
define("key_ga_downloads", "ga_downloads", true);
define("key_ga_downloads_prefix", "ga_downloads_prefix", true);
define("key_ga_widgets", "ga_widgets", true);

define("ga_uid_default", "XX-XXXXX-X", true);
define("ga_status_default", ga_disabled, true);
define("ga_admin_default", ga_enabled, true);
define("ga_admin_disable_default", 'remove', true);
define("ga_admin_level_default", 8, true);
define("ga_adsense_default", "", true);
define("ga_extra_default", "", true);
define("ga_extra_after_default", "", true);
define("ga_event_default", ga_enabled, true);
define("ga_outbound_default", ga_enabled, true);
define("ga_outbound_prefix_default", 'outgoing', true);
define("ga_downloads_default", "", true);
define("ga_downloads_prefix_default", "download", true);
define("ga_widgets_default", ga_enabled, true);

// Create the default key and status
add_option(key_ga_status, ga_status_default, 'If Google Analytics logging in turned on or off.');
add_option(key_ga_uid, ga_uid_default, 'Your Google Analytics UID.');
add_option(key_ga_admin, ga_admin_default, 'If WordPress admins are counted in Google Analytics.');
add_option(key_ga_admin_disable, ga_admin_disable_default, '');
add_option(key_ga_admin_level, ga_admin_level_default, 'The level to consider a user a WordPress admin.');
add_option(key_ga_adsense, ga_adsense_default, '');
add_option(key_ga_extra, ga_extra_default, 'Addition Google Analytics tracking options');
add_option(key_ga_extra_after, ga_extra_after_default, 'Addition Google Analytics tracking options');
add_option(key_ga_event, ga_event_default, '');
add_option(key_ga_outbound, ga_outbound_default, 'Add tracking of outbound links');
add_option(key_ga_outbound_prefix, ga_outbound_prefix_default, 'Add tracking of outbound links');
add_option(key_ga_downloads, ga_downloads_default, 'Download extensions to track with Google Analyticator');
add_option(key_ga_downloads_prefix, ga_downloads_prefix_default, 'Download extensions to track with Google Analyticator');
add_option(key_ga_widgets, ga_widgets_default, 'If the widgets are enabled or disabled');
add_option('ga_google_token', '', 'The token used to authenticate with Google');
add_option('ga_compatibility', 'off', 'Transport compatibility options');

# Check if we have a version of WordPress greater than 2.8
if ( function_exists('register_widget') ) {
	
	# Check if widgets are enabled
	if ( get_option(key_ga_widgets) == 'enabled' ) {
			
		# Include Google Analytics Stats widget
		require_once('google-analytics-stats-widget.php');

		# Include the Google Analytics Summary widget
		require_once('google-analytics-summary-widget.php');
		$google_analytics_summary = new GoogleAnalyticsSummary();
		
	}

}

// Create a option page for settings
add_action('admin_init', 'ga_admin_init');
add_action('admin_menu', 'add_ga_option_page');

// Initialize the options
function ga_admin_init() {
	# Load the localization information
	$plugin_dir = basename(dirname(__FILE__));
	load_plugin_textdomain('google-analyticator', 'wp-content/plugins/' . $plugin_dir . '/localizations', $plugin_dir . '/localizations');
}

# Add the core Google Analytics script, with a high priority to ensure last script for async tracking
add_action('wp_head', 'add_google_analytics', 999999);

# Initialize outbound link tracking
add_action('init', 'ga_outgoing_links');

// Hook in the options page function
function add_ga_option_page() {
	$plugin_page = add_options_page(__('Google Analyticator Settings', 'google-analyticator'), 'Google Analytics', 8, basename(__FILE__), 'ga_options_page');
	
	# Include javascript on the GA settings page
	add_action('admin_head-' . $plugin_page, 'ga_admin_ajax');
}

add_action('plugin_action_links_' . plugin_basename(__FILE__), 'ga_filter_plugin_actions');

// Add settings option
function ga_filter_plugin_actions($links) {
	$new_links = array();
	
	$new_links[] = '<a href="options-general.php?page=google-analyticator.php">' . __('Settings', 'google-analyticator') . '</a>';
	
	return array_merge($new_links, $links);
}

add_filter('plugin_row_meta', 'ga_filter_plugin_links', 10, 2);

// Add FAQ and support information
function ga_filter_plugin_links($links, $file)
{
	if ( $file == plugin_basename(__FILE__) )
	{
		$links[] = '<a href="http://forums.ronaldheft.com/viewforum.php?f=5">' . __('FAQ', 'google-analyticator') . '</a>';
		$links[] = '<a href="http://forums.ronaldheft.com/viewforum.php?f=6">' . __('Support', 'google-analyticator') . '</a>';
		$links[] = '<a href="http://ronaldheft.com/code/donate/">' . __('Donate', 'google-analyticator') . '</a>';
	}
	
	return $links;
}

function ga_options_page() {
	// If we are a postback, store the options
	if (isset($_POST['info_update'])) {
		# Verify nonce
		check_admin_referer('google-analyticator-update_settings');
					
		// Update the status
		$ga_status = $_POST[key_ga_status];
		if (($ga_status != ga_enabled) && ($ga_status != ga_disabled))
			$ga_status = ga_status_default;
		update_option(key_ga_status, $ga_status);

		// Update the UID
		$ga_uid = $_POST[key_ga_uid];
		if ($ga_uid == '')
			$ga_uid = ga_uid_default;
		update_option(key_ga_uid, $ga_uid);

		// Update the admin logging
		$ga_admin = $_POST[key_ga_admin];
		if (($ga_admin != ga_enabled) && ($ga_admin != ga_disabled))
			$ga_admin = ga_admin_default;
		update_option(key_ga_admin, $ga_admin);
		
		// Update the admin disable setting
		$ga_admin_disable = $_POST[key_ga_admin_disable];
		if ( $ga_admin_disable == '' )
			$ga_admin_disable = ga_admin_disable_default;
		update_option(key_ga_admin_disable, $ga_admin_disable);
		
		// Update the admin level
		$ga_admin_level = $_POST[key_ga_admin_level];
		if ( $ga_admin_level == '' )
			$ga_admin_level = ga_admin_level_default;
		update_option(key_ga_admin_level, $ga_admin_level);

		// Update the extra tracking code
		$ga_extra = $_POST[key_ga_extra];
		update_option(key_ga_extra, $ga_extra);

		// Update the extra after tracking code
		$ga_extra_after = $_POST[key_ga_extra_after];
		update_option(key_ga_extra_after, $ga_extra_after);
		
		// Update the adsense key
		$ga_adsense = $_POST[key_ga_adsense];
		update_option(key_ga_adsense, $ga_adsense);
		
		// Update the event tracking
		$ga_event = $_POST[key_ga_event];
		if (($ga_event != ga_enabled) && ($ga_event != ga_disabled))
			$ga_event = ga_event_default;
		update_option(key_ga_event, $ga_event);

		// Update the outbound tracking
		$ga_outbound = $_POST[key_ga_outbound];
		if (($ga_outbound != ga_enabled) && ($ga_outbound != ga_disabled))
			$ga_outbound = ga_outbound_default;
		update_option(key_ga_outbound, $ga_outbound);
		
		// Update the outbound prefix
		$ga_outbound_prefix = $_POST[key_ga_outbound_prefix];
		if ($ga_outbound_prefix == '')
			$ga_outbound_prefix = ga_outbound_prefix_default;
		update_option(key_ga_outbound_prefix, $ga_outbound_prefix);

		// Update the download tracking code
		$ga_downloads = $_POST[key_ga_downloads];
		update_option(key_ga_downloads, $ga_downloads);
		
		// Update the download prefix
		$ga_downloads_prefix = $_POST[key_ga_downloads_prefix];
		if ($ga_downloads_prefix == '')
			$ga_downloads_prefix = ga_downloads_prefix_default;
		update_option(key_ga_downloads_prefix, $ga_downloads_prefix);
		
		// Update the widgets option
		$ga_widgets = $_POST[key_ga_widgets];
		if (($ga_widgets != ga_enabled) && ($ga_widgets != ga_disabled))
			$ga_widgets = ga_widgets_default;
		update_option(key_ga_widgets, $ga_widgets);
		
		// Update the compatibility options
		$ga_compatibility = $_POST['ga_compatibility'];
		if ( $ga_compatibility == '' )
			$ga_compatibility = 'off';
		update_option('ga_compatibility', $ga_compatibility);

		// Give an updated message
		echo "<div class='updated fade'><p><strong>" . __('Google Analyticator settings saved.', 'google-analyticator') . "</strong></p></div>";
	}

	// Output the options page
	?>

		<div class="wrap">
			
		<h2><?php _e('Google Analyticator Settings', 'google-analyticator'); ?></h2>
		
		<p><em>Like Google Analyticator? Help support it <a href="http://ronaldheft.com/code/donate/">by donating to the developer</a>. This helps cover the cost of maintaining the plugin and development time toward new features. Every donation, no matter how small, is appreciated.</em></p>
			
		<form method="post" action="options-general.php?page=google-analyticator.php">
			<?php
			# Add a nonce
			wp_nonce_field('google-analyticator-update_settings');
			?>
			
			<h3><?php _e('Basic Settings', 'google-analyticator'); ?></h3>
			<?php if (get_option(key_ga_status) == ga_disabled) { ?>
				<div style="margin:10px auto; border:3px #f00 solid; background-color:#fdd; color:#000; padding:10px; text-align:center;">
				<?php _e('Google Analytics integration is currently <strong>DISABLED</strong>.', 'google-analyticator'); ?>
				</div>
			<?php } ?>
			<?php if ((get_option(key_ga_uid) == "XX-XXXXX-X") && (get_option(key_ga_status) != ga_disabled)) { ?>
				<div style="margin:10px auto; border:3px #f00 solid; background-color:#fdd; color:#000; padding:10px; text-align:center;">
				<?php _e('Google Analytics integration is currently enabled, but you did not enter a UID. Tracking will not occur.', 'google-analyticator'); ?>
				</div>
			<?php } ?>
			<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
				<tr>
					<th width="30%" valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_status ?>"><?php _e('Google Analytics logging is', 'google-analyticator'); ?>:</label>
					</th>
					<td>
						<?php
						echo "<select name='".key_ga_status."' id='".key_ga_status."'>\n";
						
						echo "<option value='".ga_enabled."'";
						if(get_option(key_ga_status) == ga_enabled)
							echo " selected='selected'";
						echo ">" . __('Enabled', 'google-analyticator') . "</option>\n";
						
						echo "<option value='".ga_disabled."'";
						if(get_option(key_ga_status) == ga_disabled)
							echo" selected='selected'";
						echo ">" . __('Disabled', 'google-analyticator') . "</option>\n";
						
						echo "</select>\n";
						?>
					</td>
				</tr>
				<?php
				# Check if we have a version of WordPress greater than 2.8, and check if we have the memory to use the api
				if ( function_exists('register_widget') ) {
				?>
				<tr>
					<th width="30%" valign="top" style="padding-top: 10px;">
						<label><?php _e('Authenticate with Google', 'google-analyticator'); ?>:</label>
					</th>
					<td>
						<?php if ( ( trim(get_option('ga_google_token')) == '' && !isset($_GET['token']) ) || ( isset($_GET['token']) && $_GET['token'] == 'deauth' ) ) { ?>
							<p style="margin-top: 7px;"><a href="https://www.google.com/accounts/AuthSubRequest?<?php echo http_build_query(array(		'next' => admin_url('/options-general.php?page=google-analyticator.php'),
							'scope' => 'https://www.google.com/analytics/feeds/',
							'secure' => 0,
							'session' => 1,
							'hd' => 'default'
								)); ?>"><?php _e('Click here to login to Google, thus authenticating Google Analyticator with your Analytics account.', 'google-analyticator'); ?></a></p>
						<?php } else { ?>
							<p style="margin-top: 7px;"><?php _e('Currently authenticated with Google.', 'google-analyticator'); ?> <a href="<?php echo admin_url('/options-general.php?page=google-analyticator.php&token=deauth'); ?>"><?php _e('Deauthorize Google Analyticator.', 'google-analyticator'); ?></a></p>
							<?php if ( isset($_GET['token']) && $_GET['token'] != 'deauth' ) { ?>
								<p style="color: red; display: none;" id="ga_connect_error"><?php _e('Failed to authenticate with Google. <a href="http://forums.ronaldheft.com/viewtopic.php?f=5&t=851">Read this support article</a> on Analyticator\'s support forums for help.', 'google-analyticator'); ?></p>
							<?php } ?>
						<?php } ?>
						<p style="margin: 5px 10px;" class="setting-description"><?php _e('Clicking the above link will authenticate Google Analyticator with Google. Authentication with Google is needed for use with the stats widget. In addition, authenticating will enable you to select your Analytics account through a drop-down instead of searching for your UID. If you are not going to use the stat widget, <strong>authenticating with Google is optional</strong>.', 'google-analyticator'); ?></p>
					</td>
				</tr>
				<?php } ?>
				<tr id="ga_ajax_accounts">
					<th valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_uid; ?>"><?php _e('Google Analytics UID', 'google-analyticator'); ?>:</label>
					</th>
					<td>
						<?php
						echo "<input type='text' size='50' ";
						echo "name='".key_ga_uid."' ";
						echo "id='".key_ga_uid."' ";
						echo "value='".get_option(key_ga_uid)."' />\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description"><?php _e('Enter your Google Analytics\' UID in this box (<a href="http://plugins.spiralwebconsulting.com/forums/viewtopic.php?f=5&amp;t=6">where can I find my UID?</a>). The UID is needed for Google Analytics to log your website stats.', 'google-analyticator'); ?> <strong><?php if ( function_exists('register_widget') ) _e('If you are having trouble finding your UID, authenticate with Google in the above field. After returning from Google, you will be able to select your account through a drop-down box.', 'google-analyticator'); ?></strong></p>
					</td>
				</tr>
			</table>
			<h3><?php _e('Advanced Settings', 'google-analyticator'); ?></h3>
				<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
				<tr>
					<th width="30%" valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_admin ?>"><?php _e('WordPress admin logging', 'google-analyticator'); ?>:</label>
					</th>
					<td>
						<?php
						echo "<select name='".key_ga_admin."' id='".key_ga_admin."'>\n";
						
						echo "<option value='".ga_enabled."'";
						if(get_option(key_ga_admin) == ga_enabled)
							echo " selected='selected'";
						echo ">" . __('Enabled', 'google-analyticator') . "</option>\n";
						
						echo "<option value='".ga_disabled."'";
						if(get_option(key_ga_admin) == ga_disabled)
							echo" selected='selected'";
						echo ">" . __('Disabled', 'google-analyticator') . "</option>\n";
						
						echo "</select>\n";
						
						# Generate the user level box
						$level = "<input type='text' size='2' ";
						$level .= "name='".key_ga_admin_level."' ";
						$level .= "id='".key_ga_admin_level."' ";
						$level .= "value='".stripslashes(get_option(key_ga_admin_level))."' />\n";
						
						# Output the current user level
						if ( current_user_can('level_10') )
							$user = '10';
						elseif ( current_user_can('level_9') )
							$user = '9';
						elseif ( current_user_can('level_8') )
							$user = '8';
						elseif ( current_user_can('level_7') )
							$user = '7';
						elseif ( current_user_can('level_6') )
							$user = '6';
						elseif ( current_user_can('level_5') )
							$user = '5';
						elseif ( current_user_can('level_4') )
							$user = '4';
						elseif ( current_user_can('level_3') )
							$user = '3';
						elseif ( current_user_can('level_2') )
							$user = '2';
						elseif ( current_user_can('level_1') )
							$user = '1';
						else
							$user = '0';
						?>
						<p style="margin: 5px 10px;" class="setting-description"><?php printf(__('Disabling this option will prevent all logged in WordPress admins from showing up on your Google Analytics reports. A WordPress admin is defined as a user with a level %s or higher. Your user level is %d.', 'google-analyticator'), $level, $user); ?></p>
					</td>
				</tr>
				<tr>
					<th width="30%" valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_admin_disable ?>"><?php _e('Admin tracking disable method', 'google-analyticator'); ?>:</label>
					</th>
					<td>
						<?php
						echo "<select name='".key_ga_admin_disable."' id='".key_ga_admin_disable."'>\n";
						
						echo "<option value='remove'";
						if(get_option(key_ga_admin_disable) == 'remove')
							echo " selected='selected'";
						echo ">" . __('Remove', 'google-analyticator') . "</option>\n";
						
						echo "<option value='admin'";
						if(get_option(key_ga_admin_disable) == 'admin')
							echo" selected='selected'";
						echo ">" . __('Use \'admin\' variable', 'google-analyticator') . "</option>\n";
						
						echo "</select>\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description"><?php _e('Selecting the "Remove" option will physically remove the tracking code from logged in admin users. Selecting the "Use \'admin\' variable" option will assign a variable called \'admin\' to logged in admin users. This option will allow Google Analytics\' site overlay feature to work, but you will have to manually configure Google Analytics to exclude tracking from hits with the \'admin\' variable.', 'google-analyticator'); ?></p>
					</td>
				</tr>
				<tr>
					<th width="30%" valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_outbound ?>"><?php _e('Outbound link tracking', 'google-analyticator'); ?>:</label>
					</th>
					<td>
						<?php
						echo "<select name='".key_ga_outbound."' id='".key_ga_outbound."'>\n";
						
						echo "<option value='".ga_enabled."'";
						if(get_option(key_ga_outbound) == ga_enabled)
							echo " selected='selected'";
						echo ">" . __('Enabled', 'google-analyticator') . "</option>\n";
						
						echo "<option value='".ga_disabled."'";
						if(get_option(key_ga_outbound) == ga_disabled)
							echo" selected='selected'";
						echo ">" . __('Disabled', 'google-analyticator') . "</option>\n";
						
						echo "</select>\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description"><?php _e('Disabling this option will turn off the tracking of outbound links. It\'s recommended not to disable this option unless you\'re a privacy advocate (now why would you be using Google Analytics in the first place?) or it\'s causing some kind of weird issue.', 'google-analyticator'); ?></p>
					</td>
				</tr>
				<tr>
					<th width="30%" valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_event ?>"><?php _e('Event tracking', 'google-analyticator'); ?>:</label>
					</th>
					<td>
						<?php
						echo "<select name='".key_ga_event."' id='".key_ga_event."'>\n";
						
						echo "<option value='".ga_enabled."'";
						if(get_option(key_ga_event) == ga_enabled)
							echo " selected='selected'";
						echo ">" . __('Enabled', 'google-analyticator') . "</option>\n";
						
						echo "<option value='".ga_disabled."'";
						if(get_option(key_ga_event) == ga_disabled)
							echo" selected='selected'";
						echo ">" . __('Disabled', 'google-analyticator') . "</option>\n";
						
						echo "</select>\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description"><?php _e('Enabling this option will treat outbound links and downloads as events instead of pageviews. Since the introduction of <a href="http://code.google.com/apis/analytics/docs/tracking/eventTrackerOverview.html">event tracking in Analytics</a>, this is the recommended way to track these types of actions. Only disable this option if you must use the old pageview tracking method.', 'google-analyticator'); ?></p>
					</td>
				</tr>
				<tr>
					<th valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_downloads; ?>"><?php _e('Download extensions to track', 'google-analyticator'); ?>:</label>
					</th>
					<td>
						<?php
						echo "<input type='text' size='50' ";
						echo "name='".key_ga_downloads."' ";
						echo "id='".key_ga_downloads."' ";
						echo "value='".stripslashes(get_option(key_ga_downloads))."' />\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description"><?php _e('Enter any extensions of files you would like to be tracked as a download. For example to track all MP3s and PDFs enter <strong>mp3,pdf</strong>. <em>Outbound link tracking must be enabled for downloads to be tracked.</em>', 'google-analyticator'); ?></p>
					</td>
				</tr>
				<tr>
					<th valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_outbound_prefix; ?>"><?php _e('Prefix external links with', 'google-analyticator'); ?>:</label>
					</th>
					<td>
						<?php
						echo "<input type='text' size='50' ";
						echo "name='".key_ga_outbound_prefix."' ";
						echo "id='".key_ga_outbound_prefix."' ";
						echo "value='".stripslashes(get_option(key_ga_outbound_prefix))."' />\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description"><?php _e('Enter a name for the section tracked external links will appear under. This option has no effect if event tracking is enabled.', 'google-analyticator'); ?></em></p>
					</td>
				</tr>
				<tr>
					<th valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_downloads_prefix; ?>"><?php _e('Prefix download links with', 'google-analyticator'); ?>:</label>
					</th>
					<td>
						<?php
						echo "<input type='text' size='50' ";
						echo "name='".key_ga_downloads_prefix."' ";
						echo "id='".key_ga_downloads_prefix."' ";
						echo "value='".stripslashes(get_option(key_ga_downloads_prefix))."' />\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description"><?php _e('Enter a name for the section tracked download links will appear under. This option has no effect if event tracking is enabled.', 'google-analyticator'); ?></em></p>
					</td>
				</tr>
				<tr>
					<th valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_adsense; ?>"><?php _e('Google Adsense ID', 'google-analyticator'); ?>:</label>
					</th>
					<td>
						<?php
						echo "<input type='text' size='50' ";
						echo "name='".key_ga_adsense."' ";
						echo "id='".key_ga_adsense."' ";
						echo "value='".get_option(key_ga_adsense)."' />\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description"><?php _e('Enter your Google Adsense ID assigned by Google Analytics in this box. This enables Analytics tracking of Adsense information if your Adsense and Analytics accounts are linked. Note: Google recommends the Analytics tracking code is placed in the header with this option enabled, however, a fix is included in this plugin. To follow the official specs, do not enable footer tracking.', 'google-analyticator'); ?></p>
					</td>
				</tr>
				<tr>
					<th valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_extra; ?>"><?php _e('Additional tracking code', 'google-analyticator'); ?><br />(<?php _e('before tracker initialization', 'google-analyticator'); ?>):</label>
					</th>
					<td>
						<?php
						echo "<textarea cols='50' rows='8' ";
						echo "name='".key_ga_extra."' ";
						echo "id='".key_ga_extra."'>";
						echo stripslashes(get_option(key_ga_extra))."</textarea>\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description"><?php _e('Enter any additional lines of tracking code that you would like to include in the Google Analytics tracking script. The code in this section will be displayed <strong>before</strong> the Google Analytics tracker is initialized. Read <a href="http://www.google.com/analytics/InstallingGATrackingCode.pdf">Google Analytics tracker manual</a> to learn what code goes here and how to use it.', 'google-analyticator'); ?></p>
					</td>
				</tr>
				<tr>
					<th valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_extra_after; ?>"><?php _e('Additional tracking code', 'google-analyticator'); ?><br />(<?php _e('after tracker initialization', 'google-analyticator'); ?>):</label>
					</th>
					<td>
						<?php
						echo "<textarea cols='50' rows='8' ";
						echo "name='".key_ga_extra_after."' ";
						echo "id='".key_ga_extra_after."'>";
						echo stripslashes(get_option(key_ga_extra_after))."</textarea>\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description"><?php _e('Enter any additional lines of tracking code that you would like to include in the Google Analytics tracking script. The code in this section will be displayed <strong>after</strong> the Google Analytics tracker is initialized. Read <a href="http://www.google.com/analytics/InstallingGATrackingCode.pdf">Google Analytics tracker manual</a> to learn what code goes here and how to use it.', 'google-analyticator'); ?></p>
					</td>
				</tr>
				<?php
				# Check if we have a version of WordPress greater than 2.8
				if ( function_exists('register_widget') ) {
				?>
				<tr>
					<th width="30%" valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_widgets; ?>"><?php _e('Include widgets', 'google-analyticator'); ?>:</label>
					</th>
					<td>
						<?php
						echo "<select name='".key_ga_widgets."' id='".key_ga_widgets."'>\n";
						
						echo "<option value='".ga_enabled."'";
						if(get_option(key_ga_widgets) == ga_enabled)
							echo " selected='selected'";
						echo ">" . __('Enabled', 'google-analyticator') . "</option>\n";
						
						echo "<option value='".ga_disabled."'";
						if(get_option(key_ga_widgets) == ga_disabled)
							echo" selected='selected'";
						echo ">" . __('Disabled', 'google-analyticator') . "</option>\n";
						
						echo "</select>\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description"><?php _e('Disabling this option will completely remove the Dashboard Summary widget and the theme Stats widget. Use this option if you would prefer to not see the widgets.', 'google-analyticator'); ?></p>
					</td>
				</tr>
				<tr>
					<th width="30%" valign="top" style="padding-top: 10px;">
						<label for="ga_compatibility"><?php _e('Authentication compatibility', 'google-analyticator'); ?>:</label>
					</th>
					<td>
						<?php
						echo "<select name='ga_compatibility' id='ga_compatibility'>\n";
						
						echo "<option value='off'";
						if(get_option('ga_compatibility') == 'off')
							echo " selected='selected'";
						echo ">" . __('Off', 'google-analyticator') . "</option>\n";
						
						echo "<option value='level1'";
						if(get_option('ga_compatibility') == 'level1')
							echo " selected='selected'";
						echo ">" . __('Disable cURL', 'google-analyticator') . "</option>\n";
						
						echo "<option value='level2'";
						if(get_option('ga_compatibility') == 'level2')
							echo " selected='selected'";
						echo ">" . __('Disable cURL and PHP Streams', 'google-analyticator') . "</option>\n";
						
						echo "</select>\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description"><?php _e('If you\'re having trouble authenticating with Google for use with the stats widgets, try setting these compatibility modes. Try disabling cURL first and re-authenticate. If that fails, try disabling cURL and PHP Streams.', 'google-analyticator'); ?></p>
					</td>
				</tr>
				<?php } ?>
				</table>
			<p class="submit">
				<input type="submit" name="info_update" value="<?php _e('Save Changes', 'google-analyticator'); ?>" />
			</p>
		</div>
		</form>

<?php
}

/**
 * Adds AJAX to the GA settings page
 **/
function ga_admin_ajax()
{
	if ( function_exists('register_widget') ) {
	?>
	<script type="text/javascript">
	
		jQuery(document).ready(function(){
			
			// Grab the widget data
			jQuery.ajax({
				type: 'post',
				url: 'admin-ajax.php',
				data: {
					action: 'ga_ajax_accounts',
					_ajax_nonce: '<?php echo wp_create_nonce("google-analyticator-accounts_get"); ?>'<?php if ( isset($_GET['token']) ) { ?>,
					token: '<?php echo $_GET["token"]; ?>'
					<?php } ?>
				},
				success: function(html) {
					if ( html != '' )
						jQuery('#ga_ajax_accounts').html(html);
					else
						jQuery('#ga_connect_error').show();
				}
			});
		
		});
	
	</script>
	<?php
	}
}

# Look for the ajax list call
add_action('wp_ajax_ga_ajax_accounts', 'ga_ajax_accounts');

/**
 * An AJAX function to get a list of accounts in a drop down
 **/
function ga_ajax_accounts()
{
	# Check the ajax widget
	check_ajax_referer('google-analyticator-accounts_get');
	
	# Get the list of accounts if available
	$ga_accounts = ga_get_analytics_accounts();
	
	if ( $ga_accounts !== false ) {
	?>
	
	<th valign="top" style="padding-top: 10px;">
		<label for="<?php echo key_ga_uid; ?>"><?php _e('Google Analytics account', 'google-analyticator'); ?>:</label>
	</th>
	<td>
		<?php
		# Create a select box	
		echo '<select name="' . key_ga_uid . '" id="' . key_ga_uid . '">';
		echo '<option value="XX-XXXXX-X">' . __('Select an Account', 'google-analyticator') . '</option>';
	
		# The list of accounts
		foreach ( $ga_accounts AS $account ) {
			$select = ( get_option(key_ga_uid) == $account['ga:webPropertyId'] ) ? ' selected="selected"' : '';
			echo '<option value="' . $account['ga:webPropertyId'] . '"' . $select . '>' . $account['title'] . '</option>';
		}
	
		# Close the select box
		echo '</select>';
		?>
		<p style="margin: 5px 10px;" class="setting-description"><?php _e('Select the Analytics account you wish to enable tracking for. An account must be selected for tracking to occur.', 'google-analyticator'); ?></p>
	</td>
	
	<?php
	}
	die();
}

/**
 * Checks if the WordPress API is a valid method for selecting an account
 *
 * @return a list of accounts if available, false if none available
 **/
function ga_get_analytics_accounts()
{
	$accounts = array();
	
	# Get the class for interacting with the Google Analytics
	require_once('class.analytics.stats.php');
	
	# Create a new Gdata call
	if ( isset($_POST['token']) && $_POST['token'] != '' )
		$stats = new GoogleAnalyticsStats($_POST['token']);
	elseif ( trim(get_option('ga_google_token')) != '' )
		$stats = new GoogleAnalyticsStats();
	else
		return false;
		
	# Check if Google sucessfully logged in
	if ( ! $stats->checkLogin() )
		return false;

	# Get a list of accounts
	$accounts = $stats->getAnalyticsAccounts();
	
	# Return the account array if there are accounts
	if ( count($accounts) > 0 )
		return $accounts;
	else
		return false;
}

/**
 * Add http_build_query if it doesn't exist already
 **/
if ( !function_exists('http_build_query') ) {
	function http_build_query($params, $key = null)
	{
		$ret = array();
		
		foreach( (array) $params as $name => $val ) {
			$name = urlencode($name);
			
			if ( $key !== null )
				$name = $key . "[" . $name . "]";
			
			if ( is_array($val) || is_object($val) )
				$ret[] = http_build_query($val, $name);
			elseif ($val !== null)
				$ret[] = $name . "=" . urlencode($val);
		}
        
		return implode("&", $ret);
	}
}

/**
 * Echos out the core Analytics tracking code
 **/
function add_google_analytics()
{
	# Fetch variables used in the tracking code
	$uid = stripslashes(get_option(key_ga_uid));
	$extra = stripslashes(get_option(key_ga_extra));
	$extra_after = stripslashes(get_option(key_ga_extra_after));
	$extensions = str_replace (",", "|", get_option(key_ga_downloads));
	
	# Determine if the GA is enabled and contains a valid UID
	if ( ( get_option(key_ga_status) != ga_disabled ) && ( $uid != "XX-XXXXX-X" ) )
	{
		# Determine if the user is an admin, and should see the tracking code
		if ( ( get_option(key_ga_admin) == ga_enabled || !current_user_can('level_' . get_option(key_ga_admin_level)) ) && get_option(key_ga_admin_disable) == 'remove' || get_option(key_ga_admin_disable) != 'remove' )
		{
			# Add the notice that Google Analyticator tracking is enabled
			echo "<!-- Google Analytics Tracking by Google Analyticator " . GOOGLE_ANALYTICATOR_VERSION . ": http://ronaldheft.com/code/analyticator/ -->\n";
			
			# Add the Adsense data if specified
			if ( get_option(key_ga_adsense) != '' )
				echo '<script type="text/javascript">window.google_analytics_uacct = "' . get_option(key_ga_adsense) . "\";</script>\n";
			
			# Include the file types to track
			$extensions = explode(',', stripslashes(get_option(key_ga_downloads)));
			$ext = "";
			foreach ( $extensions AS $extension )
				$ext .= "'$extension',";
			$ext = substr($ext, 0, -1);

			# Include the link tracking prefixes
			$outbound_prefix = stripslashes(get_option(key_ga_outbound_prefix));
			$downloads_prefix = stripslashes(get_option(key_ga_downloads_prefix));
			$event_tracking = get_option(key_ga_event);

			?>
<script type="text/javascript">
	var analyticsFileTypes = [<?php echo strtolower($ext); ?>];
<?php if ( $event_tracking != 'enabled' ) { ?>
	var analyticsOutboundPrefix = '/<?php echo $outbound_prefix; ?>/';
	var analyticsDownloadsPrefix = '/<?php echo $downloads_prefix; ?>/';
<?php } ?>
	var analyticsEventTracking = '<?php echo $event_tracking; ?>';
</script>
<?php
			# Add the first part of the core tracking code
			?>
<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '<?php echo $uid; ?>']);
<?php
		
			# Add any tracking code before the trackPageview
			if ( '' != $extra )
				echo "	$extra\n";
			
			# Add the track pageview function
			echo "	_gaq.push(['_trackPageview']);\n";
		
			# Disable page tracking if admin is logged in
			if ( ( get_option(key_ga_admin) == ga_disabled ) && ( current_user_can('level_' . get_option(key_ga_admin_level)) ) )
				echo "	_gaq.push(['_setVar', 'admin']);\n";
		
			# Add any tracking code after the trackPageview
			if ( '' != $extra_after )
				echo "	$extra_after\n";
		
			# Add the final section of the tracking code
			?>

	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);
	})();
</script>
<?php
		}
	}
}

/**
 * Adds outbound link tracking to Google Analyticator
 **/
function ga_outgoing_links()
{
	# Fetch the UID
	$uid = stripslashes(get_option(key_ga_uid));
	
	# If GA is enabled and has a valid key
	if (  (get_option(key_ga_status) != ga_disabled ) && ( $uid != "XX-XXXXX-X" ) )
	{
		# If outbound tracking is enabled
		if ( get_option(key_ga_outbound) == ga_enabled )
		{
			# If this is not an admin page
			if ( !is_admin() )
			{
				# Display page tracking if user is not an admin
				if ( ( get_option(key_ga_admin) == ga_enabled || !current_user_can('level_' . get_option(key_ga_admin_level)) ) && get_option(key_ga_admin_disable) == 'remove' || get_option(key_ga_admin_disable) != 'remove' )
				{
					add_action('wp_print_scripts', 'ga_external_tracking_js');
				}
			}
		}
	}
}

/**
 * Adds the scripts required for outbound link tracking
 **/
function ga_external_tracking_js()
{
	wp_enqueue_script('ga-external-tracking', plugins_url('/google-analyticator/external-tracking.min.js'), array('jquery'), GOOGLE_ANALYTICATOR_VERSION);
}

/**
 * EXPERIMENTAL: Retrieve Google's visits for the given page
 * More work needs to be done. Needs caching, needs to be less resource intensive, and
 * needs an automated way to determine the page.
 * Function may/will change in future releases. Only use if you know what you're doing.
 *
 * @param url - the page url, missing the domain information
 * @param days - the number of days to get
 * @return the number of visits
 **/
function get_analytics_visits_by_page($page, $days = 31)
{
	require_once('class.analytics.stats.php');
	
	# Create a new API object
	$api = new GoogleAnalyticsStats();
	
	# Get the current accounts accounts
	$accounts = ga_get_analytics_accounts();
	
	# Verify accounts exist
	if ( count($accounts) <= 0 )
		return 0;
	
	# Loop throught the account and return the current account
	foreach ( $accounts AS $account )
	{
		# Check if the UID matches the selected UID
		if ( $account['ga:webPropertyId'] == get_option('ga_uid') )
		{
			$api->setAccount($account['id']);
			break;
		}
	}
	
	# Encode the page url
	$page = urlencode($page);
	
	# Get the metric information from Google
	$before = date('Y-m-d', strtotime('-' . $days . ' days'));
	$yesterday = date('Y-m-d', strtotime('-1 day'));
	$stats = $api->getMetrics('ga:visits', $before, $yesterday, 'ga:pagePath', false, 'ga:pagePath%3D%3D' . $page, 1);
	
	# Check the size of the stats array
	if ( count($stats) <= 0 || !is_array($stats) ) {
		return 0;
	} else {
		# Loop through each stat for display
		foreach ( $stats AS $stat ) {
			return $stat['ga:visits'];
		}
	}
}

?>