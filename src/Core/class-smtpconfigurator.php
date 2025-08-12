<?php
/**
 * SMTP Configurator
 *
 * @package mailhealth-lite
 */

namespace MailHealthLite\Core;

/**
 * SMTP Configurator
 */
class SmtpConfigurator {
	/**
	 * Init
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action( 'phpmailer_init', array( __CLASS__, 'configure' ) );
	}

	/**
	 * Configure PHPMailer
	 *
	 * @param \PHPMailer\PHPMailer\PHPMailer $phpmailer PHPMailer instance.
	 * @return void
	 */
	public static function configure( $phpmailer ): void {
		$s = get_option( 'mailhealth_lite_settings', array() );
		if ( empty( $s['enabled'] ) ) {
			return;
		}
		$phpmailer->isSMTP();
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$phpmailer->Host = $s['host'] ?? '';
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$phpmailer->Port = (int) ( $s['port'] ?? 587 );
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$phpmailer->SMTPAuth = ! empty( $s['username'] );
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$phpmailer->Username = $s['username'] ?? '';
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$phpmailer->Password = $s['password'] ?? '';
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$phpmailer->SMTPSecure = $s['secure'] ?? 'tls';
		if ( ! empty( $s['from'] ) ) {
			$phpmailer->setFrom( $s['from'], $s['from_name'] ?? '' );
		}
	}
}
