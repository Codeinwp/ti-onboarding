<?php
/**
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      2019-04-15
 * @package class-themeisle-ob-import-logger.php
 */

class Themeisle_OB_WP_Import_Logger {

	private $log_file_path;

	private $log_file_name = 'onboarding_log.txt';

	private $log_entries = array();

	/**
	 * Themeisle_OB_WP_Import_Logger constructor.
	 */
	public function __construct() {
		$this->set_log_path();
		$this->clear_log();
		add_action( 'shutdown', array( $this, 'write_log') );
	}

	public function __destruct() {
		$this->write_log();
	}

	private function set_log_path() {
		$wp_upload_dir       = wp_upload_dir( null, false );
		$this->log_file_path = $wp_upload_dir['basedir'] . '/neve-theme/';

		if ( ! is_dir( $this->log_file_path ) ) {
			wp_mkdir_p( $this->log_file_path );
		}
	}

	private function clear_log() {
		if ( is_writable( $this->log_file_path . $this->log_file_name ) ) {
			unlink( $this->log_file_path . $this->log_file_name );
		}
	}

	public function log( $message = 'No message provided.', $type = 'error' ) {
		array_push( $this->log_entries, array(
			'message'   => $message,
			'type'      => $type,
			'time'      => date( '[d/M/Y:H:i:s]' ),
			'microtime' => microtime(),
		) );
	}

	public function write_log() {
		require_once ABSPATH . '/wp-admin/includes/file.php';
		global $wp_filesystem;
		WP_Filesystem();
		$wp_filesystem->put_contents( $this->log_file_path . $this->log_file_name, $this->get_log(), 0644 );
	}

	/**
	 * @return string
	 */
	private function get_log() {
		ob_start();
		foreach ( $this->log_entries as $log_entry ) {
			echo trim( preg_replace( '/\s\s+/', ' ', "{$log_entry['time']} ({$log_entry['type']}): {$log_entry['message']}" ) ) . PHP_EOL;
		}
		$log = ob_get_clean();
		ob_flush();

		return $log;
	}
}
