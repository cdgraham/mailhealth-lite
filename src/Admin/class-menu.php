<?php
/**
 * Admin Menu
 *
 * @package mailhealth-lite
 */

namespace MailHealthLite\Admin;

/**
 * Admin Menu
 */
class Menu {

	/**
	 * Init
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action( 'admin_menu', array( __CLASS__, 'register' ) );
		add_action( 'admin_notices', array( __CLASS__, 'upgrade_notice' ) );
		add_action(
			'wp_ajax_mailhealth_lite_dismiss_upgrade',
			function () {
				check_ajax_referer( 'mh_lite_ajax' );
				update_user_meta( get_current_user_id(), 'mailhealth_lite_dismiss_upgrade', 1 );
				wp_die( 'ok' );
			}
		);
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_action( 'wp_ajax_mailhealth_lite_send_test', array( __CLASS__, 'ajax_send_test' ) );
	}

	/**
	 * Register menu pages
	 *
	 * @return void
	 */
	public static function register(): void {
		add_menu_page( 'MailHealth Lite', 'MailHealth Lite', 'manage_options', 'mailhealth-lite', array( __CLASS__, 'render' ), 'dashicons-email-alt2' );
		add_submenu_page( 'mailhealth-lite', 'DNS Check', 'DNS Check', 'manage_options', 'mailhealth-lite-dns', array( __CLASS__, 'render_dns' ) );
		add_submenu_page( 'mailhealth-lite', 'Logs', 'Logs', 'manage_options', 'mailhealth-lite-logs', array( __CLASS__, 'render_logs' ) );
	}

	/**
	 * Register settings
	 *
	 * @return void
	 */
	public static function register_settings(): void {
		register_setting( 'mailhealth_lite_settings', 'mailhealth_lite_settings', array( 'sanitize_callback' => array( __CLASS__, 'sanitize' ) ) );
		add_settings_section(
			'mh_main',
			'SMTP Settings',
			function () {
				echo '<p>Configure SMTP and use the “Send Test” button to verify.</p>';
			},
			'mailhealth-lite'
		);
		$fields = array(
			array( 'enabled', 'checkbox', 'Enable SMTP override' ),
			array( 'host', 'text', 'SMTP Host' ),
			array( 'port', 'number', 'SMTP Port', 587 ),
			array(
				'secure',
				'select',
				'Security',
				'tls',
				array(
					'none' => 'None',
					'tls'  => 'TLS',
					'ssl'  => 'SSL',
				),
			),
			array( 'username', 'text', 'Username' ),
			array( 'password', 'password', 'Password' ),
			array( 'from', 'email', 'From Email' ),
			array( 'from_name', 'text', 'From Name' ),
		);
		foreach ( $fields as $f ) {
			add_settings_field(
				'mh_' . $f[0],
				esc_html( $f[2] ),
				function () use ( $f ) {
					$o    = get_option( 'mailhealth_lite_settings', array() );
					$key  = $f[0];
					$type = $f[1];
					$val  = $o[ $key ] ?? ( $f[3] ?? '' );
					if ( 'checkbox' === $type ) {
						printf( '<label><input type="checkbox" name="mailhealth_lite_settings[%s]" value="1" %s/> %s</label>', esc_attr( $key ), checked( ! empty( $val ), true, false ), 'Enable' );
					} elseif ( 'select' === $type ) {
						$choices = $f[4] ?? array();
						echo '<select name="mailhealth_lite_settings[' . esc_attr( $key ) . ']">';
						foreach ( $choices as $k => $lab ) {
							printf( '<option value="%s" %s>%s</option>', esc_attr( $k ), selected( $val, $k, false ), esc_html( $lab ) );
						}
						echo '</select>';
					} else {
						printf( '<input type="%s" name="mailhealth_lite_settings[%s]" value="%s" class="regular-text" />', esc_attr( $type ), esc_attr( $key ), esc_attr( $val ) );
					}
				},
				'mailhealth-lite',
				'mh_main'
			);
		}
	}

	/**
	 * Sanitize settings
	 *
	 * @param array $in Input.
	 * @return array
	 */
	public static function sanitize( $in ) {
		return array(
			'enabled'   => ! empty( $in['enabled'] ) ? 1 : 0,
			'host'      => sanitize_text_field( $in['host'] ?? '' ),
			'port'      => intval( $in['port'] ?? 587 ),
			'secure'    => in_array( $in['secure'] ?? 'tls', array( 'none', 'tls', 'ssl' ), true ) ? $in['secure'] : 'tls',
			'username'  => sanitize_text_field( $in['username'] ?? '' ),
			'password'  => $in['password'] ?? '',
			'from'      => sanitize_email( $in['from'] ?? '' ),
			'from_name' => sanitize_text_field( $in['from_name'] ?? '' ),
		);
	}

