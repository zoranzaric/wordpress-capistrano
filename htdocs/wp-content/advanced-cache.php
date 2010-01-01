<?php
# WP SUPER CACHE 0.8.9.1
function wpcache_broken_message() {
	if ( false == strpos( $_SERVER[ 'REQUEST_URI' ], 'wp-admin' ) )
		echo "<!-- WP Super Cache is installed but broken. The path to wp-cache-phase1.php in wp-content/advanced-cache.php must be fixed! -->";
}

$supercache_location = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/wp-super-cache/wp-cache-phase1.php'; 


if (!include_once ($supercache_location)) {
	if(!@is_file($supercache_location)) {
		register_shutdown_function( 'wpcache_broken_message' );
	}
}
?>
