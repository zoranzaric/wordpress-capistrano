<?php
	define('DOING_AJAX',true);

	require('../../../wp-load.php');
	
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

	$awesmapikey  	= get_option('sociable_awesmapikey'); 
	if (!empty($awesmapikey)) { 
		$key = '&api_key=' . $awesmapikey; 
	} else { 
		$key = ''; 
	}
	$destination 	= str_replace('TARGET', 'AWESM_TARGET', urldecode( $_GET['d'] ) );	
	$channel		= urldecode( $_GET['c'] );
	$permalink		= urldecode( $_GET['t'] );

	if ($_GET['dir']) {
		$direct		= '&direct=true';
	} else {
		$direct		= '';
	}
	
	if ($_GET['p']) {
		// if the page was arrived at through an awe.sm URL, make that the parent
		$parent = $_GET['p'];
		$parentargument = '&parent_awesm=' . $parent;
	} else {
		// otherwise, there is no parent
		$parentargument = '';
	}
	
	$url = 'http://create.awe.sm/url/share?&version=1'.$key.'&share_type='.urlencode($channel).'&create_type=sociable-wordpress&target='.urlencode($permalink).'&destination='.urlencode($destination).$direct.$parentargument;
	
	$url = str_replace("+","%20",$url);
	header("Location: ".$url);
?>