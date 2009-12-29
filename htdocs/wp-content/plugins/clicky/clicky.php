<?php
/*
Plugin Name: Clicky for WordPress
Version: 1.0.6
Plugin URI: http://getclicky.com/goodies/#wordpress
Description: Integrates Clicky on your blog!
Author: Joost de Valk
Author URI: http://yoast.com/
*/

load_plugin_textdomain('clicky','','/clicky/lang/');

if ( ! class_exists( 'Clicky_Admin' ) ) {

	require_once('yst_plugin_tools.php');
	
	class Clicky_Admin extends Clicky_Base_Plugin_Admin {
		
		var $hook 				= 'clicky';
		var $longname			= 'Clicky Configuration';
		var $shortname			= 'Clicky';
		var $shorturl_service	= 'Clicky.me';
		var $filename			= 'clicky/clicky.php';
		var $homepage			= 'http://getclicky.com/goodies/';
		var $feed 				= 'http://getclicky.com/blog/rss';
				
		function meta_box() {
			add_meta_box('clickyme',$this->shortname,array('Clicky_Admin','clicky_meta_box'),'post','side');
			add_meta_box('clickyme',$this->shortname,array('Clicky_Admin','clicky_meta_box'),'page','side');
		}

		function clicky_admin_warnings() {
			$options = clicky_get_options();
			if ( (!$options['site_id'] || empty($options['site_id']) || !$options['site_key'] || empty($options['site_key']) || !$options['admin_site_key'] || empty($options['admin_site_key'])) && !$_POST ) {
				function clicky_warning() {
					echo "<div id='clickywarning' class='updated fade'><p><strong>";
					_e('Clicky is almost ready. ', 'clicky');
					echo "</strong>";
					printf (__('You must %1$s enter your Clicky Site ID, Site Key and Admin Site Key%2$s for it to work.', 'clicky'), "<a href='options-general.php?page=clicky'>", "</a>");
					echo "</p></div>";
					echo "<script type=\"text/javascript\">setTimeout(function(){jQuery('#clickywarning').hide('slow');}, 10000);</script>";
				}
				add_action('admin_notices', 'clicky_warning');
				return;
			} 
		}

		function clicky_meta_box() {
			global $post;
			$options 			= clicky_get_options();
			$clicky_goal 		= get_post_meta($post->ID,'_clicky_goal',true);
			$clicky_tweetpost	= get_post_meta($post->ID,'_clicky_tweetpost',true);
			
			if ($clicky_tweetpost == '' && $post->post_type == 'post')
				$clicky_tweetpost = true;
				
			if ($options['allow_clickyme_integration']) {
				if ($post->post_status == 'publish') {
					$shorturl = clickyme_shorturl($post->ID);
					echo '<p><strong>'.__('Twitter', 'clicky').'</strong></p>';
					echo '<p>';
					_e('The Clicky.me short URL for this post is', 'clicky');
					echo '<a href="'.$shorturl.'">'.$shorturl.'</a>. <a target="_blank" href="http://twitter.com/home?status='.urlencode($post->post_title.' - '.$shorturl).'">';
					_e('Tweet this post', 'clicky');
					echo '</a></p><br/>';
				} else {
					if (!$options['auto_tweet']) {
						echo '<p><strong>'.__('Twitter', 'clicky').'</strong></p>';
						echo '<p>';
						_e('This post is not published yet, a short URL will be created for it once the post is published. Do you want it to be tweeted automatically too?', 'clicky');
						echo '<br/><br/>';
						echo '<input type="checkbox" name="clicky_tweetpost" '.checked($clicky_tweetpost,true,false).'/> '.__('Tweet post on publish', 'clicky').'</p>';
						echo '<br/>';
					}
				}
			}
			echo '<p><strong>'.__('Goal Tracking', 'clicky').'</strong></p>';
			echo '<p>';
			printf(__('Clicky can track Goals for you too, %1$sread the documentation here%2$s. To be able to track a goal on this post, you need to specify the goal ID here. Optionally, you can also provide the goal revenue.'),'<a href="http://getclicky.com/stats/goals-setup">','</a>');
			echo '</p>';
			echo '<table>';
			echo '<tr><th style="text-align:left;"><label for="clicky_goal_id">'.__('Goal ID', 'clicky').':</label></th><td><input type="text" name="clicky_goal_id" id="clicky_goal_id" value="'.$clicky_goal['id'].'"/></td></tr>';
			echo '<tr><th style="text-align:left;"><label for="clicky_goal_value">'.__('Goal Revenue', 'clicky').': &nbsp;</label></th><td><input type="text" name="clicky_goal_value" id="clicky_goal_value" value="'.$clicky_goal['value'].'"/></td></tr>';
			echo '</table>';
		}
		
		function Clicky_Admin() {
			add_action( 'admin_menu', array(&$this, 'register_settings_page') );
			add_action( 'admin_menu', array(&$this, 'register_dashboard_page') );
			
			add_filter( 'plugin_action_links', array(&$this, 'add_action_link'), 10, 2 );
			add_filter( 'ozh_adminmenu_icon', array(&$this, 'add_ozh_adminmenu_icon' ) );				
			
			add_action('admin_print_scripts', array(&$this,'config_page_scripts'));
			add_action('admin_print_styles', array(&$this,'config_page_styles'));	
			
			add_action('admin_menu', array(&$this,'meta_box'));
			add_action('wp_insert_post', array(&$this,'clicky_insert_post'));
			
			$this->clicky_admin_warnings();
		}

		function clicky_insert_post($pID) {
			global $_POST;
			$options = clicky_get_options();
			extract($_POST);
			$clicky_goal = array();
			$clicky_goal['id'] = $clicky_goal_id;
			$clicky_goal['value'] = $clicky_goal_value;
			delete_post_meta($pID,'_clicky_goal');
			add_post_meta($pID,'_clicky_goal',$clicky_goal, true);
			
			if ($options['allow_clickyme_integration'] && !$options['auto_tweet']) {
				delete_post_meta($pID,'_clicky_tweetpost');
				if (isset($clicky_tweetpost))
					add_post_meta($pID,'_clicky_tweetpost',true, true);
				else
					add_post_meta($pID,'_clicky_tweetpost',false, true);
			}
		}

		function register_dashboard_page() {
			add_dashboard_page($this->shortname.' '.__('Stats', 'clicky'), $this->shortname.' '.__('Stats', 'clicky'), $this->accesslvl, $this->hook, array(&$this,'dashboard_page'));
		}
		
		function dashboard_page() {
			$options = clicky_get_options();
?>
		<br/>
		<iframe style="margin-left: 20px; width: 850px; height: 1000px;" src="http://getclicky.com/stats/wp-iframe?site_id=<?php echo $options['site_id']; ?>&amp;sitekey=<?php echo $options['site_key']; ?>"></iframe>
<?php			
		}
		
		function config_page() {
			$options = clicky_get_options();
			
			if ( isset($_POST['submit']) ) {
				if (!current_user_can('manage_options')) die(__('You cannot edit the Clicky settings.', 'clicky'));
				check_admin_referer('clicky-config');
			
				foreach (array('site_id', 'site_key', 'admin_site_key', 'twitter_username', 'twitter_password', 'twitter_prefix') as $option_name) {
					if (isset($_POST[$option_name]))
						$options[$option_name] = $_POST[$option_name];
					else
						$options[$option_name] = '';
				}
				
				foreach (array('ignore_admin', 'track_names', 'allow_clickyme_integration','auto_tweet') as $option_name) {
					if (isset($_POST[$option_name]))
						$options[$option_name] = true;
					else
						$options[$option_name] = false;
				}
				
				if (clicky_get_options() != $options) {
					update_option('clicky', $options);
					$message = "<p>".__('Clicky settings have been updated.', 'clicky')."</p>";
				}
			}
			
			if (isset($error) && $error != "") {
				echo "<div id=\"message\" class=\"error\">$error</div>\n";
			} elseif (isset($message) && $message != "") {
				echo "<div id=\"updatemessage\" class=\"updated fade\">$message</div>\n";
				echo "<script type=\"text/javascript\">setTimeout(function(){jQuery('#updatemessage').hide('slow');}, 3000);</script>";
			}
			?>
			<div class="wrap">
				<a href="http://getclicky.com/"><div id="clicky-icon" style="background: url(<?php echo plugins_url('',__FILE__); ?>/clicky-32x32.png) no-repeat;" class="icon32"><br /></div></a>
				<h2>Clicky <?php _e("Configuration",'clicky'); ?></h2>
				<div class="postbox-container" style="width:70%;">
					<div class="metabox-holder">	
						<div class="meta-box-sortables">
							<form action="" method="post" id="clicky-conf" enctype="multipart/form-data">
								<?php
								wp_nonce_field('clicky-config');
																											
								$content = '<p style="text-align:left; margin: 0 10px; font-size: 13px; line-height: 150%;">'.sprintf(__('Go to your %1$suser homepage on Clicky%2$s and click &quot;Preferences&quot; under the name of the domain, you will find the Site ID, Site Key, Admin Site Key and Database Server under Site information.', 'clicky'),'<a href="http://getclicky.com/user/">','</a>').'</p>';

								$rows = array ();
								$rows[] = array(
											'id' => 'site_id',
											'label' => __('Site ID', 'clicky'),
											'desc' => '',
											'content' => '<input class="text" type="text" value="'.$options['site_id'].'" name="site_id" id="site_id"/>',
										);

								$rows[] = array(
											'id' => 'site_key',
											'label' => __('Site Key', 'clicky'),
											'desc' => '',
											'content' => '<input class="text" type="text" value="'.$options['site_key'].'" name="site_key" id="site_key"/>',
										);

								$rows[] = array(
											'id' => 'admin_site_key',
											'label' => __('Admin Site Key', 'clicky'),
											'desc' => '',
											'content' => '<input class="text" type="text" value="'.$options['admin_site_key'].'" name="admin_site_key" id="admin_site_key"/>',
										);

								$content .= ' '.$this->form_table($rows);
								$this->postbox('clicky_settings',__('Clicky Settings', 'clicky'), $content);

								$content = '<p style="text-align:left; margin: 0 10px; font-size: 13px; line-height: 150%;">'.sprintf(__('This plugin allows you to automatically create short URLs through the %1$sClicky.me service%2$s', 'clicky'),'<a href="http://clicky.me">','</a>,').' '.__('and tweet your post immediately when you publish it. To do that, this plugin will need your Twitter username and pass.', 'clicky').'</p>';
								
								$rows = array();
								$rows[] = array(
											'id' => 'ignore_admin',
											'label' => __('Ignore Admin users', 'clicky'),
											'desc' => __('If you are using a caching plugin, such as WP-Supercache, please ensure that you have it configured to NOT use the cache for logged in users. Otherwise, admin users <em>will still</em> be tracked.', 'clicky'),
											'content' => '<input type="checkbox" '.checked($options['ignore_admin'],true,false).' name="ignore_admin" id="ignore_admin"/>',
										);

								$rows[] = array(
											'id' => 'track_names',
											'label' => __('Track names of commenters', 'clicky'),
											'desc' => '',
											'content' => '<input type="checkbox" '.checked($options['track_names'],true,false).' name="track_names" id="track_names"/>'
										);
														
								$this->postbox('clicky_settings',__('Advanced Settings', 'clicky'), $this->form_table($rows));

								$rows = array();
								$rows[] = array(
											'id' => 'allow_clickyme_integration',
											'label' => __('Allow Clicky.me integration', 'clicky'),
											'desc' => '',
											'content' => '<input type="checkbox" '.checked($options['allow_clickyme_integration'],true,false).' name="allow_clickyme_integration" id="allow_clickyme_integration"/>',
										);

								$rows[] = array(
											'id' => 'auto_tweet',
											'label' => __('Auto Tweet', 'clicky'),
											'desc' => __('No need to check the box on each post, by checking this box, each post get\'s tweeted automatically.', 'clicky'),
											'content' => '<input type="checkbox" '.checked($options['auto_tweet'],true,false).' name="auto_tweet" id="auto_tweet"/>',
										);

								$rows[] = array(
											'id' => 'twitter_username',
											'label' => __('Twitter username', 'clicky'),
											'desc' => '',
											'content' => '<input type="text" value="'.$options['twitter_username'].'" name="twitter_username" id="twitter_username"/>'
										);

								$rows[] = array(
											'id' => 'twitter_password',
											'label' => __('Twitter password', 'clicky'),
											'desc' => '',
											'content' => '<input type="password" value="'.$options['twitter_password'].'" name="twitter_password" id="twitter_password"/>'
										);

								$rows[] = array(
											'id' => 'twitter_prefix',
											'label' => __('Prefix for Tweets', 'clicky'),
											'desc' => __('This text will be put in front of Tweets that are published when a blog post is published', 'clicky'),
											'content' => '<input type="text" class="text" value="'.$options['twitter_prefix'].'" name="twitter_prefix" id="twitter_prefix"/>'
										);
														
								$this->postbox('clickyme_integration',__('Clicky.me &amp; Twitter Integration', 'clicky'), $content .' '. $this->form_table($rows));
								

								?>
								<div class="submit">
									<input type="submit" class="button-primary" name="submit" value="<?php _e("Update Clicky Settings", 'clicky'); ?> &raquo;" />
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="postbox-container" style="width:20%;">
					<div class="metabox-holder">	
						<div class="meta-box-sortables">
							<?php
								$this->plugin_like('clicky');
								$this->plugin_support('clicky');
								$this->news(); 
							?>
						</div>
						<br/><br/><br/>
					</div>
				</div>
			</div>
<?php		
			}
	}
	$clicky_admin = new Clicky_Admin();
}

