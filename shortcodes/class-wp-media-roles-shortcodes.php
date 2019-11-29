<?php

/**
 * This class defines Shortcodes for the plugin.
 *
 * @link       
 * @since      1.0.0
 *
 * @package    
 * @subpackage 
 */
class Wp_Media_Roles_Shortcodes
{
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
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
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
         * defined in Wp_Shark_Spot_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wp_Shark_Spot_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style( $this->plugin_name . "-shortcodes", plugin_dir_url( __FILE__ ) . 'css/wp-media-roles-shortcodes.css', array(), $this->version, 'all' );

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
         * defined in Wp_Shark_Spot_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wp_Shark_Spot_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script( $this->plugin_name . "-shortcodes", plugin_dir_url( __FILE__ ) . 'js/wp-media-roles-shortcodes.js', array( 'jquery' ), $this->version, false );

    }

    public function post_info( $atts, $content = null, $tag = '' )
    {
        $key = $atts['key'] ?? null;

        $attachment = get_post( get_the_ID() );

        switch ($key)
        {
            case 'filepath':    return basename ( get_attached_file( $attachment->ID ) );
            case 'caption':     return $attachment->post_excerpt;
            case 'description': return $attachment->post_content;
            case 'url':         return $attachment->guid;
        }
        
        return "";
    }
}
