<?php
/*
 Plugin Name: test
 Plugin URI: https://beapi.fr
 Description: test
 Author: Be API
 Author URI: https://beapi.fr

 ----

 Copyright 2021 Be API Technical team (humans@beapi.fr)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/
namespace BEAPI\Clear_Opcache;

class Clear_Opcache {

	private $secret = 'yuriltgvurdgmxdoxmgvqwdbkkzvloeo';

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
new Clear_Opcache();
