(function ($) {

  Drupal.ajax.prototype.commands.facebook_bean_update = function(ajax, response, status) {
    FB.XFBML.parse();
  };

})(jQuery);
