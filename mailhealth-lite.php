<?php
/**
 * Plugin Name: MailHealth Lite – SMTP & Deliverability Monitor
 * Description: Lite version: SMTP wizard, manual DNS checks (SPF/DMARC), one-click test email, and a small local log. Upgrade for canary schedules, alerts, blacklist checks, and more.
 * Version: 0.9.0
 * Requires PHP: 7.4
 * Requires at least: 6.3
 * Author: ChilliChalli
 * Author URI: https://www.chillichalli.com
 * Plugin URI: https://www.chillichalli.com
 * License: GPLv2 or later
 * Text Domain: mailhealth-lite
 *
 * @package mailhealth-lite
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MAILHEALTH_LITE_VERSION', '0.9.0' );
define( 'MAILHEALTH_LITE_SLUG', 'mailhealth-lite' );

// Autoloader to support PSR-4 and WordPress-style class names.
spl_autoload_register(function ($class) {
    $prefix = 'MailHealthLite\\';
    if (strpos($class, $prefix) !== 0) {
        return;
    }
    $rel  = substr($class, strlen($prefix));            // e.g. Admin\Menu
    $base = __DIR__ . '/src/';

    // PSR-4 first (Admin/Menu.php)
    $psr = $base . str_replace('\\', '/', $rel) . '.php';
    if (file_exists($psr)) {
        require_once $psr;
        return;
    }

    // WordPress-style fallback (Admin/class-menu.php)
    $parts = explode('\\', $rel);
    $cls   = array_pop($parts);
    $dir   = implode('/', $parts);
    $alt   = rtrim($base . $dir, '/');
    if ($alt !== '') {
        $alt .= '/';
    }
    $alt  .= 'class-' . strtolower($cls) . '.php';
    if (file_exists($alt)) {
        require_once $alt;
        return;
    }
});

add_action(
	'plugins_loaded',
	function () {
		\MailHealthLite\Plugin::init( __FILE__ );
	}
);
