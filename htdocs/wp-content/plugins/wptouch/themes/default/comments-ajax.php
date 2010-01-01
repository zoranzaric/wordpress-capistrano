<?php
// This relative path will find what we need... kinda dirty, but it's a failsafe
require_once( dirname(__FILE__) . '../../../../../../wp-load.php' ); 
// Make sure the server returns the fact that this is a real file and it exists, even though its outside WordPress
header("HTTP/1.1 200 OK");

// AJAX COMMENTS
//No need to modify this file, it works under all installations

global $comment, $comments, $post, $wpdb, $user_ID, $user_identity, $user_email, $user_url;
function fail($s) {
	header('HTTP/1.0 500 Internal Server Error');
	echo $s;
	exit;
  }
foreach ($_GET as $k => $v) {
	$_GET[$k] = urldecode($v);
  }

$comment_post_ID = (int)$_GET['comment_post_ID'];
$post_status = $wpdb->get_var("SELECT comment_status FROM $wpdb->posts WHERE ID = '$comment_post_ID'");
if (empty($post_status)) {
	do_action('comment_id_not_found', $comment_post_ID);
	fail( __('The post you are trying to comment on does not curently exist in the database.', 'wptouch') );
  } elseif ('closed' == $post_status) {
	do_action('comment_closed', $comment_post_ID);
	fail(__('Sorry, comments are closed for this item.', 'wptouch'));
  }

$comment_author = trim($_GET['author']);
$comment_author_email = trim($_GET['email']);
$comment_author_url = trim($_GET['url']);
$comment_content = trim($_GET['comment']);

// If the user is logged in
get_currentuserinfo();
	if ($user_ID) : 
		$comment_author = addslashes($user_identity);
		$comment_author_email = addslashes($user_email);
		$comment_author_url = addslashes($user_url);
	else : if 
		(get_option('comment_registration'))
		fail(__('Sorry, you must be logged in to post a comment.', 'wptouch'));
	endif;

$comment_type = '';
if (get_settings('require_name_email') && !$user_ID) {
	if (6 > strlen($comment_author_email) || '' == $comment_author)
		fail(__('Error: please fill in the required fields', 'wptouch'));
	elseif (!is_email($comment_author_email))
		fail(__('Error: please enter a valid email address.', 'wptouch')); }
	if ('' == $comment_content)
		fail(__('Error: please type something in the comment area.', 'wptouch'));
	$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'user_ID');
	$new_comment_ID = wp_new_comment($commentdata);
	if (!$user_ID) : 
		setcookie('comment_author_' . COOKIEHASH, stripslashes($comment_author), time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
		setcookie('comment_author_email_' . COOKIEHASH, stripslashes($comment_author_email), time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
		setcookie('comment_author_url_' . COOKIEHASH, stripslashes($comment_author_url), time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
	endif;

$comment = $wpdb->get_row("SELECT * FROM {$wpdb->comments} WHERE comment_ID = " . $new_comment_ID);
$post->comment_status = $wpdb->get_var("SELECT comment_status FROM {$wpdb->posts} WHERE ID = {$comment_post_ID}");
	ob_start();
		$comments = array($comment);
			include(TEMPLATEPATH . '/comments.php');
		$commentout = ob_get_clean();
			preg_match('#<li(.*?)>(.*)</li>#ims', $commentout, $matches);
		echo "<li id=\"the-new-comment\" style=\"display:none\"" . $matches[1] . ">" . $matches[2] . "</li>";
?>