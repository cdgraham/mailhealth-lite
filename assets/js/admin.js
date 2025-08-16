(function($){
  $(document).on('click','#mh-lite-test-smtp',function(e){
    e.preventDefault();
    var $b = $(this).prop('disabled', true).text('Testing...');
    
    // Gather current form values
    var settings = {
      action: 'mailhealth_lite_test_smtp',
      _ajax_nonce: MHLAjax.nonce,
      host: $('input[name="mailhealth_lite_settings[host]"]').val(),
      port: $('input[name="mailhealth_lite_settings[port]"]').val(),
      secure: $('select[name="mailhealth_lite_settings[secure]"]').val(),
      username: $('input[name="mailhealth_lite_settings[username]"]').val(),
      password: $('input[name="mailhealth_lite_settings[password]"]').val()
    };
    
    $.post(MHLAjax.ajaxurl, settings)
      .done(function(res){ 
        alert('✓ ' + (res.data && res.data.message ? res.data.message : 'Connection successful')); 
      })
      .fail(function(xhr){ 
        alert('✗ ' + ((xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) || 'Connection failed')); 
      })
      .always(function(){ $b.prop('disabled', false).text('Test Connection'); });
  });

  $(document).on('click','#mh-lite-send-test',function(e){
    e.preventDefault();
    var to = $('#mh-lite-test-to').val() || '';
    var $b = $(this).prop('disabled', true).text('Sending...');
    $.post(MHLAjax.ajaxurl, { action:'mailhealth_lite_send_test', _ajax_nonce:MHLAjax.nonce, to: to })
      .done(function(res){ alert(res.data && res.data.message ? res.data.message : 'Done'); })
      .fail(function(xhr){ alert((xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) || 'Failed'); })
      .always(function(){ $b.prop('disabled', false).text('Send Test'); });
  });

  $(document).on("click", ".mailhealth-lite-upgrade .notice-dismiss", function () {
    $.post(MHLAjax.ajaxurl, {
        action: "mailhealth_lite_dismiss_upgrade",
        _ajax_nonce: MHLAjax.nonce,
    });
  });
})(jQuery);
