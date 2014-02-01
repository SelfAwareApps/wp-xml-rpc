<?php
/** 
 * WordPress XML-RPC Wrapper Class
 * 
 * Provides easy integration towards the WordPress XML-RPC API.
 *
 * @version 1.0
 * @author tormjens
 */

/**
 * The Main Class 
 */

class XMLRPC_WP_Class {

    /**
     * Object Variables
     */

    private $username;
    private $password;
    private $endpoint;
    private $blogid;
    
    private $ch;

    /**
     * Core Functions
     */

    public function __construct($username, $password, $endpoint, $blogid = 1) {

        $this->username = $username;
        $this->password = $password;
        $this->endpoint = $endpoint;
        $this->blogid = $blogid;
        
        $this->ch = curl_init($this->endpoint);
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));

    }

    private function execute($request) {
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $request);
        //Disable displaying responce
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
        
        $response = curl_exec($this->ch);
        //Helper::dump($response);
        
        $result = xmlrpc_decode($response);
        if (is_array($result) && xmlrpc_is_fault($result)) {
            throw new Exception($result['faultString'], $result['faultCode']);
        }
        else {
            return $result;
        }
    }

    /**
     * Post Functions
     * 
     * For more information about possible input fields for the differenet functions
     * see: http://codex.wordpress.org/XML-RPC_WordPress_API/Posts
     */

    public function getPost($post_id, array $fields = array()) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $post_id,
            $fields
        );
        
        $request = xmlrpc_encode_request('wp.getPost', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    public function getPosts(array $args = array(), array $fields = array()) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $args,
            $fields
        );
        
        $request = xmlrpc_encode_request('wp.getPosts', $params, array('encoding'=>'UTF-8','escaping'=>'cdata'));
        $response = $this->execute($request);

        return $response;

    }
    
    public function newPost(array $args = array()) {
        
        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $args
        );
        
        $request = xmlrpc_encode_request('wp.newPost', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);
        return $response;
    }
    
    public function editPost($post_id, array $args = array()) {
        
        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $post_id,
            $args
        );
        
        $request = xmlrpc_encode_request('wp.editPost', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);
        return $response;
    }

    public function deletePost($post_id) {
        
        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $post_id
        );
        
        $request = xmlrpc_encode_request('wp.deletePost', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);
        return $response;
    }

    public function getPostType($post_type, array $fields = array()) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $post_type,
            $fields
        );
        
        $request = xmlrpc_encode_request('wp.getPostType', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    public function getPostTypes() {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password
        );
        
        $request = xmlrpc_encode_request('wp.getPostTypes', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    public function getPostFormats($supported = true) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $supported
        );
        
        $request = xmlrpc_encode_request('wp.getPostFormats', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    public function getPostStatusList($supported = true) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password
        );
        
        $request = xmlrpc_encode_request('wp.getPostStatusList', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    /**
     * Media Functions
     *
     * For more information about possible input fields for the differenet functions
     * see: http://codex.wordpress.org/XML-RPC_WordPress_API/Media
     */

    public function getMediaItem($attachment) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $attachment

        );
        
        $request = xmlrpc_encode_request('wp.getMediaItem', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    public function getMediaLibrary(array $filter) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $filter

        );
        
        $request = xmlrpc_encode_request('wp.getMediaLibrary', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    public function uploadFile($image) {
        //Send binary data and label as Base64
        $bits = file_get_contents($image["fullpath"]);
        xmlrpc_set_type($bits, 'base64');

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            array(
                'name' => $image["filename"],
                'type' => $image["type"],
                'bits' => $bits,
                'overwrite' => true,
            ),
        );

        $request = xmlrpc_encode_request('wp.uploadFile', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));

        $response = $this->execute($request);
        return $response;
    }

    /**
     * Taxonomy Functions
     *
     * For more information about possible input fields for the differenet functions
     * see: http://codex.wordpress.org/XML-RPC_WordPress_API/Taxonomies
     */

    public function getTaxonomies() {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password
        );
        
        $request = xmlrpc_encode_request('wp.getTaxonomies', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;
    }

    public function getTaxonomy($taxonomy) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $taxonomy
        );
        
        $request = xmlrpc_encode_request('wp.getTaxonomy', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;
    }

    public function getTerms($taxonomy, array $args = array()) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $taxonomy,
            $args
        );
        
        $request = xmlrpc_encode_request('wp.getTerms', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;
    }

    public function getTerm($term_id, $taxonomy) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $taxonomy,
            $term_id
        );
        
        $request = xmlrpc_encode_request('wp.getTerm', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;
    }

    public function newTerm($name, $taxonomy, $slug = '', $description = '', $parent = 0) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            array(
                $name,
                $taxonomy,
                $slug,
                $description,
                $parent
            )
        );
        
        $request = xmlrpc_encode_request('wp.newTerm', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    } 

    public function editTerm($term_id, $taxonomy, $name, $slug = '', $description = '', $parent = 0) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $term_id,
            array(
                $name,
                $taxonomy,
                $slug,
                $description,
                $parent
            )
        );
        
        $request = xmlrpc_encode_request('wp.editTerm', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    } 

    public function deleteTerm($term_id, $taxonomy) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $term_id,
            $taxonomy
        );
        
        $request = xmlrpc_encode_request('wp.deleteTerm', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    } 

    /**
     * Comment Functions
     *
     * For more information about possible input fields for the differenet functions
     * see: http://codex.wordpress.org/XML-RPC_WordPress_API/Comments
     */

    public function getCommentCount($post_id) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $post_id
        );
        
        $request = xmlrpc_encode_request('wp.getCommentCount', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    public function getComment($comment_id) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $comment_id
        );
        
        $request = xmlrpc_encode_request('wp.getComment', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    public function getComments(array $filter) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $filter
        );
        
        $request = xmlrpc_encode_request('wp.getComments', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    public function newComment($post_id, $comment_parent = 0, $content, $author, $author_url = '', $author_email) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $post_id,
            array(
                $comment_parent,
                $content,
                $author,
                $author_url,
                $author_email
            )
        );
        
        $request = xmlrpc_encode_request('wp.newComment', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    public function editComment($comment_id, $comment_parent = 0, $date, $content, $author, $author_url = '', $author_email, $status = 'hold') {

        if ($date == Null) {
            $comment_date = date("Ymd\TH:i:s", time());
        }
        else {
            $comment_date = $date;
        }
        xmlrpc_set_type($comment_date, 'datetime');

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $comment_id,
            array(
                $status,
                $comment_date,
                $content,
                $author,
                $author_url,
                $author_email
            )
        );
        
        $request = xmlrpc_encode_request('wp.editComment', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    public function deleteComment($comment_id) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $comment_id
        );
        
        $request = xmlrpc_encode_request('wp.deleteComment', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    public function getCommentStatusList() {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password
        );
        
        $request = xmlrpc_encode_request('wp.getCommentStatusList', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    /**
     * Options Functions
     *
     * For more information about possible input fields for the differenet functions
     * see: http://codex.wordpress.org/XML-RPC_WordPress_API/Options
     */

    public function getOptions(array $options = array()) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $options
        );
        
        $request = xmlrpc_encode_request('wp.getOptions', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    public function setOptions(array $options = array()) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $options
        );
        
        $request = xmlrpc_encode_request('wp.setOptions', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    /**
     * Users Functions
     *
     * For more information about possible input fields for the differenet functions
     * see: http://codex.wordpress.org/XML-RPC_WordPress_API/Users
     */

    public function getUsersBlogs() {

        $params = array(
            $this->username,
            $this->password
        );
        
        $request = xmlrpc_encode_request('wp.getUsersBlogs', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    public function getUser($user_id, array $fields) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $user_id,
            $fields
        );
        
        $request = xmlrpc_encode_request('wp.getUser', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    public function getUsers(array $filter = array()) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $filter
        );
        
        $request = xmlrpc_encode_request('wp.getUsers', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    public function getProfile(array $fields) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $fields
        );
        
        $request = xmlrpc_encode_request('wp.getProfile', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    public function editProfile(array $fields) {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password,
            $fields
        );
        
        $request = xmlrpc_encode_request('wp.editProfile', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

    public function getAuthors() {

        $params = array(
            $this->blogid,
            $this->username,
            $this->password
        );
        
        $request = xmlrpc_encode_request('wp.getAuthors', $params, array('encoding'=>'UTF-8','escaping'=>'markup'));
        $response = $this->execute($request);

        return $response;

    }

}