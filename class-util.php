<?php

namespace H5PUSERSCORE;

/**
 * Utility functions
 *
 * @package H5PUSERSCORE
 * @since 0.1
 */
class Util {

	/**
	 * Transform a JSON string from JavaScript JSON.stringify for PHP.
	 * @param {string} $data Stringified JSON data.
	 * @return {string} String ready for JSON_decode.
	 */
	public static function transform_js_json_string( $data ) {
		$data = str_replace( '\"', '"', $data );
		$data = str_replace( "\'", "'", $data );
		$data = str_replace( '\\\\"', '&#x22;', $data );

		return $data;
	}

	/**
	 * Create a UUID.
	 * @return string UUID.
	 */
	public static function create_uuid() {
		// Initialize mt_rand with seed
		mt_srand( crc32( serialize( [ microtime( true ), 'USER_IP', 'ETC' ] ) ) );
		return sprintf(
			'%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0x0fff ) | 0x4000,
			mt_rand( 0, 0x3fff ) | 0x8000,
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xffff )
		);
	}
}
