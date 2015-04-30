<?php

/**
 * Landing pages admin functionality.
 *
 * @package    AllClients
 * @subpackage AllClients/landing-pages
 */
class AllClients_Landing_Pages_Admin {

	/**
	 * @var string The plugin name
	 */
	private $plugin_name;

	/**
	 * @var string The current version of this plugin.
	 */
	private $version;

	/**
	 * @var array Post type configuration.
	 */
	private $post_type;

	/**
	 * @param string $plugin_name Plugin name
	 * @param string $version     Plugin version
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Set post type configuration.
	 *
	 * @param array $post_type
	 */
	public function set_post_type(array $post_type) {
		$this->post_type = $post_type;
	}

	/**
	 * Add meta data box to landing page post form and save hook.
	 */
	public function add_meta_box() {
		$post_type = $this->post_type['type'];
		add_meta_box( $post_type . '_select', 'Select Landing Page', 'allclients_landing_pages_meta_box_callback', $post_type, 'normal' );
		add_action( 'save_post', 'allclients_landing_pages_meta_box_save' );
	}

	/**
	 * Add ajax actions to fetch landing pages over XHR.
	 */
	public function add_get_pages_action() {
		add_action( 'wp_ajax_allclients_get_landing_pages', 'allclients_ajax_get_landing_pages' );
		add_action( 'wp_ajax_allclients_get_landing_page', 'allclients_ajax_get_landing_page' );
	}

	/**
	 * Register the JavaScript for the post type screens, and remove auto-save option.
	 */
	public function enqueue_scripts() {
		$post_type = $this->post_type['type'];
		wp_enqueue_script( $this->plugin_name . '-landing-admin-scripts', plugin_dir_url( __FILE__ ) . 'js/settings.js', array( 'jquery' ), $this->version, false );
		if ( $post_type === get_post_type() ) {
			wp_enqueue_script( $this->plugin_name . '-landing-admin-scripts', plugin_dir_url( __FILE__ ) . 'js/meta-box.js', array( 'jquery' ), $this->version, false );
			wp_dequeue_script( 'autosave' );
		}
	}

}
