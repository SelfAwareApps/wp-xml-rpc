<?php
/** 
 * Example of WordPress XML-RPC Wrapper Class
 *
 * @version 1.0
 * @author tormjens
 */

// Require the class
require_once 'wp-xml-rpc.php';

// Credentials
$username = 'myusername';
$password = 'mypassword';
$url = 'http://example.com/xmlrpc.php';

// Initate the class
$wordpress = new XMLRPC_WP_Class($username, $password, $url);

// Get post id and titles of all posts
$posts = $wordpress->getPosts(
	array(
		'post_type' => 'post' // only return posts from the post type post
	),
	array(
		'post_id', 'post_title' // the fields to return
	) 
);

var_dump($posts);


?>