<?php
namespace BEAPI\Clear_Opcache;

class Clear_Opcache {

	private $secret = RESET_OPCACHE_SECRET;

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'clear_opcache'] );
		if ( defined( 'WP_CLI' ) ) {
			\WP_CLI::add_command( 'clear-opcache', [ $this, 'clear_command'] );
		}
	}

	/**
	 * Reset opcache when a request is received with a specific key
	 *
	 * @author Ingrid Azéma
	 */
	function clear_opcache() {
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
	 * ## EXAMPLES
	 *
	 *  wp clear-opcache --url=https://uat.kiloutou.com --host=uat.kiloutou.com --servers=integ-www-01a,integ-www-01b
	 *
	 * @param array $args
	 * @param array $assoc_args
	 * @throws \WP_CLI\ExitException
	 * @author Ingrid Azéma
	 */
	public function clear_command( $args, $assoc_args ) {
		if ( empty( $assoc_args ) || ( empty( $assoc_args['host'] ) && empty( $assoc_args['servers'] ) ) ) {
			\WP_CLI::error( 'Please provide a host name (ex. --host=uat.kiloutou.com) and at least 1 server-name (ex. --servers=integ-www-01a)'  );
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
// Constant to be defined as env var
if ( ! defined( 'RESET_OPCACHE_SECRET' ) ) {
	return;
}
new Clear_Opcache();
