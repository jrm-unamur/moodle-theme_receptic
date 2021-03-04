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
                    $('.block-myoverview [data-region="courses-view"]').attr('data-display', params.displaymode);
                }

                $('body').on('click', '.hideflashbox1', function(event) {
                    event.preventDefault();
                    var target = event.target;
                    $(target).parent().parent().parent().slideUp(250);
                    M.util.set_user_preference('flashbox1-hidden', 'true');
                });

                $('body').on('click', '.hideflashbox2', function(event) {
                    event.preventDefault();
                    var target = event.target;
                    $(target).parent().parent().parent().slideUp(250);
                    M.util.set_user_preference('flashbox2-hidden', 'true');
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

                    if( href && href.lastIndexOf('#section-') != -1) {

                        var index = href.substring(href.lastIndexOf('#section-') + 1);

                        var attr = '#collapse-' + index;

                        $(attr).collapse('show');
                    }

                });

                if (params.pictureuploaddeactivated) {
                    var edituserpicturefieldset = $('#page-user-edit #id_moodle_picture');
                    if (edituserpicturefieldset) {
                        var message = M.util.get_string('nopictureupload', 'theme_receptic');
                        var afterelement = $('#fitem_id_currentpicture').parent();
                        $('<div class="alert alert-warning">' +
                            '<i class="fa fa-exclamation-circle fa-3x fa-pull-left mr-3"></i>' +
                            message +
                            '</div>').insertBefore(afterelement);
                        $('#fitem_id_imagefile').remove();
                        if (!params.haspicture) {
                            $('#fitem_id_imagealt').remove();
                        }
                    }
                }
            });
        }
    };
});
/* jshint ignore:end */
