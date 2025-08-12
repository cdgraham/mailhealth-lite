<?php
namespace MailHealthLite\Rest;

class Routes {
    public static function init(): void {
        add_action('rest_api_init', function(){
            register_rest_route('mailhealth-lite/v1','/dns-check',[
                'methods'=>'GET',
                'callback'=>[__CLASS__,'dns'],
                'permission_callback'=> function(){ return current_user_can('manage_options'); }
            ]);
        });
        add_action('admin_init', ['MailHealthLite\Core\Logger','maybeCreate']);
    }
    protected static function spf($domain){
        $recs = dns_get_record($domain, DNS_TXT);
        $txts = array_map(function($r){ return $r['txt'] ?? ''; }, $recs);
        return array_values(array_filter($txts, fn($t)=> stripos($t,'v=spf1')===0));
    }
    protected static function dmarc($domain){
        $host = '_dmarc.'.$domain;
        $recs = dns_get_record($host, DNS_TXT);
        $txts = array_map(function($r){ return $r['txt'] ?? ''; }, $recs);
        return $txts;
    }
    public static function dns($req){
        $domain = sanitize_text_field($_GET['domain'] ?? parse_url(home_url(), PHP_URL_HOST));
        $spf = self::spf($domain);
        $dmarc = self::dmarc($domain);
        return ['domain'=>$domain, 'spf'=>$spf, 'dmarc'=>$dmarc];
    }
}
