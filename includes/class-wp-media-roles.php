<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       5nines.com
 * @since      1.0.0
 *
 * @package    Wp_Media_Roles
 * @subpackage Wp_Media_Roles/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wp_Media_Roles
 * @subpackage Wp_Media_Roles/includes
 * @author     Matthew Kirk <mkirk@5nines.com>
 */
class Wp_Media_Roles {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Media_Roles_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

        /**
         * A wrapper around the Wordpress Plugin API, so we can test it.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $version    The current version of this plugin.
         */
        private $wordpressApi;
        
        private $phpApi;
        
        private $membersApi;
        
	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'wp-media-roles';
		$this->version = '1.1.0';
                
		$this->load_dependencies();
		$this->set_locale();
		
                $this->wordpressApi = new wpapi\v1\WordpressPluginApi();
                
                $this->phpApi = new PhpApi();
                
                $this->membersApi = new MembersApi();
                
                if (is_admin() || is_customize_preview())
                {
                    $this->define_admin_hooks();
                    
                    $this->define_ajax_hooks();
                }
                
		$this->define_public_hooks();

                if (!is_admin())
                {
                    $this->define_template_hooks();
                    
                    $this->add_shortcodes();
                }
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_Media_Roles_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_Media_Roles_i18n. Defines internationalization functionality.
	 * - Wp_Media_Roles_Admin. Defines all hooks for the admin area.
	 * - Wp_Media_Roles_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-media-roles-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-media-roles-i18n.php';

                /**
		 * Core dependencies.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'core/service/class-htaccess-service.php';
                
                /**
		 * The class responsible for defining all ajax.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'ajax/class-wp-media-roles-ajax.php';
                
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-media-roles-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-media-roles-public.php';
                
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public-templates/class-media-template.php';
                
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'shortcodes/class-wp-media-roles-shortcodes.php';
                
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/wordpress-plugin-api/WordpressPluginApi.php';
                
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/php-api/class-php-api.php';
                
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/members-api/class-members-api.php';
                
		$this->loader = new Wp_Media_Roles_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_Media_Roles_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wp_Media_Roles_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

        /**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_ajax_hooks() {

		$plugin_ajax = new Wp_Media_Roles_Ajax( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_ajax, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_ajax, 'enqueue_scripts' );

                $this->loader->add_action( 'wp_ajax_recreate_htaccess', $plugin_ajax, 'wp_ajax_recreate_htaccess' );
	}
        
	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wp_Media_Roles_Admin( 
                        $this->get_plugin_name(), 
                        $this->get_version(),
                        $this->wordpressApi,
                        $this->phpApi,
                        $this->membersApi);

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

                $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_management_page_action');
                
                $this->loader->add_filter( 'members_api', $plugin_admin, 'members_api_filter');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wp_Media_Roles_Public( 
                        $this->get_plugin_name(), 
                        $this->get_version(),
                        $this->wordpressApi,
                        $this->phpApi,
                        $this->membersApi);

//		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
//		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

                $membersRef =  $this->membersApi->get_membersuite_admin_instance();
                
                $this->loader->add_action( 'parse_request', $plugin_public, 'parse_request', 99999, 0);
                
                if ($membersRef !== null)
                {
                    $this->loader->add_action( 'edit_attachment', $membersRef, 'update', 10, 1 );
                    
                    $this->loader->add_filter( 'members_enable_attachment_content_permissions', $plugin_public, 'members_enable_attachment_content_permissions');
                }
	}

        /**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_template_hooks() 
        {
            $class = new Media_Template( $this->get_plugin_name(), $this->get_version() );
            
            $this->loader->add_filter( 'the_content', $class, 'the_content', 10, 1 );

            $this->loader->add_filter( 'template_include', $class, 'template_include', 10, 1 );

            $this->loader->add_filter( 'get_post_metadata', $class, 'get_post_metadata', 10, 4 );
            
            $this->loader->add_filter( 'members_post_error_message', $class, 'members_post_error_message', 10, 1 );
	}
        
        private function add_shortcodes()
	{
            $shortcodes = new Wp_Media_Roles_Shortcodes( $this->get_plugin_name(), $this->get_version());

            $this->loader->add_action( 'wp_enqueue_scripts', $shortcodes, 'enqueue_styles' );
            $this->loader->add_action( 'wp_enqueue_scripts', $shortcodes, 'enqueue_scripts' );

            $this->loader->add_shortcode('post_info', $shortcodes, 'post_info');
	}
        
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wp_Media_Roles_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