function clicky_get_options() {
	$options = get_option('clicky');
	if (!is_array($options)) {
		clicky_defaults();
	} else {
		$options['site_id'] 		= trim($options['site_id']);
		$options['site_key'] 		= trim($options['site_key']);
		$options['admin_site_key'] 	= trim($options['admin_site_key']);		
	}
	return $options;
}

function clicky_defaults() {
	$options = array(
		'site_id' 						=> '',
		'site_key' 						=> '',
		'admin_site_key' 				=> '',
		'twitter_username' 				=> '',
		'twitter_password' 				=> '',
		'twitter_prefix'				=> '',
		'auto_tweet'					=> false,
		'ignore_admin' 					=> false,
		'track_names'					=> true,
		'allow_clickyme_integration' 	=> true,
	);
	add_option('clicky',$options);
}

function clickyme_shorturl($pid) {
	$shorturl = get_post_meta($post->ID, '_clickyme_url', true);
	if (!$shorturl) {
		$options = clicky_get_options();
		if ( empty($options['site_id']) || empty($options['admin_site_key']) )
			return false;

		$res = wp_remote_get('http://clicky.me/app/api?site_id='.$options['site_id'].'&sitekey_admin='.$options['admin_site_key'].'&url='.get_permalink($pid)); 
		if ($res['response']['code'] == 200) {
			$shorturl = trim($res['body']);
			if (preg_match( '#^http://#', $shorturl )) {
				add_post_meta($post->ID,'_clickyme_url',$shorturl,true);
				return $shorturl;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	return $shorturl;
}

function publish_tweet($pid) {
	$options 	= clicky_get_options();
	if (!$options['allow_clickyme_integration'] || $options['twitter_username'] == "" || $options['twitter_password'] == "")
		return false;
	
	// Check if post has to be tweeted, if not, return false, else make sure it doesn't get tweeted twice.
	$clicky_tweetpost = get_post_meta($pid,'_clicky_tweetpost',true);

	if (!$options['auto_tweet'] && !$clicky_tweetpost) {
		return false;
	} else {
		delete_post_meta($pid,'_clicky_tweetpost');
		add_post_meta($pid,'_clicky_tweetpost',false, true);
	}
		
	$shorturl 		= clickyme_shorturl( $pid );
	if (!$shorturl) {
		// Short URL creation went wrong, make sure it tries again next time and bail.
		delete_post_meta($pid,'_clicky_tweetpost');
		add_post_meta($pid,'_clicky_tweetpost',true,true);		
		return false;
	}
	$su_length		= strlen( $shorturl );
	$status 		= trim( $options['twitter_prefix'] ).' '.get_the_title( $pid ).' -';

	// Make sure the tweet message isn't too long, so make sure the shorturl fits
	$max = 139 - $su_length;
	if ( strlen( $status ) > $max ) {
		$status = substr( 0, $max, $status );
	}
	$status .= ' '.$shorturl;

	$headers = array( 'Authorization' => 'Basic '.base64_encode($options['twitter_username'].":".$options['twitter_password']) );
	$request = new WP_Http;
	$result = $request->request( 
		'http://twitter.com/statuses/update.xml' , 
		array( 
			'method' => 'POST', 
			'body' => array('status' => $status), 
			'headers' => $headers )
		);
	return $result;
}
add_action('publish_post','publish_tweet');
add_action('publish_page','publish_tweet');

function clicky_script() {
	$options = clicky_get_options();
	
	// Bail early if current user is admin and ignore admin is true
	if( $options['ignore_admin'] && current_user_can("manage_options") ) {
		echo "\n<!-- ".__("Clicky tracking not shown because you're an administrator and you've configured Clicky to ignore administrators.", 'clicky')." -->\n";
		return;
	}
			

	// Branding
?>
<!-- Clicky Web Analytics - http://getclicky.com, WordPress Plugin by Yoast - http://yoast.com -->
<?php
		// Track commenter name if track_names is true
		if( $options['track_names'] ) 
{ ?>
<script type='text/javascript'>
	function clicky_gc( name ) {
		var ca = document.cookie.split(';');
		for( var i in ca ) {
			if( ca[i].indexOf( name+'=' ) != -1 )
				return decodeURIComponent( ca[i].split('=')[1] );
		}
		return '';
	}
	var clicky_custom_session = { username: clicky_gc( 'comment_author_<?php echo md5( get_option("siteurl") ); ?>' ) };
  	</script>
<?php 	}
	
	// Goal tracking
	if (is_singular()) {
		global $post;
		$clicky_goal = get_post_meta($post->ID,'_clicky_goal',true);
		if (is_array($clicky_goal) && !empty($clicky_goal['id'])) {
			echo '<script type="text/javascript">';
			echo 'var clicky_goal = { id: "'.trim($clicky_goal['id']).'"';
			if (isset($clicky_goal['value']) && !empty($clicky_goal['value'])) 
				echo ', revenue: "'.$clicky_goal['value'].'"';
			echo ' };';
			echo '</script>';
		}
	}
	
	// Display the script
?>
<script src="http://static.getclicky.com/js" type="text/javascript"></script>
<script type="text/javascript">clicky.init(<?php echo $options['site_id']; ?>);</script>
<noscript><p><img alt="Clicky" width="1" height="1" src="http://static.getclicky.com/<?php echo $options['site_id']; ?>ns.gif" /></p></noscript>	
<!-- End Clicky Tracking -->
<?php
}
add_action('wp_footer','clicky_script',90);

function clicky_log( $a ) {
	$options = clicky_get_options();

	if (!isset($options['site_id']) || empty($options['site_id']) || !isset($options['admin_site_key']) || empty($options['admin_site_key']))
		return;

	$type = $a['type'];
	if( !in_array( $type, array( "pageview", "download", "outbound", "click", "custom", "goal" ))) 
		$type = "pageview";

	$file = "http://static.getclicky.com/in.php?site_id=".$options['site_id']."&sitekey_admin=".$options['admin_site_key']."&type=".$type;

	# referrer and user agent - will only be logged if this is the very first action of this session
	if( $a['ref'] ) 
		$file .= "&ref=".urlencode( $a['ref'] );
		
	if( $a['ua']  ) 
		$file .= "&ua=". urlencode( $a['ua']  );

	# we need either a session_id or an ip_address...
	if( is_numeric( $a['session_id'] )) {
		$file .= "&session_id=".$a['session_id'];
	} else {
		if( !$a['ip_address'] ) 
			$a['ip_address'] = $_SERVER['REMOTE_ADDR']; # automatically grab IP that PHP gives us.
		if( !preg_match( "#^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$#", $a['ip_address'] )) 
			return false;
		$file .= "&ip_address=".$a['ip_address'];
	}

	# goals can come in as integer or array, for convenience
	if( $a['goal'] ) {
		if( is_numeric( $a['goal'] )) {
			$file .= "&goal[id]=".$a['goal'];
		} else {
			if( !is_numeric( $a['goal']['id'] )) 
				return false;
			foreach( $a['goal'] as $key => $value ) 
				$file .= "&goal[".urlencode( $key )."]=".urlencode( $value );
		}
	}

	# custom data, must come in as array of key=>values
	foreach( $a['custom'] as $key => $value ) 
		$file .= "&custom[".urlencode( $key )."]=".urlencode( $value );

	if( $type == "goal" || $type == "custom" ) {
		# dont do anything, data has already been cat'd
	} else {
		if( $type == "outbound" ) {
			if( !preg_match( "#^(https?|telnet|ftp)#", $a['href'] )) 
				return false;
		} else {
			# all other action types must start with either a / or a #
			if( !preg_match( "#^(/|\#)#", $a['href'] )) 
				$a['href'] = "/" . $a['href'];
		}
		$file .= "&href=".urlencode( $a['href'] );
		if( $a['title'] ) 
			$file .= "&title=".urlencode( $a['title'] );
	}
	return wp_remote_get( $file ) ? true:false;
}

function clicky_track_comment($commentID, $comment_status) {
	// Make sure to only track the comment if it's not spam (but do it for moderated comments).
	if ($comment_status != 'spam') {
		$comment = get_comment($commentID);
		// Only do this for normal comments, not for pingbacks or trackbacks
		if ($comment->comment_type != 'pingback' && $comment->comment_type != 'trackback') {
			clicky_log( 
				array( 
					"type" 			=> "click", 
					"href" 			=> "/wp-comments-post.php", 
					"title" 		=> __("Posted a comment",'clicky'),
					"ua"			=> $comment->comment_agent,
					"ip_address"	=> $comment->comment_author_IP,
					"custom" 		=> array(
						"username" 	=> $comment->comment_author,
						"email"		=> $comment->comment_author_email,
					)
				) 
			);
		}
	}
}
add_action('comment_post','clicky_track_comment',10,2);

?>