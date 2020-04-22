<?php


/**
 * WordPress Comics main plugin class
 *
 * @package WordPress Comics
 */
class WP_Comics {


	/** plugin version number */
	const VERSION = '1.0.0';

	/** @var array names of comic book publishing companies */
	private $wp_comics_publishers = array();

	/** @var array meta data fields for the wp_comics post type */
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

		// display meta before the content.
		add_filter( 'the_content', array( $this, 'prepend_comic_meta_to_content' ) );

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
	 * Sets the names of comic book publishers that can be selected
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
				'other'      => 'Other',
			)
		);
	}

	/**
	 * Registers the wp_comics post type, the new post type this plugin provides
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
	 * Adds meta boxes containing meta data fields for wp_comics posts.
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
	 * Displays meta data fields for wp_comics posts
	 *
	 * @param \WP_Post $post the wp_comics post.
	 *
	 * @since 1.0.0
	 */
	public function comic_meta_box_display( $post ) {

		// set nonce field.
		wp_nonce_field( 'wp_comics_nonce', 'wp_comics_nonce_field' );

		$post_meta = array();

		// load the post meta data.
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
				  <option value="<?php echo sanitize_key( $key ); ?>"<?php echo esc_attr( $selected ); ?>><?php echo esc_attr( $name ); ?></option>
					<?php
				}
			}
			?>
			</select>
		</div>
	<div class="field">
	  <label for="wp_comics_issue">Issue #</label>
	  <input type="text" name="wp_comics_issue" id="wp_comics_issue" value="<?php echo esc_attr( $post_meta['wp_comics_issue'] ); ?>" />
	</div>
	<div class="field">
	  <label for="wp_comics_date">Date</label>
	  <input type="text" name="wp_comics_date" id="wp_comics_date" value="<?php echo esc_attr( $post_meta['wp_comics_date'] ); ?>" />
	</div>
	<div class="field">
	  <label for="wp_comics_price">Price</label>
	  <input type="text" name="wp_comics_price" id="wp_comics_price" value="<?php echo esc_attr( $post_meta['wp_comics_price'] ); ?>" />
	</div>
	<div class="field">
	  <label for="wp_comics_cover_artist">Cover artist</label>
	  <input type="text" name="wp_comics_cover_artist" id="wp_comics_cover_artist" value="<?php echo esc_attr( $post_meta['wp_comics_cover_artist'] ); ?>" />
	</div>
	<div class="field">
	  <label for="wp_comics_writer">Writer</label>
	  <input type="text" name="wp_comics_writer" id="wp_comics_writer" value="<?php echo esc_attr( $post_meta['wp_comics_writer'] ); ?>" />
	</div>
	<div class="field">
	  <label for="wp_comics_penciller">Penciller</label>
	  <input type="text" name="wp_comics_penciller" id="wp_comics_penciller" value="<?php echo esc_attr( $post_meta['wp_comics_penciller'] ); ?>" />
	</div>
	<div class="field">
	  <label for="wp_comics_inker">Inker</label>
	  <input type="text" name="wp_comics_inker" id="wp_comics_inker" value="<?php echo esc_attr( $post_meta['wp_comics_inker'] ); ?>" />
	</div>
	<div class="field">
	  <label for="wp_comics_colorist">Colorist</label>
	  <input type="text" name="wp_comics_colorist" id="wp_comics_colorist" value="<?php echo esc_attr( $post_meta['wp_comics_colorist'] ); ?>" />
	</div>
		<?php
		// after main form elements hook.
		do_action( 'wp_comics_admin_form_end' );
		?>
  </div>
		<?php
	}

	/**
	 * Saves the wp_comics post meta data.
	 *
	 * @param int $post_id the id of the wp_comics post.
	 *
	 * @since 1.0.0
	 */
	public function save_comic( $post_id ) {

		// check for nonce.
		if ( ! isset( $_POST['wp_comics_nonce_field'] ) ) {
			return $post_id;
		}

		// verify nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wp_comics_nonce_field'] ) ), 'wp_comics_nonce' ) ) {
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
				$updated_post_meta[ $field ] = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
			}
		}

		// update fields.
		foreach ( $updated_post_meta as $field => $value ) {
			update_post_meta( $post_id, $field, $value );
		}

		// comic save hook.
		do_action( 'wp_comics_admin_save', $post_id, $_POST );
	}

	/**
	 * Adds comic meta data to wp_comics posts
	 *
	 * @param string $content the content of the post.
	 *
	 * @since 1.0.0
	 */
	public function prepend_comic_meta_to_content( $content ) {

		global $post, $post_type;

		// display on single instances of the wp_comics content type.
		if ( 'wp_comics' === $post_type && is_singular( 'wp_comics' ) ) {

			$wp_comics_id = $post->ID;

			$post_meta = array();

			foreach ( $this->wp_comics_meta_fields as $field ) {
				$post_meta[ $field ] = get_post_meta( $post->ID, $field, true );
			}

			// build markup.
			$html = '';

			$html .= '<section class="meta-data">';

			// output any additional content before the comic meta data.
			do_action( 'wp_comics_meta_data_output_start', $wp_comics_id );

			$html .= '<p>';
			// publisher.
			if ( ! empty( $post_meta['wp_comics_publisher'] ) ) {
				$publisher = $post_meta['wp_comics_publisher'];
				$html     .= '<b>Publisher</b> ' . ( isset( $this->wp_comics_publishers[ $publisher ] ) ? $this->wp_comics_publishers[ $publisher ] : $publisher ) . '</br>';
			}
			// issue number.
			if ( ! empty( $post_meta['wp_comics_issue'] ) ) {
				$html .= '<b>Issue #</b> ' . $post_meta['wp_comics_issue'] . '</br>';
			}
			// date.
			if ( ! empty( $post_meta['wp_comics_date'] ) ) {
				$html .= '<b>Date</b> ' . $post_meta['wp_comics_date'] . '</br>';
			}
			// price.
			if ( ! empty( $post_meta['wp_comics_price'] ) ) {
				$html .= '<b>Price</b> ' . $post_meta['wp_comics_price'] . '</br>';
			}
			// cover artist.
			if ( ! empty( $post_meta['wp_comics_cover_artist'] ) ) {
				$html .= '<b>Cover artist</b> ' . $post_meta['wp_comics_cover_artist'] . '</br>';
			}
			// writer.
			if ( ! empty( $post_meta['wp_comics_writer'] ) ) {
				$html .= '<b>Writer</b> ' . $post_meta['wp_comics_writer'] . '</br>';
			}
			// penciller.
			if ( ! empty( $post_meta['wp_comics_penciller'] ) ) {
				$html .= '<b>Penciller</b> ' . $post_meta['wp_comics_penciller'] . '</br>';
			}
			// inker.
			if ( ! empty( $post_meta['wp_comics_inker'] ) ) {
				$html .= '<b>Inker</b> ' . $post_meta['wp_comics_inker'] . '</br>';
			}
			// colorist.
			if ( ! empty( $post_meta['wp_comics_colorist'] ) ) {
				$html .= '<b>Colorist</b> ' . $post_meta['wp_comics_colorist'] . '</br>';
			}
			$html .= '</p>';

			// output any additional content after the comic meta data.
			do_action( 'wp_comics_meta_data_output_end', $wp_comics_id );

			$html .= '</section>';
			$html .= $content;

			return $html;

		} else {
			return $content;
		}

	}

}
