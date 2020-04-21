<?php


/**
 * @package WordPress Comics
 */
class WP_Comics {


	/** plugin version number */
	const VERSION = '1.0.0';

	private $wp_comics_publishers = array();

	/**
	 * Initializes the plugin
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// set the default comic publishers.
		add_action( 'init', array( $this, 'set_comic_publishers' ) );

		// register the comic post type.
		add_action( 'init', array( $this, 'register_comic_post_type' ) );

		// activate hook.
		register_activation_hook( __FILE__, array( $this, 'plugin_activate' ) );

		// deactivate hook.
		register_deactivation_hook( __FILE__, array( $this, 'plugin_deactivate' ) );
	}

	/**
	 * Called when the plugin is activated
	 *
	 * @since 1.0.0
	 */
	public function plugin_activate() {

		// register the comic post type.
		$this->register_comic_post_type();
	}

	/**
	 * Called when the plugin is deactivated
	 *
	 * @since 1.0.0
	 */
	public function plugin_deactivate() {

	}

	public function set_comic_publishers() {

		$this->wp_comics_publishers = apply_filters(
			'wp_comics_publishers',
			array(
				'alterna'    => 'Alterna Comics',
				'dark-horse' => 'Dark Horse Comics',
				'dc'         => 'DC Comics',
				'image'      => 'Image Comics',
				'marvel'     => 'Marvel Comics',
			)
		);
	}

}
