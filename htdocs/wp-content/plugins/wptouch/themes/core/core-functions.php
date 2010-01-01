<?php 
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// WPtouch Core Header Functions
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function wptouch_core_header_enqueue() {
	$version = get_bloginfo('version'); 
		if (!bnc_wptouch_is_exclusive()) { 
		wp_enqueue_script('wptouch-core', '' . compat_get_plugin_url( 'wptouch' ) . '/themes/core/core.js', array('jquery'),'1.9' );		
		wp_head(); 

		} elseif (bnc_wptouch_is_exclusive()) { 
		echo "<script src='" . get_bloginfo('wpurl') . "/wp-includes/js/jquery/jquery.js' type='text/javascript' charset='utf-8'></script>\n";
		echo "<script src='" . compat_get_plugin_url( 'wptouch' ) . "/themes/core/core.js' type='text/javascript' charset='utf-8'></script>\n"; 
		 }
	}
  
function wptouch_core_header_home() {
	if (bnc_is_home_enabled()) {
		echo sprintf(__( "%sHome%s", "wptouch" ), '<li><a href="' . get_bloginfo('home') . '"><img src="' . bnc_get_title_image() . '" alt=""/>','</a></li>');
	}
}
  
function wptouch_core_header_pages() {
	$pages = bnc_wp_touch_get_pages();
	global $blog_id;
	foreach ($pages as $p) {
		if ( file_exists( compat_get_plugin_dir( 'wptouch' ) . '/images/icon-pool/' . $p['icon'] ) ) {
			$image = compat_get_plugin_url( 'wptouch' ) . '/images/icon-pool/' . $p['icon'];	
		} else {
		$image = compat_get_upload_url() . '/wptouch/custom-icons/' . $p['icon'];
	}
		echo('<li><a href="' . get_permalink($p['ID']) . '"><img src="' . $image . '" alt="icon" />' . $p['post_title'] . '</a></li>'); 
	}
  }
 
function wptouch_core_header_rss() {
	if (bnc_is_rss_enabled()) {
		echo sprintf(__( "%sRSS Feed%s", "wptouch" ), '<li><a href="' . get_bloginfo('rss2_url') . '"><img src="' . compat_get_plugin_url( 'wptouch' ) . '/images/icon-pool/RSS.png" alt="" />','</a></li>');
	}
}

function wptouch_core_header_email() {
	if (bnc_is_email_enabled()) {
		echo sprintf(__( "%sE-Mail%s", "wptouch" ), '<li><a href="mailto:' . get_bloginfo('admin_email') . '"><img src="' . compat_get_plugin_url( 'wptouch' ) . '/images/icon-pool/Mail.png" alt="" />','</a></li>');
	}
} 
  
function wptouch_core_header_check_use() {
	if (false && function_exists('bnc_is_iphone') && !bnc_is_iphone()) {
		echo '<div class="content post">';
		echo sprintf(__( "%sWarning%s", "wptouch" ), '<a href="#" class="h2">','</a>');
		echo '<div class="mainentry">';
		echo __( "Sorry, this theme is only meant for use with WordPress on certain smartphones.", "wptouch" );
		echo '</div></div>';
		echo '' .get_footer() . '';
		echo '</body>';
	die; 
	} 
}

function wptouch_core_header_styles() {
	include('core-styles.php' );
}

function wptouch_agent($browser) {
	$useragent = $_SERVER['HTTP_USER_AGENT'];
		return strstr($useragent,$browser);
	}

function wptouch_twitter_link() {
	if(wptouch_agent("iphone") || wptouch_agent("ipod") || wptouch_agent("aspen") != FALSE) {
		echo '<li><a href="javascript:(function(){var%20f=false,t=true,a=f,b=f,u=\'\',w=window,d=document,g=w.open(),p,linkArr=d.getElementsByTagName(\'link\');for(var%20i=0;i%3ClinkArr.length&&!a;i++){var%20l=linkArr[i];for(var%20x=0;x%3Cl.attributes.length;x++){if(l.attributes[x].nodeName.toLowerCase()==\'rel\'){p=l.attributes[x].nodeValue.split(\'%20\');for(y=0;y%3Cp.length;y++){if(p[y]==\'short_url\'||p[y]==\'shorturl\'||p[y]==\'shortlink\'){a=t;}}}if(l.attributes[x].nodeName.toLowerCase()==\'rev\'&&l.attributes[x].nodeValue==\'canonical\'){a=t;}if(a){u=l.href;}}}if(a){go(u);}else{var%20h=d.getElementsByTagName(\'head\')[0]||d.documentElement,s=d.createElement(\'script\');s.src=\'http://api.bit.ly/shorten?callback=bxtShCb&longUrl=\'+encodeURIComponent(window.location.href)+\'&version=2.0.1&login=amoebe&apiKey=R_60a24cf53d0d1913c5708ea73fa69684\';s.charSet=\'utf-8\';h.appendChild(s);}bxtShCb=function(data){var%20rs,r;for(r%20in%20data.results){rs=data.results[r];break;}go(rs[\'shortUrl\']);};function%20go(u){return%20g.document.location.href=(\'http://twitter.com/home/?status=\'+encodeURIComponent(document.title+\'%20\'+u));}})();" id="otweet"></a></li>';
	} else {
		echo '<li><a href="javascript:return false;" onclick="wptouch_toggle_twitter();" id="otweet"></a></li>';
	}
}

