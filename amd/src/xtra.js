/**
 * Created by jmeuriss on 20/06/19.
 */
/**
 * Created by jmeuriss on 3/02/16.
 */
/* jshint ignore:start */
define(['jquery', 'core/log'], function($, log) {

    "use strict";
    return {
        init: function() {
            log.debug('Xtra AMD module initialized');

            $(document).ready(function($) {
                // Javascript to enable link to tab.
                $('.linktotab').on('click', function(event) {
                    var href = ($(event.currentTarget).attr('href'));
                    $('.nav-tabs a[href="#' + href.split('#')[1] + '"]').tab('show');
                });

                // With HTML5 history API, we can easily prevent scrolling!
                // Activate only for passeports site.
                /*$('.nav-tabs a').on('shown.bs.tab', function (e) {
                    if(history.pushState) {
                        console.log(e.target.hash);
                        history.pushState(null, null, e.target.hash);
                    } else {
                        // Polyfill for old browsers.
                        window.location.hash = e.target.hash;
                    }
                });*/

                var isCtrl = false;
                var isShift = false;
                // Action on key up.
                $(document).keyup(function(e) {
                    if(e.which == 17) {
                        isCtrl = false;
                    }
                    if(e.which == 16) {
                        isShift = false;
                    }
                });
                // Action on key down.
                $(document).keydown(function(e) {
                    if(e.which == 17) {
                        isCtrl = true;
                    }
                    if(e.which == 16) {
                        isShift = true;
                    }
                    if(e.which == 120 && isCtrl && isShift) {
                        if($('body').hasClass('drawer-open-right')) {
                            M.util.set_user_preference('blocks-column-open', 'false');
                        } else {
                            M.util.set_user_preference('blocks-column-open', 'true');
                        }
                        $('body').toggleClass('drawer-open-right');
                    }
                });

                // Disable shortname, fullname and category in course edit form.
                $('#page-course-edit:not(.format-site) #id_fullname').prop('readonly', true);
                $('#page-course-edit:not(.format-site) #id_shortname').prop('readonly', true);
                $('#page-course-edit:not(.format-site) #id_category').prop('readonly', true);

                $('#chooserform #item_lesson').parent().parent().remove();
                $('#chooserform input ~ span.typename:contains("Ubicast")').parent().parent().remove();

                $('.contactdpo').parent().parent().next().hide();
                $('.contactdpo').parent().parent().next().next().hide();
                $('.contactdpo').parent().parent().next().next().next().hide();
                $('[data-action="contactdpo"]').parent().parent().next().hide();
                $('[data-action="contactdpo"]').parent().parent().next().next().hide();
                $('[data-action="contactdpo"]').parent().parent().next().next().next().hide();
                $('[data-action="contactdpo"]').parent().parent().parent().children('li').last().show();
                $('#page-user-profile div.profile_tree section:nth-of-type(2n) ul').show();

            });
        }
    };
});
/* jshint ignore:end */
