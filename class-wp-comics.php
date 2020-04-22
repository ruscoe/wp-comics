<?php


/**
 * @package WordPress Comics
 */
class WP_Comics {


	/** plugin version number */
	const VERSION = '1.0.0';

	private $wp_comics_publishers = array();

	private $wp_comics_meta_fields = array(
		'wp_comics_publisher',
		'wp_comics_issue',
		'wp_comics_date',
		'wp_comics_price',
		'wp_comics_cover_artist',
		'wp_comics_writer',
		'wp_comics_penciller',
		'wp_comics_inker',
		'wp_comics_colorist',
	);

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

		// add meta boxes.
		add_action( 'add_meta_boxes', array( $this, 'add_comic_meta_boxes' ) );

		// save comic.
		add_action( 'save_post_wp_comics', array( $this, 'save_comic' ) );

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

	/**
	 *
	 *
	 * @since 1.0.0
	 */
	public function add_comic_meta_boxes() {

		add_meta_box(
			// id.
			'wp_comics_meta_box',
			// name.
			'Comic Information',
			// display function.
			array( $this, 'comic_meta_box_display' ),
			// post type.
			'wp_comics',
			// location.
			'normal',
			// priority.
			'default'
		);
	}

	/**
	 *
	 *
	 * @since 1.0.0
	 */
	public function comic_meta_box_display( $post ) {

		// set nonce field.
		wp_nonce_field( 'wp_comics_nonce', 'wp_comics_nonce_field' );

		$post_meta = array();

		foreach ( $this->wp_comics_meta_fields as $field ) {
			$post_meta[ $field ] = get_post_meta( $post->ID, $field, true );
		}

		?>
	<div class="field-container">
		<?php
		// before main form elements hook.
		do_action( 'wp_comics_admin_form_start' );
		?>
		<div class="field">
			<label for="wp_comics_publisher">Publisher</label>
			<select name="wp_comics_publisher" id="wp_comics_publisher">
			<?php
			if ( ! empty( $this->wp_comics_publishers ) ) {
				foreach ( $this->wp_comics_publishers as $key => $name ) {
					$selected = ( $key === $post_meta['wp_comics_publisher'] ) ? ' selected="true"' : '';
					?>
				  <option value="<?php echo sanitize_key( $key ); ?>"<?php echo $selected; ?>><?php echo sanitize_text_field( $name ); ?></option>
					<?php
				}
			}
			?>
			</select>
		</div>
	<div class="field">
	  <label for="wp_comics_issue">Issue #</label>
	  <input type="text" name="wp_comics_issue" id="wp_comics_issue" value="<?php echo $post_meta['wp_comics_issue']; ?>" />
	</div>
	<div class="field">
	  <label for="wp_comics_date">Date</label>
	  <input type="text" name="wp_comics_date" id="wp_comics_date" value="<?php echo $post_meta['wp_comics_date']; ?>" />
	</div>
	<div class="field">
	  <label for="wp_comics_price">Price</label>
	  <input type="text" name="wp_comics_price" id="wp_comics_price" value="<?php echo $post_meta['wp_comics_price']; ?>" />
	</div>
	<div class="field">
	  <label for="wp_comics_cover_artist">Cover artist</label>
	  <input type="text" name="wp_comics_cover_artist" id="wp_comics_cover_artist" value="<?php echo $post_meta['wp_comics_cover_artist']; ?>" />
	</div>
	<div class="field">
	  <label for="wp_comics_writer">Writer</label>
	  <input type="text" name="wp_comics_writer" id="wp_comics_writer" value="<?php echo $post_meta['wp_comics_writer']; ?>" />
	</div>
	<div class="field">
	  <label for="wp_comics_penciller">Penciller</label>
	  <input type="text" name="wp_comics_penciller" id="wp_comics_penciller" value="<?php echo $post_meta['wp_comics_penciller']; ?>" />
	</div>
	<div class="field">
	  <label for="wp_comics_inker">Inker</label>
	  <input type="text" name="wp_comics_inker" id="wp_comics_inker" value="<?php echo $post_meta['wp_comics_inker']; ?>" />
	</div>
	<div class="field">
	  <label for="wp_comics_colorist">Colorist</label>
	  <input type="text" name="wp_comics_colorist" id="wp_comics_colorist" value="<?php echo $post_meta['wp_comics_colorist']; ?>" />
	</div>
		<?php
		// after main form elements hook.
		do_action( 'wp_comics_admin_form_end' );
		?>
  </div>
		<?php
	}

	/**
	 *
	 *
	 * @since 1.0.0
	 */
	public function save_comic( $post_id ) {

		// check for nonce.
		if ( ! isset( $_POST['wp_comics_nonce_field'] ) ) {
			return $post_id;
		}

		// verify nonce.
		if ( ! wp_verify_nonce( $_POST['wp_comics_nonce_field'], 'wp_comics_nonce' ) ) {
			return $post_id;
		}

		// check for autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// get fields.
		$updated_post_meta = array();

		foreach ( $this->wp_comics_meta_fields as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				$updated_post_meta[ $field ] = sanitize_text_field( $_POST[ $field ] );
			}
		}

		// update fields.
		foreach ( $updated_post_meta as $field => $value ) {
			update_post_meta( $post_id, $field, $value );
		}

		// comic save hook.
		do_action( 'wp_comics_admin_save', $post_id, $_POST );
	}

}
