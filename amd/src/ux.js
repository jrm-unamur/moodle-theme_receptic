/**
 * Created by jmeuriss on 3/02/16.
 */
/* jshint ignore:start */
define(['jquery', 'core/log'], function($, log) {

    "use strict";

    return {
        init: function() {
            log.debug('Theme Receptic AMD module initialized');

            $(document).ready(function($) {

                // Javascript to enable link to tab
                $('.linktotab').on('click', function(event) {
//console.log(event);
                    var href = ($(event.currentTarget).attr('href'));
                    $('.nav-tabs a[href="#'+href.split('#')[1]+'"]').tab('show');
                });

                // With HTML5 history API, we can easily prevent scrolling!
                $('.nav-tabs a').on('shown.bs.tab', function (e) {

                    if(history.pushState) {
                        history.pushState(null, null, e.target.hash);
                    } else {
                        window.location.hash = e.target.hash; //Polyfill for old browsers
                    }
                });

                var isCtrl = false;
                var isShift = false;
                // action on key up
                $(document).keyup(function(e) {
                    if(e.which == 17) {
                        isCtrl = false;
                    }
                    if(e.which == 16) {
                        isShift = false;
                    }
                });
                // action on key down
                $(document).keydown(function(e) {
                    if(e.which == 17) {
                        isCtrl = true;
                    }
                    if(e.which == 16) {
                        isShift = true;
                    }
                    if(e.which == 120 && isCtrl && isShift) {
                        if($('body').hasClass('drawer-open-right')) {
                            M.util.set_user_preference('sidepre-open', 'false');
                        } else {
                            M.util.set_user_preference('sidepre-open', 'true');
                        }
                        $('body').toggleClass('drawer-open-right');
                    }
                });

                //Disable shortname, fullname and category for course edit form
                $('#page-course-edit:not(.format-site) #id_fullname').prop('readonly', true);
                $('#page-course-edit:not(.format-site) #id_shortname').prop('readonly', true);
                $('#page-course-edit:not(.format-site) #id_category').prop('readonly', true);

                $('body').on('click', '.hideflashboxteachers', function(event) {
                    event.preventDefault();
                    var target = event.target;
                    $(target).parent().parent().parent().slideUp(250);
                    M.util.set_user_preference('flashbox-teacher-hidden', 'true');
                });

                $('body').on('click', '.hideflashboxstudents', function(event) {
                    event.preventDefault();
                    var target = event.target;
                    $(target).parent().parent().slideUp(250);
                    M.util.set_user_preference('flashbox-student-hidden', 'true');
                });

                var offset = 200;
                var duration = 700;
                $(window).scroll(function(){
                    if ($(window).scrollTop() > offset) {
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

                $('#nav-drawer div.media').on('click', function(event) {
                    var href = $(event.target).parent().parent().parent().attr('href');

                    if( href.lastIndexOf('#section-') != -1) {

                        var index = href.substring(href.lastIndexOf('#section-') + 1);

                        var attr = '#collapse-' + index;

                        $(attr).collapse('show');
                    }

                });

                $('#chooserform #item_lesson').parent().parent().remove();
                $('.block-myoverview').show();
            });
        }
    };
});
/* jshint ignore:end */
