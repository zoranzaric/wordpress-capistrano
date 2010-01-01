<?php global $is_ajax; $is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']); if (!$is_ajax) get_header(); ?>
<?php $wptouch_settings = bnc_wptouch_get_settings(); ?>
 <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
 	<div class="post content" id="post-<?php the_ID(); ?>">
	 <div class="page">
		<div class="page-title-icon">		
			<?php
			$mypages = bnc_wp_touch_get_pages();
			
			if ( isset( $mypages[get_the_ID()]) ) {
				$icon_name = $mypages[get_the_ID()]['icon'];
				if ( file_exists( compat_get_plugin_dir( 'wptouch' ) . '/images/icon-pool/' . $icon_name ) ) {
					$image = compat_get_plugin_url( 'wptouch' ) . '/images/icon-pool/' . $icon_name;	
				} else {
					$image = compat_get_upload_url() . '/wptouch/custom-icons/' . $icon_name;
				}
				echo('<img class="pageicon" src="' . $image . '" alt="icon" />'); 
			} else {
				echo ('<img class="pageicon" src="' . compat_get_plugin_url( 'wptouch' ) . '/images/icon-pool/Default.png" alt="pageicon" />');
			}
			
			?> 
		</div>
			<h2><?php the_title(); ?></h2>
	</div>
	      
<div class="clearer"></div>
  
    <div id="entry-<?php the_ID(); ?>" class="pageentry <?php echo $wptouch_settings['style-text-size']; ?> <?php echo $wptouch_settings['style-text-justify']; ?>">
        <?php if (!is_page('archives') || !is_page('links')) { the_content(); } ?>  

<?php if (is_page('archives')) {
// If you have a page named 'Archives', the WP tag cloud will be displayed
?>
          </div>
	</div>

	<h3 class="result-text"><?php _e( "Tag Cloud", "wptouch" ); ?></h3>
		<div id="wptouch-tagcloud" class="post">
			<?php wp_tag_cloud('smallest=11&largest=18&unit=px&orderby=count&order=DESC'); ?>
		</div>
	</div>
</div>

	<h3 class="result-text"><?php _e( "Monthly Archives", "wptouch" ); ?></h3>
		<div id="wptouch-archives" class="post">
			<?php wp_get_archives(); // This will print out the default WordPress Monthly Archives Listing. ?> 
		</div>
		  
<?php } ?><!-- end if archives page-->
            
<?php if (is_page('photos')) {
// If you have a page named 'Photos', and the FlickrRSS activated and configured your photos will be displayed here.
// It will override other number of images settings and fetch 20 from the ID.
?>
	<?php if (function_exists('get_flickrRSS')) { ?>
		<div id="wptouch-flickr">
			<?php get_flickrRSS(20); ?>
		</div>
	<?php } ?>
<?php } ?><!-- end if photos page-->
		</div>
	</div>   
           		
<?php if (is_page('links')) {
// If you have a page named 'Links', a default listing of your Links will be displayed here.
?>
		</div>
	</div>          

		<div id="wptouch-links">
			<?php wp_list_bookmarks('title_li=&category_before=&category_after='); ?>
		</div>
<?php } ?><!-- end if links page-->    	
	
		<?php wp_link_pages( __('Pages in this article: ', 'wptouch'), '', 'number'); ?>

<!--If comments are enabled for pages in the WPtouch admin, and 'Allow Comments' is checked on a page-->
	<?php if (bnc_is_page_coms_enabled() && 'open' == $post->comment_status) : ?>
		<?php comments_template(); ?>
  	<?php endif; ?>
<!--end comment status-->
    <?php endwhile; ?>	

<?php else : ?>

	<div class="result-text-footer">
		<?php wptouch_core_else_text(); ?>
	</div>

 <?php endif; ?>

<!-- If it's ajax, we're not bringing in footer.php -->
<?php global $is_ajax; if (!$is_ajax) get_footer(); ?>