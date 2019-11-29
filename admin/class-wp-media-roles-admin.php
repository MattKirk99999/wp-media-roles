<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       5nines.com
 * @since      1.0.0
 *
 * @package    Wp_Media_Roles
 * @subpackage Wp_Media_Roles/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Media_Roles
 * @subpackage Wp_Media_Roles/admin
 * @author     Matthew Kirk <mkirk@5nines.com>
 */
class Wp_Media_Roles_Admin {

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
     * @param      string    $plugin_name       The name of this plugin.
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
     * Register the stylesheets for the admin area.
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

            wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-media-roles-admin.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
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

            wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-media-roles-admin.js', array( 'jquery' ), $this->version, false );

    }

    public function add_management_page_action()
    {
        $hook = add_management_page( 'Test Media Permissions', 'Media Permissions', 'install_plugins', 'test-wp-media-roles', array( $this, 'display_management_page' ) );
        add_action( "load-$hook", array( $this, 'load_management_page' ) );
    }

    public function load_management_page()
    {
        //
    }

    public function display_management_page()
    {
        include_once( 'partials/wp-media-roles-admin-display.php' );
    }

    public function members_api_filter($noargs = null): MembersApi
    {
        return $this->membersApi;
    }

    public function getHtaccessPath(): string
    {
        $wordpress_path = get_home_path();

        $filename = ".htaccess";

        return $wordpress_path . "wp-content/uploads/" . $filename;
    }

    public function htaccessExists(): bool
    {
        $path = $this->getHtaccessPath();

        return file_exists($path);
    }

    public function htaccessIsValid(): bool
    {
        if (!$this->htaccessExists()) return false;

        $contents = trim( file_get_contents($this->getHtaccessPath()) );

        $expected = trim( $this->getHtaccessRules() );

        return $contents == $expected;
    }

    public function getHtaccessRules(): string
    {
        $ret = "";

        $ret .= "<IfModule mod_rewrite.c>\n";
        $ret .=     "RewriteEngine On\n";
        $ret .=     "### start-wp-media-roles ###\n";
        $ret .=     "RewriteCond %{HTTP_HOST} ^wasb.org [OR]\n";
        $ret .=     "RewriteCond %{HTTP_HOST} ^www.wasb.org$\n";
        $ret .=     "RewriteCond %{REQUEST_FILENAME} -f\n";
        $ret .=     "RewriteRule ^(.+\.(pdf|doc|docx|xls|xlsx|ppt|pptx))$ /index.php [L]\n";
        $ret .=     "### end-wp-media-roles ###\n";
        $ret .= "</IfModule>";

        return $ret;
    }
    
    public function testHtaccess(): bool
    {
//        copy('foo/test.php', 'bar/test.php');
//        
//        $url = plugins_url();
//        
//        var_dump($url);
//        
//        $test_url = get_site_url() . "/wp-content/uploads/wp-media-roles/test/fake/path/to/fake/file.pdf";
        
//        $contents = file_get_contents($test_url);
//        
//        var_dump($contents);
        
        return false;
    }
}