<?php
/**
 * Plugin Name: WordPress Comics
 * Plugin URI: https://github.com/ruscoe/wp-comics/
 * Description: Catalogue your comic book collection in WordPress!
 * Author: Dan Ruscoe
 * Author URI: http://ruscoe.org/
 * Version: 1.0.0
 * Text Domain: wordpress-comics
 * Domain Path: /i18n/languages/
 *
 * License: MIT License
 * License URI: https://en.wikipedia.org/wiki/MIT_License
 *
 * @author   Dan Ruscoe (dan@ruscoe.org)
 * @license  https://en.wikipedia.org/wiki/MIT_License MIT License
 */

 defined( 'ABSPATH' ) || exit;

 define( 'WP_COMICS__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

 require_once WP_COMICS__PLUGIN_DIR . 'class.wp-comics.php';
