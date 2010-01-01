<div id="footer">

	<center>
		<div id="wptouch-switch-link">
			<?php wptouch_core_footer_switch_link(); ?>
		</div>
	</center>
	
	<p><?php _e( "All content Copyright &copy;", "wptouch" ); ?> <?php $str = bnc_get_header_title(); echo stripslashes($str); ?></p>
	<p><?php _e( 'Powered by', 'wptouch' ); ?> <a href="http://www.wordpress.org/"><?php _e( 'WordPress', 'wptouch' ); ?></a> <?php _e( '+', 'wptouch' ); ?> <a href="http://www.wptouch.com"><?php WPtouch(); ?></a></p>
	<?php if ( !bnc_wptouch_is_exclusive() ) { wp_footer(); } ?>
</div>

<?php wptouch_get_stats(); 
// WPtouch theme designed and developed by Dale Mugford and Duane Storey for BraveNewCode.com
// If you modify it for yourself, please keep the link credit *visible* in the footer (and keep the WordPress credit, too!) that's all we ask folks.
?>
</body>
</html>