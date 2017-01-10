/**
 * Created by jmeuriss on 3/02/16.
 */
/* jshint ignore:start */
define(['jquery', 'core/log'], function($, log) {

    "use strict";

    return {
        init: function() {
            log.debug('UnamurUI AMD module initialized');
            $(document).ready(function($) {
                $(this).scrollTop(0);

                $("body").on( "click", ".toggleblocks", function() {
                    $('body').toggleClass('noblocks');
                });
                var offset = 50;
                var duration = 500;
                $(window).scroll(function(){
                    if ($(this).scrollTop() > offset) {
                        $('#back-to-top').fadeIn(duration);
                    } else {
                        $('#back-to-top').fadeOut(duration);
                    }
                });
                $('#back-to-top').click(function (event) {
                    event.preventDefault();
                    $('html, body').animate({scrollTop: 0}, duration);
                    return false;
                });
            });
        }
    };
});
/* jshint ignore:end */
