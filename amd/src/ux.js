/**
 * Created by jmeuriss on 3/02/16.
 */
/* jshint ignore:start */
define(['jquery', 'core/log', 'core/ajax', 'core/notification', 'core/templates'], function($, log, ajax, notification, templates) {

    "use strict";
    var params;
    return {
        init: function(args) {
            log.debug('Theme Receptic AMD module initialized');

            $(document).ready(function($) {
                params = JSON.parse(args);
                if (params.displaymode) {
                    $('.block-myoverview [data-region="courses-view"]').attr('data-display', 'list');
                }

                // Javascript to enable link to tab.
                $('.linktotab').on('click', function(event) {
                    var href = ($(event.currentTarget).attr('href'));
                    $('.nav-tabs a[href="#' + href.split('#')[1] + '"]').tab('show');
                });

                // With HTML5 history API, we can easily prevent scrolling!
                $('.nav-tabs a').on('shown.bs.tab', function (e) {

                    if(history.pushState) {
                        history.pushState(null, null, e.target.hash);
                    } else {
                        // Polyfill for old browsers.
                        window.location.hash = e.target.hash;
                    }
                });

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

                $('body').on('click', '.hideflashboxteachers', function(event) {
                    event.preventDefault();
                    var target = event.target;
                    $(target).parent().parent().parent().slideUp(250);
                    M.util.set_user_preference('flashbox-teacher-hidden', 'true');
                });

                $('body').on('click', '.hideflashboxstudents', function(event) {
                    event.preventDefault();
                    var target = event.target;
                    $(target).parent().parent().parent().slideUp(250);
                    M.util.set_user_preference('flashbox-student-hidden', 'true');
                });

                $('body').on('click', '[data-action="makevisible"]', function(event) {

                    event.preventDefault();
                    var target = event.target;
                    var courseId = $(target).attr('data-course-id');
                    var promises = ajax.call([{
                        methodname: 'theme_receptic_change_course_visibility',
                        args: {
                            'id': courseId,
                            'visible': true
                        }
                    }]);
                    promises[0]
                        .done(function() {
                            $(target).parent().fadeOut(400);
                            var datafortpl = new Array();
                            datafortpl.makevisiblesuccess = true;
                            datafortpl.coursehidden = false;

                            templates.render('theme_receptic/coursewarnings', datafortpl).done(function(html) {
                                $(target).parent().replaceWith(html);
                            }).fail(notification.exception);
                        }).fail(notification.exception);

                    /*var request = {
                        methodname: 'theme_receptic_change_course_visibility',
                        args: args
                    };

                    var promise = Ajax.call([request])[0];

                    return promise;*/
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
            });
        }
    };
});
/* jshint ignore:end */
