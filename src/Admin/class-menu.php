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
		add_action( 'wp_ajax_mailhealth_lite_dismiss_upgrade', array( __CLASS__, 'ajax_dismiss_upgrade' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_action( 'wp_ajax_mailhealth_lite_send_test', array( __CLASS__, 'ajax_send_test' ) );
		add_action( 'wp_ajax_mailhealth_lite_test_smtp', array( __CLASS__, 'ajax_test_smtp' ) );
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
		add_settings_section( 'mh_main', 'SMTP Settings', array( __CLASS__, 'render_settings_section' ), 'mailhealth-lite' );
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
		foreach ( $fields as $field_args ) {
			add_settings_field(
				'mh_' . $field_args[0],
				esc_html( $field_args[2] ),
				array( __CLASS__, 'render_settings_field' ),
				'mailhealth-lite',
				'mh_main',
				$field_args
			);
		}
	}

	/**
	 * Render a settings field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public static function render_settings_field( $args ) {
		$options = get_option( 'mailhealth_lite_settings', array() );
		$key     = $args[0];
		$type    = $args[1];
		$default = $args[3] ?? '';
		$value   = $options[ $key ] ?? $default;

		if ( 'checkbox' === $type ) {
			printf( '<label><input type="checkbox" name="mailhealth_lite_settings[%s]" value="1" %s/> %s</label>', esc_attr( $key ), checked( ! empty( $value ), true, false ), 'Enable' );
		} elseif ( 'select' === $type ) {
			$choices = $args[4] ?? array();
			echo '<select name="mailhealth_lite_settings[' . esc_attr( $key ) . ']">';
			foreach ( $choices as $k => $label ) {
				printf( '<option value="%s" %s>%s</option>', esc_attr( $k ), selected( $value, $k, false ), esc_html( $label ) );
			}
			echo '</select>';
		} else {
			printf( '<input type="%s" name="mailhealth_lite_settings[%s]" value="%s" class="regular-text" />', esc_attr( $type ), esc_attr( $key ), esc_attr( $value ) );
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
		wp_enqueue_script( 'mailhealth-lite-admin', plugins_url( 'assets/js/admin.js', dirname( __DIR__, 1 ) ), array( 'jquery' ), MAILHEALTH_LITE_VERSION, true );
		wp_localize_script(
			'mailhealth-lite-admin',
			'MHLAjax',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'mh_lite_ajax' ),
			)
		);
		wp_enqueue_style( 'mailhealth-lite', plugins_url( 'assets/css/admin.css', dirname( __DIR__, 1 ) ), array(), MAILHEALTH_LITE_VERSION );
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
		echo '<hr/><h2>Test SMTP Connection</h2>';
		echo '<p><button class="button" id="mh-lite-test-smtp">Test Connection</button> ';
		echo '<span class="description">Test connectivity and authentication with current settings</span></p>';
		echo '<h2>Send Test</h2>';
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
		wp_enqueue_script( 'mailhealth-lite-dns', plugins_url( 'assets/js/dns.js', dirname( __DIR__, 1 ) ), array( 'jquery' ), MAILHEALTH_LITE_VERSION, true );
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
	 * Render settings section
	 *
	 * @return void
	 */
	public static function render_settings_section() {
		echo '<p>Configure SMTP and use the “Send Test” button to verify.</p>';
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

		$to = filter_input( INPUT_POST, 'to', FILTER_SANITIZE_EMAIL );
		if ( empty( $to ) ) {
			$to = get_option( 'admin_email' );
		}

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
	 * Ajax handler for testing SMTP connectivity
	 *
	 * @return void
	 */
	public static function ajax_test_smtp() {
		check_ajax_referer( 'mh_lite_ajax' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
		}

		$settings = array(
			'host'     => sanitize_text_field( $_POST['host'] ?? '' ),
			'port'     => intval( $_POST['port'] ?? 587 ),
			'secure'   => in_array( $_POST['secure'] ?? 'tls', array( 'none', 'tls', 'ssl' ), true ) ? $_POST['secure'] : 'tls',
			'username' => sanitize_text_field( $_POST['username'] ?? '' ),
			'password' => $_POST['password'] ?? '',
		);

		if ( empty( $settings['host'] ) ) {
			wp_send_json_error( array( 'message' => 'SMTP Host is required' ) );
		}

		$result = self::test_smtp_connection( $settings );

		if ( $result['success'] ) {
			wp_send_json_success( array( 'message' => $result['message'] ) );
		} else {
			wp_send_json_error( array( 'message' => $result['message'] ) );
		}
	}

	/**
	 * Test SMTP connection with given settings
	 *
	 * @param array $settings SMTP settings.
	 * @return array Test result with success boolean and message.
	 */
	private static function test_smtp_connection( $settings ) {
		require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
		require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
		require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';

		$mailer = new \PHPMailer\PHPMailer\PHPMailer( true );

		try {
			$mailer->isSMTP();
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$mailer->Host = $settings['host'];
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$mailer->Port = $settings['port'];
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$mailer->SMTPAuth = ! empty( $settings['username'] );
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$mailer->Username = $settings['username'];
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$mailer->Password = $settings['password'];
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$mailer->SMTPSecure = $settings['secure'];
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$mailer->Timeout = 10;
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$mailer->SMTPDebug = 0;

			$t0        = microtime( true );
			$connected = $mailer->smtpConnect();
			$ms        = (int) round( ( microtime( true ) - $t0 ) * 1000 );

			if ( $connected ) {
				$mailer->smtpClose();
				\MailHealthLite\Core\Logger::log(
					array(
						'context'    => 'smtp_test',
						'status'     => 'ok',
						'latency_ms' => $ms,
						'host'       => $settings['host'],
						'port'       => $settings['port'],
					)
				);
				return array(
					'success' => true,
					'message' => "SMTP connection successful! Connected to {$settings['host']}:{$settings['port']} in {$ms}ms",
				);
			} else {
				return array(
					'success' => false,
					'message' => 'SMTP connection failed. Please check your settings.',
				);
			}
		} catch ( \Exception $e ) {
			\MailHealthLite\Core\Logger::log(
				array(
					'context' => 'smtp_test',
					'status'  => 'fail',
					'error'   => $e->getMessage(),
					'host'    => $settings['host'],
					'port'    => $settings['port'],
				)
			);
			return array(
				'success' => false,
				'message' => 'SMTP Error: ' . $e->getMessage(),
			);
		}
	}

	/**
	 * Ajax handler for dismissing the upgrade notice
	 *
	 * @return void
	 */
	public static function ajax_dismiss_upgrade() {
		check_ajax_referer( 'mh_lite_ajax' );
		update_user_meta( get_current_user_id(), 'mailhealth_lite_dismiss_upgrade', 1 );
		wp_die( 'ok' );
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
	}
}
