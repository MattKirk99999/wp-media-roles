<?php

/**
 * The 
 *
 * @link       www.5nines.com
 * @since      1.0.0
 *
 * @package    Membersuite_Sso
 * @subpackage Membersuite_Sso/public
 */

/**
 * The 
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Membersuite_Sso
 * @subpackage Membersuite_Sso/public
 * @author     Matthew Kirk <mkirk@5nines.com>
 */
class Media_Template 
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string
     */
    private $version;
    
    private $post_type = 'attachment';
    
    private $content_global_module = 39608;
    
    private $content_blocked_global_module = 39580;
    
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version) 
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    
    public function the_content( $the_content ) 
    {
        if (!is_singular( $this->post_type )) return $the_content;

        $the_content = "[et_pb_section global_module='$this->content_global_module'][/et_pb_section]";
        
        return $the_content;
    }

    public function template_include( string $template ): string
    {
        if (!is_singular( $this->post_type )) return $template;
        
        if (!self::endsWith($template, "/Divi/single.php")) return $template;

        $new_template = substr($template, 0, -strlen("/Divi/single.php")) . "/Divi/page.php";
        
        if (!file_exists ( $new_template )) return $template;
        
        return $new_template;
    }
    
    public function members_post_error_message(string $message)
    {
        if (!is_singular( $this->post_type )) return $message;

        $message = '<div id="et-boc">' . "[et_pb_section global_module='$this->content_blocked_global_module'][/et_pb_section]" . '</div>';
        
        return $message;
    }

    /**
     * Filters whether to retrieve metadata of a specific type.
     *
     * The dynamic portion of the hook, `$meta_type`, refers to the meta
     * object type (comment, post, term, or user). Returning a non-null value
     * will effectively short-circuit the function.
     *
     * @since 3.1.0
     *
     * @param null|array|string $value     The value get_metadata() should return - a single metadata value,
     *                                     or an array of values.
     * @param int               $object_id Object ID.
     * @param string            $meta_key  Meta key.
     * @param bool              $single    Whether to return only the first value of the specified $meta_key.
     */
    public function get_post_metadata( $value, int $object_id, string $meta_key, bool $single )
    {
        if (!is_singular( $this->post_type )) return $value;
        
        if ($meta_key !== '_et_pb_use_builder' || $single === false) return $value;

        return 'on';
    }
    
    private static function endsWith($haystack, $needle) 
    {
        return substr_compare($haystack, $needle, -strlen($needle)) === 0;
    }
}
