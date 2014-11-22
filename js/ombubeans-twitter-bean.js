(function ($) {

  if (Drupal.ajax) {
    Drupal.ajax.prototype.commands.twitter_bean_update = function(ajax, response, status) {
      $.getScript('//platform.twitter.com/widgets.js');
    };
  }

  $(document).on('tiles.requestSuccess', function(e, tile) {
    $.getScript('//platform.twitter.com/widgets.js');
  });

})(jQuery);
