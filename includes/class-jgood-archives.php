<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    JGood_Archives
 * @subpackage JGood_Archives/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    JGood_Archives
 * @subpackage JGood_Archives/includes
 * @author     Your Name <email@example.com>
 */
class JGood_Archives {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      JGood_Archives_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $jgood_archives    The string used to uniquely identify this plugin.
	 */
	protected $jgood_archives;

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
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->jgood_archives = 'jgood-archives';
		$this->version = '1.0.2';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - JGood_Archives_Loader. Orchestrates the hooks of the plugin.
	 * - JGood_Archives_i18n. Defines internationalization functionality.
	 * - JGood_Archives_Admin. Defines all hooks for the dashboard.
	 * - JGood_Archives_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jgood-archives-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jgood-archives-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-jgood-archives-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-jgood-archives-public.php';


		$this->loader = new JGood_Archives_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the JGood_Archives_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new JGood_Archives_i18n();
		$plugin_i18n->set_domain( $this->get_jgood_archives() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new JGood_Archives_Admin( $this->get_jgood_archives(), $this->get_version() );

		// setup custom archive post admin views
		// add url column to all archives admin page
		$this->loader->add_action( 'manage_jgood_archives_posts_columns', $plugin_admin, 'jgood_archives_admin_list_columns' );
		// display url in the URL column of the all archives page
		$this->loader->add_action( 'manage_jgood_archives_posts_custom_column', $plugin_admin, 'jgood_archives_admin_list', 10, 2);
		// add jgood archives settings menu to admin menu
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'jgood_archives_menu' );
		// remove default parent section on custom post page
		//$this->loader->add_action( 'admin_menu', $plugin_admin, 'jgood_archives_post_parent_menu' );

		// saves settings page to archives db table
		$this->loader->add_action( 'admin_post_jgood_archives_save_settings', $plugin_admin, 'jgood_archives_process_settings' );

		// add archive options to custom post type
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'jgood_archives_add_metaboxes' );
		// saves custom post type archive settings to archives db table
		$this->loader->add_action( 'save_post', $plugin_admin, 'jgood_archives_save_archive', 10, 3 );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new JGood_Archives_Public( $this->get_jgood_archives(), $this->get_version() );
		
		// set up redirects for archive pagination
		$this->loader->add_action( 'redirect_canonical', $plugin_public, 'jgood_archives_redirect_canonical' );
		// choosing an archive template to tuse
		$this->loader->add_action( 'template_include', $plugin_public, 'jgood_archives_template' );
		// setup shortcode
		$this->loader->add_shortcode( 'jgoodarchives', $plugin_public, 'jgood_archives_page_shortcode' );

		// register get sidebar action
		$this->loader->add_action( 'jgood_archives_get_sidebar', $plugin_public, 'jgood_archives_get_sidebar', 10, 2 );
		// register get title action
		$this->loader->add_action( 'jgood_archives_get_title', $plugin_public, 'jgood_archives_get_title', 10, 2 );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// add widget
		$this->loader->add_action( 'widgets_init', $plugin_public, 'jgood_archives_register_widget');

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
	public function get_jgood_archives() {
		return $this->jgood_archives;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    JGood_Archives_Loader    Orchestrates the hooks of the plugin.
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
