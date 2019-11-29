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

    private $htaccessService;
    
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
        
        $this->htaccessService = new HtaccessService( $plugin_name, $version );
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
        $htaccessCurrent  = $this->htaccessService->getCurrentHtaccessRules();
        
        $htaccessExpected = $this->htaccessService->getHtaccessRules();
        
        $htaccessSaved    = $this->htaccessService->viewSaved();
        
        include_once( 'partials/wp-media-roles-admin-display.php' );
    }

    public function members_api_filter($noargs = null): MembersApi
    {
        return $this->membersApi;
    }
    
    public function getHtaccessPath(): string
    {
        return $this->htaccessService->getHtaccessPath();
    }

    public function htaccessExists(): bool
    {
        return $this->htaccessService->htaccessExists();
    }

    public function htaccessIsValid(): bool
    {
        return $this->htaccessService->htaccessIsValid();
    }

    public function getHtaccessRules(): string
    {
        return $this->htaccessService->getHtaccessRules();
    }
}