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
                $('body').scrollTop(0);

                $("body").on( "click", ".toggleblocks", function() {
                    if($('body').hasClass('noblocks')) {
                        M.util.set_user_preference('blocks-collapsed', 'false');
                    } else {
                        M.util.set_user_preference('blocks-collapsed', 'true');
                    }
                    $('body').toggleClass('noblocks');
                });


                var offset = 50;
                var duration = 500;
                $(window).scroll(function(){
                    if ($('body').scrollTop() > offset) {
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
                $('div.media').first().on('click', function(event) {
                    event.preventDefault();
                    $('html, body').animate({scrollTop: 150}, 50);
                    return false;
                });
            });
        }
    };
});
/* jshint ignore:end */
