(function ($) {

  Drupal.ajax.prototype.commands.twitter_bean_update = function(ajax, response, status) {
    $.getScript('//platform.twitter.com/widgets.js');
  };

})(jQuery);
