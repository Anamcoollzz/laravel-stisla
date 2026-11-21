(function (window, $) {
  'use strict';

  var config = window.StislaCrud || {};

  function showFlashAlerts() {
    if (typeof window.swal !== 'function') {
      return;
    }

    if (config.flashSuccess) {
      window.swal({
        title: config.successTitle || 'Success',
        text: config.flashSuccess,
        icon: 'success',
        timer: 2500,
        buttons: false
      });
    } else if (config.flashError) {
      window.swal({
        title: config.errorTitle || 'Error',
        text: config.flashError,
        icon: 'error'
      });
    }
  }

  function bindDeleteConfirmation() {
    $(document).on('click', '.btn-delete', function (e) {
      e.preventDefault();

      var $button = $(this);
      var $form = $button.closest('form');

      if (!$form.length) {
        return;
      }

      var title = $button.data('confirm-title') || $form.data('confirm-title') || config.title || 'Are you sure?';
      var text = $button.data('confirm-text') || $form.data('confirm-text') || config.text || 'Once deleted, you will not be able to recover this data!';
      var cancelText = $button.data('cancel-text') || $form.data('cancel-text') || config.cancelButtonText || 'Cancel';
      var confirmText = $button.data('confirm-button-text') || $form.data('confirm-button-text') || config.confirmButtonText || 'OK';

      if (typeof window.swal !== 'function') {
        if (window.confirm(text || title)) {
          $form.trigger('submit');
        }
        return;
      }

      window.swal({
        title: title,
        text: text,
        icon: 'warning',
        buttons: [cancelText, confirmText],
        dangerMode: true
      }).then(function (willDelete) {
        if (willDelete) {
          $form.trigger('submit');
        }
      });
    });
  }

  $(function () {
    showFlashAlerts();
    bindDeleteConfirmation();
  });
})(window, window.jQuery);
