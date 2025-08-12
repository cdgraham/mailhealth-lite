(function($){
  $(function(){
    var root = $('#mailhealth-dns-root');
    if (!root.length) return;
    var rest = (window.wpApiSettings && wpApiSettings.root) || (ajaxurl.replace('admin-ajax.php','rest-route/'));
    root.html('<em>Loading…</em>');
    $.get(rest + 'mailhealth-lite/v1/dns-check').done(function(data){
      var html = '<h2>Domain: '+data.domain+'</h2>';
      html += '<h3>SPF</h3>' + (data.spf && data.spf.length ? '<pre>'+data.spf.join("\n")+'</pre>' : '<p>No SPF TXT found</p>');
      html += '<h3>DMARC</h3>' + (data.dmarc && data.dmarc.length ? '<pre>'+data.dmarc.join("\n")+'</pre>' : '<p>No DMARC TXT found</p>');
      root.html(html);
    }).fail(function(){ root.html('<span style="color:red">Failed to load DNS data</span>'); });
  });
})(jQuery);
