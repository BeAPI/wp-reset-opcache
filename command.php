<?php
namespace BEAPI\Clear_Opcache;

/**
 *
 * Class Clear_Opcache
 * @package BEAPI\Clear_Opcache
 *
 * @author Léonard Phoumpakka
 *
 */
class Clear_Opcache {

	private $secret;

	public function __construct() {
		$this->secret = RESET_OPCACHE_SECRET;
		if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
			add_action( 'plugins_loaded', [ $this, 'clear_opcache' ] );
		}
		if ( defined( 'WP_CLI' ) ) {
			\WP_CLI::add_command( 'clear-opcache', [ $this, 'clear_command'] );
		}
	}

	/**
	 * Reset opcache when a request is received with a specific key
	 *
	 * @author Ingrid Azéma
	 */
	public function clear_opcache() {
		if ( isset( $_GET['secret'] ) && $this->secret === $_GET['secret'] ) {
			if ( opcache_reset() ) {
				status_header( 202 );
			} else {
				status_header( 400 );
			}
			die;
		}
	}

	/**
	 * Command to clear opcache on 1 or several servers
	 *
	 * ## OPTIONS
	 * [--host=<host>]
	 * : Hostname (staging.mydomain.com). Required
	 *
	 * [--servers=<servers>]
	 * : List of server names on which opcache must be reset (srv-www-1, srv-www-2). Required
	 *
	 * ## EXAMPLES
	 *
	 *  wp clear-opcache --url=https://staging.mydomain.com --host=staging.mydomain.com --servers=srv-www-1,srv-www-2
	 *
	 * @author Ingrid Azéma
	 */
	public function clear_command( $args, $assoc_args ) {
		// Constant to be defined as env var
		if ( empty( $this->secret ) ) {
			\WP_CLI::error( 'Please define a constant named RESET_OPCACHE_SECRET with a string of your choosing' );
		}

		if ( empty( $assoc_args ) || ( empty( $assoc_args['host'] ) && empty( $assoc_args['servers'] ) ) ) {
			\WP_CLI::error( 'Please provide a host name (ex. --host=staging.mydomain.com) and at least 1 server-name (ex. --servers=srv-www-1,srv-www-2)'  );
		}

		$servers = explode( ',', $assoc_args['servers'] ) ;

		foreach ( $servers as $server_name ) {
			$url = add_query_arg(
				[
					'secret' => $this->secret,
				],
				'http://' . $server_name
			);


			$response = wp_remote_get(
				$url,
				[
					'headers' => [
						'Host' => $assoc_args['host'],
					]
				]
			);
			if ( is_wp_error( $response) ) {
				\WP_CLI::error( sprintf( 'Failed to send clear request to %s', $server_name ), false );
				continue;
			}

			$response_code = wp_remote_retrieve_response_code( $response );
			if ( 202 === $response_code ) {
				\WP_CLI::log( 'Opcache cleared on ' . $server_name );
				continue;
			}
			\WP_CLI::log( 'Failed to clear opcache on ' . $server_name );
		}
	}

}
