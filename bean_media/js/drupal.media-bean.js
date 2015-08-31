(function ($) {

  Drupal.behaviors.mediaBean = {
    attach: function(context, settings) {
      $('input.fid', context).bind('change', function(e) {
        $.getJSON('/media-bean-options?fid=' + $(this).val(), function(data) {
          var options = '';
          for (var i = 0; i < data.length; i++) {
            options += '<option value="' + data[i].value + '">' + data[i].name + '</option>';
          };
          $('select#edit-file-view-mode').html(options);
        });
      });
    }
  };

})(jQuery);
