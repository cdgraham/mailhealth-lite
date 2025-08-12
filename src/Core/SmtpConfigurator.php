<?php
namespace MailHealthLite\Core;

class SmtpConfigurator {
    public static function init(): void { add_action('phpmailer_init',[__CLASS__,'configure']); }
    public static function configure($phpmailer): void {
        $s = get_option('mailhealth_lite_settings', []);
        if (empty($s['enabled'])) return;
        $phpmailer->isSMTP();
        $phpmailer->Host = $s['host'] ?? '';
        $phpmailer->Port = (int) ($s['port'] ?? 587);
        $phpmailer->SMTPAuth = !empty($s['username']);
        $phpmailer->Username = $s['username'] ?? '';
        $phpmailer->Password = $s['password'] ?? '';
        $phpmailer->SMTPSecure = $s['secure'] ?? 'tls';
        if (!empty($s['from'])) $phpmailer->setFrom($s['from'], $s['from_name'] ?? '');
    }
}
