<?php
/*
Plugin Name: Category Expander
Plugin URI: http://outthinkgroup.com/category-expander
Description: This plugin allows WordPress Admins to show only a selected few categories on the sidebar and hide the rest from view until visitor clicks "See All".  Useful for sites that have lots of categories.
Version: 0.9
Author: Joseph Hinson and Tim Grahl
Author URI: http://outthinkgroup.com

    Copyright 2009 - Out:think Group  (email : joseph@outthinkgroup.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('admin_menu', 'cat_expander_menu');
add_action( 'admin_init', 'ce_register_settings' );
add_action('init', 'ce_init_method');
add_action('widgets_init', 'ce_init_widget');

function ce_init_method() {
    wp_enqueue_script('jquery');            
}    

function ce_register_settings() { 
		register_setting( 'ce_catID_list', 'ce_catIDs', 'ce_catID_validate' );
}

function ce_init_widget() {
	register_widget( 'CE_Widget' );
} 

function cat_expander_menu() {
	add_options_page('Category Expander Options', 'Category Expander', '8', 'category_expander.php', 'category_expander_options');
	
}

function category_expander_options() { ?>

	<div class="wrap">
		<style type="text/css" media="screen">
			.category-list li {
				float: left;
				width: 170px;
				margin-right: 30px;

			}
			.category-list li input {
				float: left;
				margin-right: 4px;
			}
			.clr {
				clear: both;
			}
		</style>
	<h2>Category Expander</h2>
	<p>Please choose which categories you want to initially display.</p>
	<form method="post" action="options.php">
		<?php settings_fields('ce_catID_list'); ?>
		<?php $options = get_option('ce_catIDs');?>
		<div class="checkboxes">
			<ul class="category-list">
				<?php
				// Displaying list of checkboxes from which to select
				$categories = get_categories('hide_empty=0'); 
				foreach ($categories as $cat) {
				?>
				<li>
					<label for="ce_catIDs[<?php echo $cat->cat_ID; ?>]"><?php echo $cat->cat_name; ?></label>
					<input type="checkbox" name="ce_catIDs[<?php echo $cat->cat_ID; ?>]" id="ce_catIDs[<?php echo $cat->cat_ID; ?>]" value="1" <?php if($options[$cat->cat_ID] == 1) : checked('1', $options[$cat->cat_ID]); endif; ?> />
				</li>
				 <?php } ?>
			</ul>
			<div class="clr"></div>
		</div>
		
		<input type="hidden" name="action" value="update" />
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>	

<?php } 

function ce_catID_validate() {
	$categories = get_categories('hide_empty=0'); 
	foreach ($categories as $cat) {
		$input[$cat->cat_ID] = ( $_POST['ce_catIDs'][$cat->cat_ID] == 1 ? 1 : 0 );
	}
	return $input;
}

function ce_display_categories()
{
	$ce_catIDs = get_option('ce_catIDs');
	$master_counter = count($ce_catIDs);
	$counter = 0;
	if(!empty($ce_catIDs)) :
		foreach($ce_catIDs as $key => $value) :
			if($value == 1) :
				if($counter==0) :
					$catstr = $key;
				else :
					$catstr .= ','.$key;
				endif;
				$counter++;
			endif;
		endforeach;
	endif;
	?>
	<ul id="ce_widget_ul">
		<?php wp_list_categories('title_li=&include='.$catstr);	?>
		<?php
		if(!empty($catstr)) {
			if($master_counter <> $counter) {
			?>
			<li id="ce_seeall_li"><a href="#" onclick="jQuery('#ce_seeall_li').hide();jQuery('.ce_hidden_li').fadeIn(500);return false;">see all</a></li>
			<?php 
			}
			$ce_hidden_cats = get_categories('exclude='.$catstr); 
			foreach($ce_hidden_cats as $ce_cat) {
				?>
				<li class="cat-item cat-item-<?php echo $ce_cat->cat_ID; ?> ce_hidden_li" style="display:none;"><a href="<?php echo get_category_link($ce_cat->cat_ID); ?>" title="View all posts filed under <?php echo $ce_cat->name; ?>"><?php echo $ce_cat->name; ?></a>
				<?php
			}
		}
		?>
	</ul>
	<div class="clear">&nbsp;</div>
	<?php
}

class CE_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function CE_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'category-expander', 'description' => __('Allows you to pick which categories show on the sidebar (and which are hidden).', 'category-expander') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'ce-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'ce-widget', __('Category Expander Widget', 'Categories'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		/* Display name from widget settings if one was input. */
		
		ce_display_categories();
		
		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Categories', 'category-expander'));
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		<p>Pick your categories on the <a href="options-general.php?page=category-expander">settings page</a>.</p>
	<?php
	}
}

function ce_plugin_action_links($links, $file) {
    $plugin_file = basename(__FILE__);
    if (basename($file) == $plugin_file) {
        $settings_link = '<a href="options-general.php?page='.$plugin_file.'">'.__('Settings', 'twitter-tools').'</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}
add_filter('plugin_action_links', 'ce_plugin_action_links', 10, 2);
?>