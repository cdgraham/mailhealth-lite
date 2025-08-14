<?php
/**
 * Rest routes
 *
 * @package mailhealth-lite
 */

namespace MailHealthLite\Rest;

/**
 * Rest routes
 */
class Routes {
	/**
	 * Init
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
		add_action( 'admin_init', array( 'MailHealthLite\Core\Logger', 'maybe_create' ) );
	}

	/**
	 * Register REST routes
	 *
	 * @return void
	 */
	public static function register_routes(): void {
		register_rest_route(
			'mailhealth-lite/v1',
			'/dns-check',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'dns' ),
				'permission_callback' => array( __CLASS__, 'check_permissions' ),
				'args'                => array(
					'domain' => array(
						'validate_callback' => function ( $param ) {
							return preg_match( '/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i', $param );
						},
					),
				),
			)
		);
	}

	/**
	 * Check permissions for REST routes
	 *
	 * @return bool
	 */
	public static function check_permissions(): bool {
		return current_user_can( 'manage_options' );
	}
	/**
	 * SPF
	 *
	 * @param string $domain Domain.
	 * @return array
	 */
	protected static function spf( $domain ) {
		$recs = dns_get_record( $domain, DNS_TXT );
		$txts = array_map(
			function ( $r ) {
				return $r['txt'] ?? '';
			},
			$recs
		);
		return array_values(
			array_filter(
				$txts,
				function ( $t ) {
					return stripos( $t, 'v=spf1' ) === 0;
				}
			)
		);
	}
	/**
	 * DMARC
	 *
	 * @param string $domain Domain.
	 * @return array
	 */
	protected static function dmarc( $domain ) {
		$host = '_dmarc.' . $domain;
		$recs = dns_get_record( $host, DNS_TXT );
		$txts = array_map(
			function ( $r ) {
				return $r['txt'] ?? '';
			},
			$recs
		);
		return $txts;
	}
	/**
	 * DNS
	 *
	 * @param \WP_REST_Request $req Request.
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 * @return array
	 */
	public static function dns( \WP_REST_Request $req ) {
		$domain = $req->get_param( 'domain' );
		if ( empty( $domain ) ) {
			$domain = wp_parse_url( home_url(), PHP_URL_HOST );
		}
		$spf   = self::spf( $domain );
		$dmarc = self::dmarc( $domain );
		return array(
			'domain' => $domain,
			'spf'    => $spf,
			'dmarc'  => $dmarc,
		);
	}
}
