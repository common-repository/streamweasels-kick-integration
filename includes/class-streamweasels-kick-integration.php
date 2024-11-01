<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.streamweasels.com
 * @since      1.0.0
 *
 * @package    Streamweasels_Kick_Integration
 * @subpackage Streamweasels_Kick_Integration/includes
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
 * @package    Streamweasels_Kick_Integration
 * @subpackage Streamweasels_Kick_Integration/includes
 * @author     StreamWeasels <admin@streamweasels.com>
 */
class Streamweasels_Kick_Integration {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Streamweasels_Kick_Integration_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'STREAMWEASELS_KICK_VERSION' ) ) {
			$this->version = STREAMWEASELS_KICK_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'streamweasels-kick-integration';

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
	 * - Streamweasels_Kick_Integration_Loader. Orchestrates the hooks of the plugin.
	 * - Streamweasels_Kick_Integration_i18n. Defines internationalization functionality.
	 * - Streamweasels_Kick_Integration_Admin. Defines all hooks for the admin area.
	 * - Streamweasels_Kick_Integration_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-streamweasels-kick-integration-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-streamweasels-kick-integration-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-streamweasels-kick-integration-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-streamweasels-kick-integration-admin-wall.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-streamweasels-kick-integration-admin-status.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-streamweasels-kick-integration-admin-vods.php';

		if (ski_fs()->is_plan_or_trial('pro', true)) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-streamweasels-kick-integration-admin-feature.php';
		}

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-streamweasels-kick-integration-public.php';

		$this->loader = new Streamweasels_Kick_Integration_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Streamweasels_Kick_Integration_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Streamweasels_Kick_Integration_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Streamweasels_Kick_Integration_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_admin_wall = new Streamweasels_Kick_Integration_Wall_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_admin_status = new Streamweasels_Kick_Integration_Status_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_admin_vods = new Streamweasels_Kick_Integration_Vods_Admin( $this->get_plugin_name(), $this->get_version() );


		$this->loader->add_action( 'admin_notices', $plugin_admin, 'display_admin_upsell' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_admin, 'enqueue_blocks' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'display_admin_page' );
		$this->loader->add_action( 'admin_menu', $plugin_admin_wall, 'display_admin_page_wall', 20 );
		$this->loader->add_action( 'admin_menu', $plugin_admin_status, 'display_admin_page_status', 20 );
		$this->loader->add_action( 'admin_menu', $plugin_admin_vods, 'display_admin_page_vods', 20 );
		$this->loader->add_action( 'rest_api_init', $plugin_admin, 'add_rest_endpoints' );
		$this->loader->add_filter( 'block_categories_all', $plugin_admin, 'swki_custom_block_category', 10, 2 );
		$this->loader->add_action( 'wp_ajax_swki_admin_notice_dismiss', $plugin_admin, 'swki_admin_notice_dismiss' );
		$this->loader->add_action( 'wp_ajax_swki_admin_notice_dismiss_for_good', $plugin_admin, 'swki_admin_notice_dismiss_for_good' );

		if (ski_fs()->is_plan_or_trial('pro', true)) {
			$plugin_admin_feature = new Streamweasels_Kick_Integration_Feature_Admin( $this->get_plugin_name(), $this->get_version() );
			$this->loader->add_action( 'admin_menu', $plugin_admin_feature, 'display_admin_page_feature', 20 );
			$this->loader->add_filter( 'swki_twitch_layout_options', $plugin_admin_feature, 'swki_twitch_layout_options_pro' );
		}

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Streamweasels_Kick_Integration_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'streamWeasels_shortcode' );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'swki_status_show_global' );


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
	 * @return    Streamweasels_Kick_Integration_Loader    Orchestrates the hooks of the plugin.
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
