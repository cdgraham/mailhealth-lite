(function($){
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
