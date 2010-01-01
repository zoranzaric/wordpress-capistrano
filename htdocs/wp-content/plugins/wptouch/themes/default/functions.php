<?php 
include( dirname(__FILE__) . '/../core/core-functions.php' ); 

//---------------- Custom Drop-Down Tags Function ----------------// 
//  function dropdown_tag_cloud( $args = '' ) {
//  	$defaults = array(
//  		'smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 45,
//  		'format' => 'flat', 'orderby' => 'name', 'order' => 'ASC',
//  		'exclude' => '', 'include' => ''
//  	);
//  	$args = wp_parse_args( $args, $defaults );
//  
//  	$tags = get_tags( array_merge($args, array('orderby' => 'count', 'order' => 'DESC')) ); // Always query top tags
//  
//  	if ( empty($tags) )
//  		return;
//  
//  	$return = core_header_tag_cloud( $tags, $args ); // Here's where those top tags get sorted according to $args
//  	if ( is_wp_error( $return ) )
//  		return false;
//  	else
//  		echo apply_filters( 'dropdown_tag_cloud', $return, $args );
//  }
//  
//  function core_header_tag_cloud( $tags, $args = '' ) {
//  	global $wp_rewrite;
//  	$defaults = array(
//  		'smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 45,
//  		'format' => 'flat', 'orderby' => 'name', 'order' => 'ASC'
//  	);
//  	$args = wp_parse_args( $args, $defaults );
//  	extract($args);
//  
//  	if ( !$tags )
//  		return;
//  	$counts = $tag_links = array();
//  	foreach ( (array) $tags as $tag ) {
//  		$counts[$tag->name] = $tag->count;
//  		$tag_links[$tag->name] = get_tag_link( $tag->term_id );
//  		if ( is_wp_error( $tag_links[$tag->name] ) )
//  			return $tag_links[$tag->name];
//  		$tag_ids[$tag->name] = $tag->term_id;
//  	}
//  
//  	$min_count = min($counts);
//  	$spread = max($counts) - $min_count;
//  	if ( $spread <= 0 )
//  		$spread = 1;
//  	$font_spread = $largest - $smallest;
//  	if ( $font_spread <= 0 )
//  		$font_spread = 1;
//  	$font_step = $font_spread / $spread;
//  
//  	// SQL cannot save you; this is a second (potentially different) sort on a subset of data.
//  	if ( 'name' == $orderby )
//  		uksort($counts, 'strnatcasecmp');
//  	else
//  		asort($counts);
//  
//  	if ( 'DESC' == $order )
//  		$counts = array_reverse( $counts, true );
//  
//  	$a = array();
//  
//  	$rel = ( is_object($wp_rewrite) && $wp_rewrite->using_permalinks() ) ? ' rel="tag"' : '';
//  
//  	foreach ( $counts as $tag => $count ) {
//  		$tag_id = $tag_ids[$tag];
//  		$tag_link = clean_url($tag_links[$tag]);
//  		$tag = str_replace(' ', '&nbsp;', wp_specialchars( $tag ));
//  		$a[] = "\t<option value='$tag_link'>$tag ($count)</option>";
//  	}
//  
//  	switch ( $format ) :
//  	case 'array' :
//  		$return =& $a;
//  		break;
//  	case 'list' :
//  		$return = "<ul class='wp-tag-cloud'>\n\t<li>";
//  		$return .= join("</li>\n\t<li>", $a);
//  		$return .= "</li>\n</ul>\n";
//  		break;
//  	default :
//  		$return = join("\n", $a);
//  		break;
//  	endswitch;
//  
//  	return apply_filters( 'core_header_tag_cloud', $return, $tags, $args );
//  }

//---------------- Custom Exclude Cats Function ----------------//
function exclude_category($query) {
	$cats = wptouch_excluded_cats();
	$icats = explode( ",", $cats );
	$new_cats = array();
	foreach( $icats as $icat ) {
		$new_cats[] = "-" . $icat;
}
	$cats = implode( ",",  $new_cats );
	
	if ( $query->is_home ) {
	$query->set('cat', $cats);
	}
return $query;
}

add_filter('pre_get_posts', 'exclude_category');


//---------------- Custom Excerpts Function ----------------//
function wptouch_trim_excerpt($text) {
	$raw_excerpt = $text;
	if ( '' == $text ) {
		$text = get_the_content('');
		$text = strip_shortcodes( $text );
		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = strip_tags($text);
		$excerpt_length = apply_filters('excerpt_length', 30);
		$words = explode(' ', $text, $excerpt_length + 1);
		if (count($words) > $excerpt_length) {
			array_pop($words);
			array_push($words, '...');
			$text = implode(' ', $words);
			$text = force_balance_tags( $text );
		}
	}
	return apply_filters('wptouch_trim_excerpt', $text, $raw_excerpt);
}


//---------------- Custom Time Since Function ----------------//

function time_since($older_date, $newer_date = false)
	{
	// array of time period chunks
	$chunks = array(
//	array(60 * 60 * 24 * 365 , 'yr'),
	array(60 * 60 * 24 * 30 , 'mo'),
	array(60 * 60 * 24 * 7, 'wk'),
	array(60 * 60 * 24 , 'day'),
	array(60 * 60 , 'hr'),
	array(60 , 'min'),
	);
	
	// $newer_date will equal false if we want to know the time elapsed between a date and the current time
	// $newer_date will have a value if we want to work out time elapsed between two known dates
	$newer_date = ($newer_date == false) ? (time()+(60*60*get_settings("gmt_offset"))) : $newer_date;
	
	// difference in seconds
	$since = $newer_date - $older_date;
	
	// we only want to output two chunks of time here, eg:
	// x years, xx months
	// x days, xx hours
	// so there's only two bits of calculation below:

	// step one: the first chunk
	for ($i = 0, $j = count($chunks); $i < $j; $i++)
		{
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];

		// finding the biggest chunk (if the chunk fits, break)
		if (($count = floor($since / $seconds)) != 0)
			{
			break;
			}
		}

	// set output var
	$output = ($count == 1) ? '1 '.$name : "$count {$name}s";

	// step two: the second chunk
	if ($i + 1 < $j)
		{
		$seconds2 = $chunks[$i + 1][0];
		$name2 = $chunks[$i + 1][1];
		
		if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0)
			{
			// add to output var
			$output .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
			}
		}
	
	return $output;
	}

remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'wptouch_trim_excerpt');
?>