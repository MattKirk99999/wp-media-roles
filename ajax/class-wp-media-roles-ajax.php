<?php

/**
 * The ajax-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the ajax-specific stylesheet and JavaScript.
 *
 * @package    Wp_Media_Roles
 * @subpackage Wp_Media_Roles/ajax
 * @author     matt kirk <matthew.the.kirk@gmail.com>
 */
class Wp_Media_Roles_Ajax 
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

    private $htaccessService;
    
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

            $this->plugin_name = $plugin_name;
            $this->version = $version;
            
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
             * defined in Wp_Shark_Spot_Loader as all of the hooks are defined
             * in that particular class.
             *
             * The Wp_Shark_Spot_Loader will then create the relationship
             * between the defined hooks and the functions defined in this
             * class.
             */

            wp_enqueue_style( $this->plugin_name . "-ajax", plugin_dir_url( __FILE__ ) . 'css/wp-media-roles-ajax.css', array(), $this->version, 'all' );

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

            wp_enqueue_script( $this->plugin_name . "-ajax", plugin_dir_url( __FILE__ ) . 'js/wp-media-roles-ajax.js', array( 'jquery' ), $this->version, false );

    }

    public function ajax_shutdown_function()
    {
        @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );

        if ( ! headers_sent() ) 
        {
            status_header( 400 );
            nocache_headers();
        }

        $response = [ 
            'success' => false,
            'data' => error_get_last()
        ];

        echo wp_json_encode( $response );
    }

    public function wp_die_ajax_handler($function)
    {
        $error = error_get_last();

        // No error, just skip the error handling code.
        if ( null === $error ) {
                return $function;
        }

        // Bail if this error should not be handled.
        if ( ! $this->should_handle_error( $error ) ) {
                return $function;
        }

        return array($this, 'ajax_shutdown_function');
    }

    public function wp_ajax_recreate_htaccess()
    {
        global $wpdb;

        add_filter( 'wp_die_ajax_handler', array($this, 'wp_die_ajax_handler'), 100 );

        $status = ['status'=>'in-progress'];

        if ($this->htaccessService->htaccessIsValid())
        {
            $status['status'] = 'No change: .htaccess is already valid.';
            wp_send_json( $status, 200 );
            return;
        }

        $this->htaccessService->save();
        
        $this->htaccessService->recreate();
        
        if ($this->htaccessService->htaccessIsValid())
        {
            $status['status'] = 'Success.';
        }
        else
        {
            $this->htaccessService->restore();
            
            $status['status'] = 'Failed: unable to modify .htaccess.';
        }

        wp_send_json( $status, 200 );
    }

    /**
     * Determines whether we are dealing with an error that WordPress should handle
     * in order to protect the admin backend against WSODs.
     *
     * @since 5.2.0
     *
     * @param array $error Error information retrieved from error_get_last().
     * @return bool Whether WordPress should handle this error.
     */
    protected function should_handle_error( $error ) {
            $error_types_to_handle = array(
                    E_ERROR,
                    E_PARSE,
                    E_USER_ERROR,
                    E_COMPILE_ERROR,
                    E_RECOVERABLE_ERROR,
            );

            if ( isset( $error['type'] ) && in_array( $error['type'], $error_types_to_handle, true ) ) {
                    return true;
            }

            /**
             * Filters whether a given thrown error should be handled by the fatal error handler.
             *
             * This filter is only fired if the error is not already configured to be handled by WordPress core. As such,
             * it exclusively allows adding further rules for which errors should be handled, but not removing existing
             * ones.
             *
             * @since 5.2.0
             *
             * @param bool  $should_handle_error Whether the error should be handled by the fatal error handler.
             * @param array $error               Error information retrieved from error_get_last().
             */
            return (bool) apply_filters( 'wp_should_handle_php_error', false, $error );
    }
}
