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

	/**
	 *
	 *
	 * @since 1.0.0
	 */
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

	/**
	 *
	 *
	 * @since 1.0.0
	 */
	public function register_comic_post_type() {

		$labels = array(
			'name'               => 'Comic',
			'singular_name'      => 'Comic',
			'menu_name'          => 'Comics',
			'name_admin_bar'     => 'Comic',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Comic',
			'new_item'           => 'New Comic',
			'edit_item'          => 'Edit Comic',
			'view_item'          => 'View Comic',
			'all_items'          => 'All Comics',
			'search_items'       => 'Search Comics',
			'parent_item_colon'  => 'Parent Comic:',
			'not_found'          => 'No Comics found.',
			'not_found_in_trash' => 'No Comics found in Trash.',
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_nav'        => true,
			'query_var'          => true,
			'hierarchical'       => false,
			'supports'           => array( 'title', 'thumbnail', 'editor' ),
			'has_archive'        => true,
			'menu_position'      => 20,
			'show_in_admin_bar'  => true,
			'menu_icon'          => 'none',
			'rewrite'            => array(
				'slug'       => 'comics',
				'with_front' => 'true',
			),
		);

		register_post_type( 'wp_comics', $args );
	}

}
