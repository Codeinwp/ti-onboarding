<?php
/**
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      2019-04-15
 * @package class-themeisle-ob-import-logger.php
 */

/**
 * Class Themeisle_OB_WP_Import_Logger
 */
class Themeisle_OB_WP_Import_Logger {

	/**
	 * Emojis mapped for each case.
	 *
	 * @var array
	 */
	private $icon_map = array(
		'success'  => '✅',
		'warning'  => '⚠️',
		'progress' => '🔵',
		'error'    => '🔴️',
		'generic'  => '⚪️',
	);

	/**
	 * Log file path.
	 *
	 * @var string
	 */
	private $log_file_path;

	/**
	 * Log file name
	 *
	 * @var string
	 */
	private $log_file_name = 'onboarding_log.log';

	/**
	 * @var Themeisle_OB_WP_Import_Logger
	 */
	private static $_instance;

	/**
	 * Themeisle_OB_WP_Import_Logger constructor.
	 */
	public function __construct() {
		if ( ! defined( 'TI_OB_DEBUG_LOG' ) ) {
			define( 'TI_OB_DEBUG_LOG', false );
		}

		if ( TI_OB_DEBUG_LOG !== true ) {
			return;
		}

		$this->set_log_path();
		$this->clear_log();
	}

	/**
	 * Returns the instance of the class.
	 *
	 * @return Themeisle_OB_WP_Import_Logger
	 */
	public static function get_instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Set the log path.
	 */
	private function set_log_path() {
		$wp_upload_dir       = wp_upload_dir( null, false );
		$this->log_file_path = $wp_upload_dir['basedir'] . DIRECTORY_SEPARATOR;

		if ( ! is_dir( $this->log_file_path ) ) {
			wp_mkdir_p( $this->log_file_path );
		}
	}

	/**
	 * Clear the log file.
	 */
	private function clear_log() {
		if ( is_writable( $this->log_file_path . $this->log_file_name ) ) {
			unlink( $this->log_file_path . $this->log_file_name );
		}
	}

	/**
	 * Log entry.
	 *
	 * @param string $message log message.
	 * @param string $type    log type.
	 */
	public function log( $message = 'No message provided.', $type = 'error' ) {
		$log_entry = array(
			'message' => $message,
			'type'    => array_key_exists( $type, $this->icon_map ) ? $this->icon_map[ $type ] : $this->icon_map['generic'],
			'time'    => date( '[d/M/Y:H:i:s]' ),
		);

		file_put_contents( $this->log_file_path . $this->log_file_name, $this->get_log_entry( $log_entry ), FILE_APPEND );
	}

	/**
	 * Get the formatted log entry.
	 *
	 * @return string
	 */
	private function get_log_entry( $log_entry ) {
		return trim( preg_replace( '/\s\s+/', ' ', "{$log_entry['time']} ({$log_entry['type']}): {$log_entry['message']}" ) ) . PHP_EOL;
	}
}
