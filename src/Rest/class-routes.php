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
		add_action(
			'rest_api_init',
			function () {
				register_rest_route(
					'mailhealth-lite/v1',
					'/dns-check',
					array(
						'methods'             => 'GET',
						'callback'            => array( __CLASS__, 'dns' ),
						'permission_callback' => function () {
							return current_user_can( 'manage_options' );
						},
					)
				);
			}
		);
		add_action( 'admin_init', array( 'MailHealthLite\Core\Logger', 'maybe_create' ) );
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
		return array_values( array_filter( $txts, fn( $t ) => stripos( $t, 'v=spf1' ) === 0 ) );
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
	public static function dns( $req ) {
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'wp_rest' ) ) {
			return new \WP_Error( 'invalid_nonce', __( 'Nonce is invalid.', 'mailhealth-lite' ), array( 'status' => 403 ) );
		}
		$domain = wp_unslash( $_GET['domain'] ?? '' );
		if ( empty( $domain ) ) {
			$domain = wp_parse_url( home_url(), PHP_URL_HOST );
		}
		if ( ! preg_match( '/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i', $domain ) ) {
			return new \WP_Error( 'invalid_domain', __( 'Invalid domain format.', 'mailhealth-lite' ), array( 'status' => 400 ) );
		}
		$spf    = self::spf( $domain );
		$dmarc  = self::dmarc( $domain );
		return array(
			'domain' => $domain,
			'spf'    => $spf,
			'dmarc'  => $dmarc,
		);
	}
}