function wptouch_tags_link() {
	if(wptouch_agent("iphone") || wptouch_agent("ipod") || wptouch_agent("aspen") != FALSE) {
	} else {
		echo '<a href="#head-tags">' . __( "Tags", "wptouch" ) . '</a>';
	}
}

function wptouch_cats_link() {
	if(wptouch_agent("iphone") || wptouch_agent("ipod") || wptouch_agent("aspen") != FALSE) {
	} else {
		echo '<a href="#head-cats">' . __( "Categories", "wptouch" ) . '</a>';
	}
}

function bnc_get_ordered_cat_list() {
	// We created our own function for this as wp_list_categories doesn't make the count linkable

	global $table_prefix;
	global $wpdb;

	$sql = "select * from " . $table_prefix . "term_taxonomy inner join " . $table_prefix . "terms on " . $table_prefix . "term_taxonomy.term_id = " . $table_prefix . "terms.term_id where taxonomy = 'category' order by count desc";	
	$results = $wpdb->get_results( $sql );
	foreach ($results as $result) {
		echo "<li><a href=\"" . get_category_link( $result->term_id ) . "\">" . $result->name . " (" . $result->count . ")</a></li>";
	}

}

  
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// WPtouch Core Body Functions
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  

function wptouch_core_body_background() {
	$wptouch_settings = bnc_wptouch_get_settings();
	echo $wptouch_settings['style-background'];
  }
  
function wptouch_core_body_sitetitle() {  
	$str = bnc_get_header_title(); 
	echo stripslashes($str);  
  }

function wptouch_core_body_result_text() {  
	global $is_ajax; if (!$is_ajax) {
			if (is_search()) {
				echo sprintf( __("Search results &rsaquo; %s", "wptouch"), get_search_query() );
			} if (is_category()) {
				echo sprintf( __("Categories &rsaquo; %s", "wptouch"), single_cat_title("", false));
			} elseif (is_tag()) {
				echo sprintf( __("Tags &rsaquo; %s", "wptouch"), single_tag_title("", false));
			} elseif (is_day()) {
				echo sprintf( __("Archives &rsaquo; %s", "wptouch"),  get_the_time('F jS, Y'));
			} elseif (is_month()) {
				echo sprintf( __("Archives &rsaquo; %s", "wptouch"),  get_the_time('F, Y'));
			} elseif (is_year()) {
				echo sprintf( __("Archives &rsaquo; %s", "wptouch"),  get_the_time('Y'));
		}
	}
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// WPtouch Core Footer Functions
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  

function wptouch_core_else_text() {	
	 global $is_ajax; if (($is_ajax) && !is_search()) {
		echo '' . __( "No more entries to display.", "wptouch" ) . '';
	 } elseif (is_search() && ($is_ajax)) {
		echo '' . __( "No more search results to display.", "wptouch" ) . '';
	 } elseif (is_search() && (!$is_ajax)) {
	 	echo '<div style="padding-bottom:127px">' . __( "No search results results found.", "wptouch" ) . '<br />' . __( "Try another query.", "wptouch" ) . '</div>';
	 } else {
	  echo '<div class="post">
	  	<h2>' . __( "404 Not Found", "wptouch" ) . '</h2>
	  	<p>' . __( "The page or post you were looking for is missing or has been removed.", "wptouch" ) . '</p>
	  </div>';
	}
}

function wptouch_core_footer_switch_link() {	
	$switch_url = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

echo '<script type="text/javascript">function switch_delayer() { window.location = "' . get_bloginfo('siteurl') . '/?theme_view=normal&wptouch_redirect=' . $switch_url . '"}</script>';
echo '' . __( "Mobile Theme", "wptouch" ) . ' <a id="switch-link" onclick="wptouch_switch_confirmation(); return false;" href="#"></a>';
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// WPtouch Standard Functions
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
  
// Check if certain plugins are active
function wptouch_is_plugin_active($plugin_filename) {
	$plugins = get_option('active_plugins');
		if( !is_array($plugins) ) settype($plugins,'array');			
		return ( in_array($plugin_filename, $plugins) ) ;
}

//Filter out pingbacks and trackbacks
add_filter('get_comments_number', 'comment_count', 0);
function comment_count( $count ) {
	global $id;
	$comments = get_approved_comments($id);
	$comment_count = 0;
	foreach($comments as $comment){
		if($comment->comment_type == ""){
			$comment_count++;
		}
	}
	return $comment_count;
}

// Add 'Delete | Spam' links in comments for logged in admins
 function wptouch_moderate_comment_link($id) {  
	  if (current_user_can('edit_post')) {  
     echo '<a href="' . admin_url("comment.php?action=editcomment&c=$id") . '">' . __('edit') . '</a>';  
     echo '<a href="' . admin_url("comment.php?action=cdc&c=$id") . '">' . __('del') . '</a>';  
     echo '<a href="' . admin_url("comment.php?action=cdc&dt=spam&c=$id") . '">' . __('spam') . '</a>';  
   }  
 }  

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// WPtouch Filters
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
remove_filter('the_excerpt', 'do_shortcode');   
remove_filter('the_content', 'do_shortcode');

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// WPtouch WP Thumbnail Support
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$version = bnc_get_wp_version();
if ($version >= 2.9) {
add_theme_support( 'post-thumbnails', array( 'post' ) ); // Add it for posts
set_post_thumbnail_size( 50, 50, true ); // 50 pixels wide by 50 pixels tall, hard crop mode
}
?>