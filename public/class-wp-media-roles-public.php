<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       5nines.com
 * @since      1.0.0
 *
 * @package    Wp_Media_Roles
 * @subpackage Wp_Media_Roles/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Media_Roles
 * @subpackage Wp_Media_Roles/public
 * @author     Matthew Kirk <mkirk@5nines.com>
 */
class Wp_Media_Roles_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * A wrapper around the Wordpress Plugin API, so we can test it.
     *
     * @since    1.0.0
     * @access   private
     * @var      WordpressPluginApi
     */
    private $wordpressApi;
        
    private $phpApi;
        
    private $membersApi;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( 
            $plugin_name, 
            $version, 
            wpapi\v1\WordpressPluginApi $wordpressApi,
            PhpApi $phpApi,
            MembersApi $membersApi) 
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->wordpressApi = $wordpressApi;
        $this->phpApi = $phpApi;
        $this->membersApi = $membersApi;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
		 * defined in Wp_Media_Roles_Loader as all of the hooks are defined
         * in that particular class.
         *
		 * The Wp_Media_Roles_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-media-roles-public.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wp_Media_Roles_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wp_Media_Roles_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-media-roles-public.js', array( 'jquery' ), $this->version, false );

    }
    
    public function init()
    {
        try 
        {
            $this->doMediaRolePermissions($this->wordpressApi, $this->phpApi, $this->membersApi);
        }
        catch (Exception $e)
        {

        }
    }
    
    public function members_enable_attachment_content_permissions($enable = false)
    {
        return true;
    }
    
    // TODO: When "Members" plugin not activated and fail-secure is true
    // then add note to public display.
    
    // TODO: move all code below to new class.
    
    public function doMediaRolePermissions(wpapi\v1\WordpressPluginApi $wordpressApi, PhpApi $phpApi, MembersApi $membersApi)
    {      
        $requested_url = $phpApi->filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);

        $GET_FILE = $this->getValidPathFromUrl($requested_url, $phpApi);

        $post = $this->getMediaPostByPath($GET_FILE);

        $openInBrowser = $this->getOption("open-in-browser");

        if ($this->hasPermissionToViewMedia($wordpressApi, $membersApi, $post))
        {
            $this->redirectToMedia($phpApi, $GET_FILE, $openInBrowser);
        }
        else
        {
            /* Chrome will cache the redirect for logged-in users if we aren't careful */
//            $wordpressApi->wp_redirect("/?attachment_id=".$post->ID);
            header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            header('Location:'."/?attachment_id=".$post->ID, true, 303);
            exit;
        }
        $phpApi->___exit();
    }   

    public function pluginDependenciesExist($exclusionOption, MembersApi $membersApi)
    {        
        if ($exclusionOption === "members")
        {
            return $this->membersPluginIsEnabled($membersApi);
        }
    
        return true;
    }
    
    public function membersPluginIsEnabled($membersApi)
    {
        try
        {
            if (!$membersApi->is_active()) return false;
            
            if (!$membersApi->is_synced()) return false;
        }
        catch(Exception $e)
        {
            return false;
        }
        
        return true;
    }

    public function getMediaPostByPath($path)
    {
        $_wp_attached_file = substr ( $path, strlen("/wp-content/uploads") );
        
        $post = $this->getMediaByMeta('_wp_attached_file', $_wp_attached_file);
        
        if ($post === null)
        {
            $post = $this->getPostFromGuid($path);
        }
        
        return $post;
    }
    
    public function hasPermissionToViewMedia(wpapi\v1\WordpressPluginApi $wordpressApi, MembersApi $membersApi, $post)
    {
        if ($wordpressApi->is_admin()) return true;
        
        if ($post === null || $post->ID === null)
        {
            if ($this->getOption("fail-secure"))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        
        $exclusionOption = $this->getOption("exclude-type");

        if (!$this->pluginDependenciesExist($exclusionOption, $membersApi))
        {
            return !$this->getOption("fail-secure");
        }
        else if ($exclusionOption === "members")
        {
            if ($membersApi->members_can_current_user_view_post( $post->ID )) 
                return true;
        }
        else if ($exclusionOption === "login-status")
        {
            return is_user_logged_in();
        }
        
        return false;
    }
    
    public function getOption($key)
    {
        switch ($key)
        {
            case "open-in-browser" : return true;
            case "fail-secure" : return false;
            case "exclude-type": return "members"; //"login-status";
        }
    }
    
    function fatal_handler() {

        $error = error_get_last();
        var_dump($error);
    }
    
    public function getValidPathFromUrl(string $url,PhpApi $phpApi)
    {
        if ($url === null || strlen($url) === 0) $url = $_SERVER['REQUEST_URI'];
        
        if (strlen($url) < 5) throw new Exception();
        
        $fileExtension = $this->get_file_extension($url);
        
        if (!$this->validFileExtension($fileExtension)) throw new Exception();
        
        if ($phpApi->file_exists ( ABSPATH . $url )) 
        {
            // do nothing.
        }
        else if ($phpApi->file_exists ( ABSPATH . "/wp-content/uploads" . $url ))
        {
            $url = "/wp-content/uploads" . $url;
        }
        else if ($phpApi->file_exists ( ABSPATH . "/wp-content/uploads/" . $url ))
        {
            $url = "/wp-content/uploads/" . $url;
        }
        else 
        {
            throw new Exception();
        }
        
        return $url;
    }

    public function validFileExtension($fileExtension)
    {
        switch ($fileExtension)
        {
            case "pdf":
            case "doc":
            case "docx":
            case "xls":
            case "xlsx":
            case "ppt":
            case "pptx":
            case "rtf":
                return true;
        }
        
        return false;
    }

    public function redirectToMedia(PhpApi $phpApi, $url, $openInBrowser = false)
    {
        // .pdf .doc .docx .xls .xlsx .ppt .pptx .rtf
        
        $filename = $this->getFileNameFromUrl($url);
        
        $fileExtension = $this->get_file_extension($filename);
        
        switch ($fileExtension)
        {
            case "pdf":
                $phpApi->header("Content-type:application/pdf");
                break;
            case "doc":
                $phpApi->header("Content-type:application/msword");
                break;
            case "docx":
                $phpApi->header("Content-type:application/vnd.openxmlformats-officedocument.wordprocessingml.document");
                break;
            case "xls":
                $phpApi->header("Content-type:application/vnd.ms-excel");
                break;
            case "xlsx":
                $phpApi->header("Content-type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
                break;
            case "ppt":
                $phpApi->header("Content-type:application/vnd.ms-powerpoint");
                break;
            case "pptx":
                $phpApi->header("Content-type:application/vnd.openxmlformats-officedocument.presentationml.presentation");
                break;
            case "rtf":
                // not sure...
                break;
        }
            
        if (!$openInBrowser)
        {
            $phpApi->header("Content-Disposition:attachment;filename='$filename'");
        }

        $phpApi->readfile(ABSPATH . $url);
    }
    
    public function get_file_extension($file_name) 
    {
	return substr(strrchr($file_name,'.'),1);
    }
    
    public function getMediaByMeta($key, $value)
    {
        $args = array(
            'post_type'  => 'attachment',
            'meta_query' => array(
                array(
                    'key'   => $key,
                    'value' => $value,
                )
            )
        );

        $posts = get_posts( $args );

        return $posts[0];
    }
    
    public function getFileNameFromUrl($url)
    {
        $startOfFilename = strrpos ( $url , "/" ) + 1;
        
        if ($startOfFilename === 1 || $startOfFilename >= strlen($url))
        {
            $filename = "document.pdf";
        }
        else
        {
            $filename = substr ( $url , strrpos ( $url , "/" ) + 1);
        }
        
        return $filename;
    }
    
    public function getPostFromGuid($guid)
    {
        $id = $this->getIdFromGuid($guid);

        $post = get_post($id);

        return $post;
    }
    
    public function getIdFromGuid( $guid )
    {
        global $wpdb;
        return $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid LIKE '%s'", "%$guid" ) );
    }
}