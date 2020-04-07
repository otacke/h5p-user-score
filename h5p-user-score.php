<?php

/**
 * Plugin Name: H5P-User-Score
 * Plugin URI: https://github.com/otacke/h5p-user-score
 * Text Domain: H5PUSERSCORE
 * Domain Path: /languages
 * Description: Display H5P user scores in posts and pages
 * Version: 0.1
 * Author: Uni Freiburg
 * License: MIT
 */

namespace H5PUSERSCORE;

// as suggested by the WordPress community
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( ! defined( 'H5PUSERSCORE_VERSION' ) ) {
	define( 'H5PUSERSCORE_VERSION', '0.1' );
}

// Requirements
require_once( __DIR__ . '/class-util.php' );
require_once( __DIR__ . '/class-database.php' );

/**
 * Setup the plugin.
 */
function setup() {
	wp_enqueue_script( 'H5PUSERSCORESTORAGE', plugins_url( '/js/h5p-user-score-storage.js', __FILE__ ), array(), H5PUSERSCORE_VERSION );
	wp_enqueue_script( 'H5PUSERSCORE', plugins_url( '/js/h5p-user-score.js', __FILE__ ), array( 'jquery' ), H5PUSERSCORE_VERSION );

	// Pass variables to JavaScript
	wp_localize_script( 'H5PUSERSCORE', 'wpAJAXurl', admin_url( 'admin-ajax.php' ) );
	wp_localize_script( 'H5PUSERSCORE', 'blogId', [ get_current_blog_id() ] );
}

/**
 * Activate the plugin.
 */
function on_activation() {
	Database::build_tables();
}

/**
 * Deactivate the plugin.
 */
function on_deactivation() {
}

/**
 * Uninstall the plugin.
 */
function on_uninstall() {
	Database::delete_tables();
}

/**
 * Update the plugin.
 */
function update() {
	if ( H5PUSERSCORE_VERSION === get_option( 'H5PUSERSCORE_VERSION' ) ) {
		return;
	}

	update_option( 'H5PUSERSCORE_VERSION', H5PUSERSCORE_VERSION );
}

/**
 * Get maximum score for content type from database.
 */
function get_max_score() {
	global $wpdb;

	$data = $_REQUEST['data'];

	if ( ! isset( $data ) ) {
		wp_die();
	}

	$data = json_decode( Util::transform_js_json_string( $data ), true );

	if ( ! isset( $data['contentId'] ) ) {
		wp_die();
	}

	exit( Database::get_max_score( $data['contentId'] ) );

	wp_die();
}

/**
 * Get maximum scores for all content types from database.
 */
function get_max_scores() {
	global $wpdb;

	exit( json_encode( Database::get_max_scores() ) );

	wp_die();
}

/**
 * Set maximum score for content type in database.
 */
function set_max_score() {
	global $wpdb;

	$data = $_REQUEST['data'];

	if ( ! isset( $data ) ) {
		wp_die();
	}

	$data = json_decode( Util::transform_js_json_string( $data ), true );

	if ( ! isset( $data['contentId'] ) ) {
		wp_die();
	}

	if ( ! isset( $data['scoreMax'] ) ) {
		$data['scoreMax'] = null;
	}

	Database::set_max_score( $data['contentId'], $data['scoreMax'] );

	wp_die();
}

/**
 * Load the text domain for internationalization.
 */
function h5puserscore_load_plugin_textdomain() {
	load_plugin_textdomain( 'H5PUSERSCORE', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}

/**
 * Initialize shortcode.
 */
function shortcodes_init() {
	add_shortcode( 'h5pScore', 'H5PUSERSCORE\handle_shortcode' );
}

/**
 * Handle shortcode.
 */
function handle_shortcode( $atts = [], $content = null ) {
	if ( ! isset( $atts['value'] ) ) {
		return '-';
	}

	// Detect action
	if ( 'score' === $atts['value'] ) {
		$action = 'score';
	} elseif ( 'maxScore' === $atts['value'] ) {
		$action = 'max-score';
	} elseif ( 'percentage' === $atts['value'] ) {
		$action = 'percentage';
	} else {
		$action = 'none';
	}

	// Detect id
	$content_id = ( isset( $atts['id'] ) ) ? $atts['id'] : -1;

	return '<span class="h5p-user-score-' . $action . '" data-h5p-content-id="' . $content_id . '">-</span>';
}

// Start setup
register_activation_hook( __FILE__, 'H5PUSERSCORE\on_activation' );
register_deactivation_hook( __FILE__, 'H5PUSERSCORE\on_deactivation' );
register_uninstall_hook( __FILE__, 'H5PUSERSCORE\on_uninstall' );

add_action( 'the_post', 'H5PUSERSCORE\setup' );

// AJAX handlers
add_action( 'wp_ajax_nopriv_set_max_score', 'H5PUSERSCORE\set_max_score' );
add_action( 'wp_ajax_set_max_score', 'H5PUSERSCORE\set_max_score' );
add_action( 'wp_ajax_nopriv_get_max_score', 'H5PUSERSCORE\get_max_score' );
add_action( 'wp_ajax_get_max_score', 'H5PUSERSCORE\get_max_score' );
add_action( 'wp_ajax_nopriv_get_max_scores', 'H5PUSERSCORE\get_max_scores' );
add_action( 'wp_ajax_get_max_scores', 'H5PUSERSCORE\get_max_scores' );

add_action( 'init', 'H5PUSERSCORE\shortcodes_init' );
add_action( 'plugins_loaded', 'H5PUSERSCORE\h5puserscore_load_plugin_textdomain' );
add_action( 'plugins_loaded', 'H5PUSERSCORE\update' );

if ( is_admin() ) {
}
