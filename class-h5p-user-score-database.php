<?php

namespace H5PUSERSCORE;

/**
 * Database functions
 *
 * @package H5PUSERSCORE
 * @since 0.1
 */
class Database {
	private static $table_main;

	/**
	 * Build the tables of the plugin.
	 */
	public static function build_tables() {
		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$charset_collate = $wpdb->get_charset_collate();

		// naming a row object_id will cause trouble!
		$sql = 'CREATE TABLE ' . self::$table_main . " (
			id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
			id_content MEDIUMINT(9) NOT NULL,
			score_max MEDIUMINT(9),
			PRIMARY KEY (id)
		) $charset_collate;";

		$ok = dbDelta( $sql );
	}

	/**
	 * Delete all tables of the plugin.
	 */
	public static function delete_tables() {
		global $wpdb;

		$wpdb->query( 'DROP TABLE IF EXISTS ' . self::$table_main );
	}

	/**
	 * Insert data into all the database tables and create lookup table.
	 * @param number $content_id H5P content type id.
	 * @param number $score_max Maximum score possible for content type.
	 * @return true|false False on error.
	 */
	public static function set_max_score( $content_id, $score_max ) {
		global $wpdb;

		$exists = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT count(*) FROM ' . self::$table_main . ' WHERE id_content = %d',
				$content_id
			)
		);

		// Set maximum score for content
		if ( '0' === $exists ) {
			$ok = $wpdb->insert(
				self::$table_main,
				array(
					'id_content' => $content_id,
					'score_max' => $score_max
				)
			);
		} else {
			$wpdb->update(
				self::$table_main,
				array(
					'score_max' => $score_max
				),
				array(
					'id_content' => $content_id
				)
			);
		}
	}

	// Get maximum score for content
	public static function get_max_score( $content_id ) {
		global $wpdb;

		return $wpdb->get_var(
			$wpdb->prepare(
				'SELECT score_max FROM ' . self::$table_main . ' WHERE id_content = %d',
				$content_id
			)
		);
	}

	/**
	 * Get maximum scores for all H5Pcontents
	 */
	public static function get_max_scores( ) {
		global $wpdb;

		return $wpdb->get_results(
			'SELECT id_content, score_max FROM ' . self::$table_main
		);
	}

	/**
	 * Initialize class variables/constants
	 */
	static function init() {
		global $wpdb;

		self::$table_main = $wpdb->prefix . 'h5puserscore';
	}
}
Database::init();
