(function ($) {

  Drupal.behaviors.FacebookBean = {
    attach: function(context, settings) {
      // Trigger any new Facebook loading.
      if ($('.facebook-bean-wrapper', context)) {
        FB.XFBML.parse();
      }
    }
  };

})(jQuery);
