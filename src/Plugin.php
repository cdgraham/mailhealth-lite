<?php
namespace MailHealthLite;

class Plugin {
    public static function init($main_file): void {
        load_plugin_textdomain('mailhealth-lite', false, dirname(plugin_basename($main_file)).'/languages');
        Admin\Menu::init();
        Core\SmtpConfigurator::init();
        Core\Logger::init();
        Rest\Routes::init();
        // No cron canary in Lite to keep it simple and avoid background load.
    }
}