	/**
	 * Enqueue scripts
	 *
	 * @param string $hook Hook.
	 * @return void
	 */
	public static function enqueue( $hook ) {
		if ( false === strpos( $hook, 'mailhealth-lite' ) ) {
			return;
		}
		wp_enqueue_script( 'mailhealth-lite-admin', plugins_url( 'assets/js/admin.js', dirname( __DIR__, 1 ) ), array( 'jquery' ), '0.9.0', true );
		wp_localize_script(
			'mailhealth-lite-admin',
			'MHLAjax',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'mh_lite_ajax' ),
			)
		);
		wp_enqueue_style( 'mailhealth-lite', plugins_url( 'assets/css/admin.css', dirname( __DIR__, 1 ) ), array(), '0.9.0' );
	}

	/**
	 * Render settings page
	 *
	 * @return void
	 */
	public static function render() {
		echo '<div class="wrap"><h1>MailHealth Lite</h1>';
		echo '<form method="post" action="options.php">';
		settings_fields( 'mailhealth_lite_settings' );
		do_settings_sections( 'mailhealth-lite' );
		submit_button();
		echo '</form>';
		echo '<hr/><h2>Send Test</h2>';
		echo '<p><input type="email" id="mh-lite-test-to" placeholder="Recipient (optional)" class="regular-text" /> ';
		echo '<button class="button button-primary" id="mh-lite-send-test">Send Test</button></p>';
		echo '<p class="description">Want scheduled canary checks, alerts, and blacklist monitoring? <a href="https://example.com/mailhealth" target="_blank">Upgrade to Pro</a>.</p>';
		echo '</div>';
	}

	/**
	 * Render DNS page
	 *
	 * @return void
	 */
	public static function render_dns() {
		echo '<div class="wrap"><h1>DNS Checks</h1><div id="mailhealth-dns-root"><em>Loading...</em></div>';
		wp_enqueue_script( 'mailhealth-lite-dns', plugins_url( 'assets/js/dns.js', dirname( __DIR__, 1 ) ), array( 'jquery' ), '0.9.0', true );
		echo '</div>';
	}

	/**
	 * Render logs page
	 *
	 * @return void
	 */
	public static function render_logs() {
		echo '<div class="wrap"><h1>Email Logs</h1><div id="mailhealth-logs-root">This Lite build stores basic send/test events in the database.</div></div>';
	}

	/**
	 * Ajax handler for sending a test email
	 *
	 * @return void
	 */
	public static function ajax_send_test() {
		check_ajax_referer( 'mh_lite_ajax' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
		}
		$to = sanitize_email( $_POST['to'] ?? get_option( 'admin_email' ) );
		$t0 = microtime( true );
		$ok = wp_mail( $to, '[MailHealth Lite] Test', 'Ping ' . gmdate( 'c' ) );
		$ms = (int) round( ( microtime( true ) - $t0 ) * 1000 );
		\MailHealthLite\Core\Logger::log(
			array(
				'context'    => 'test',
				'status'     => $ok ? 'ok' : 'fail',
				'latency_ms' => $ms,
			)
		);
		if ( $ok ) {
			wp_send_json_success( array( 'message' => "Sent to $to in {$ms}ms" ) );
		} else {
			wp_send_json_error( array( 'message' => 'Failed' ), 500 );
		}
	}

	/**
	 * Upgrade notice
	 *
	 * @return void
	 */
	public static function upgrade_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! $screen || false === strpos( $screen->id, 'mailhealth-lite' ) ) {
			return;
		}
		$dismissed = get_user_meta( get_current_user_id(), 'mailhealth_lite_dismiss_upgrade', true );
		if ( $dismissed ) {
			return;
		}
		$url = 'https://example.com/mailhealth';
		printf(
			'<div class="notice notice-info is-dismissible mailhealth-lite-upgrade"><p><strong>Need monitoring & alerts?</strong> MailHealth Pro adds scheduled canary tests, Slack/Webhook alerts, and blacklist checks. <a class="button button-primary" target="_blank" href="%s">Upgrade to Pro</a></p></div>',
			esc_url( $url )
		);
		// Inline script to mark dismissed.
		printf(
			'<script>jQuery(function($){$(document).on("click",".mailhealth-lite-upgrade .notice-dismiss",function(){jQuery.post(ajaxurl,{action:"mailhealth_lite_dismiss_upgrade",_ajax_nonce:"%s"});});});</script>',
			esc_attr( wp_create_nonce( 'mh_lite_ajax' ) )
		);
	}
}
