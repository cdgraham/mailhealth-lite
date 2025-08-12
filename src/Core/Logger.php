<?php
namespace MailHealthLite\Core;

class Logger {
    public static function init(): void { add_action('wp_mail_failed', [__CLASS__,'capture']); }
    public static function table(): string { global $wpdb; return $wpdb->prefix.'mailhealth_lite_log'; }
    public static function maybeCreate(): void {
        global $wpdb;
        $table = self::table();
        $charset = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            ts DATETIME NOT NULL,
            context VARCHAR(20) NOT NULL,
            status VARCHAR(10) NOT NULL,
            latency_ms INT NULL,
            message TEXT NULL,
            PRIMARY KEY (id),
            KEY ts_idx (ts)
        ) $charset;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
    public static function log(array $row): void {
        global $wpdb;
        $row = array_merge(['ts'=> current_time('mysql'), 'context'=>'test', 'status'=>'ok'], $row);
        $wpdb->insert(self::table(), $row);
    }
    public static function capture($wp_error): void {
        self::log(['context'=>'send','status'=>'fail','message'=>$wp_error->get_error_message()]);
    }
}
