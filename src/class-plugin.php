<?php
/**
 * Plugin
 *
 * @package mailhealth-lite
 */

namespace MailHealthLite;

/**
 * Plugin
 */
class Plugin {
	/**
	 * Init
	 *
	 * @param string $main_file Main file.
	 * @return void
	 */
	public static function init( $main_file ): void {
		Admin\Menu::init();
		Core\SmtpConfigurator::init();
		Core\Logger::init();
		Rest\Routes::init();
		// No cron canary in Lite to keep it simple and avoid background load.
	}
}
