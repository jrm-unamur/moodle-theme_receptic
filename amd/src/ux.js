/**
 * Created by jmeuriss on 3/02/16.
 */
/* jshint ignore:start */
define(['jquery', 'core/log'], function($, log) {

    "use strict";

    return {
        init: function($args) {
            log.debug('UnamurUI AMD module initialized');
            $(document).ready(function($) {
                // ... $('body').scrollTop(0).
                var sectiontoggles = JSON.parse($args.sectionstoggle);

                setTimeout(function () {
                    for (var section in sectiontoggles) {
                        section = '#collapse-' + parseInt(section);
                        $(section).collapse('show');
                    }
                }, 0);
                // ... $('.summarytext').remove().

                $('.block_myoverview [data-action="more-courses"]').removeClass('hidden');

                $("body").on( "click", ".toggleblocks", function() {
                    if($('body').hasClass('blocks-hidden')) {
                        M.util.set_user_preference('blocks-collapsed', 'false');
                    } else {
                        M.util.set_user_preference('blocks-collapsed', 'true');
                    }
                    $('body').toggleClass('blocks-hidden');
                });

                // Handle toggle all sections.
                $('body').on('click', '.expandall', function(event) {
                    event.preventDefault();
                    var target = event.target;
                    $(target).removeClass('expandall').addClass('collapseall').html(M.util.get_string('collapseall', 'moodle'));

                    $('.sectiontoggle').each(function(index) {
                        var section = '#collapse-' + (index + 1);
                        $(section).collapse('show');
                    });
                });

                $('body').on('click', '.collapseall', function(event) {
                    event.preventDefault();
                    var target = event.target;
                    $(target).removeClass('collapseall').addClass('expandall').html(M.util.get_string('expandall', 'moodle'));
                    $('.sectiontoggle').each(function(index) {
                        var section = '#collapse-' + (index + 1);
                        $(section).collapse('hide');
                    });
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

                $('div.media').first().on('click', function() {
                    $('html, body').animate({scrollTop: 150}, 50);
                    return false;
                });

                $('div.media').on('click', function(event) {
                    var href = $(event.target).parent().parent().parent().attr('href');
                    var index = href.substring(href.lastIndexOf('-') + 1);
                    var attr = '#collapse-' + index;
                    $(attr).collapse('show');
                });

                $('.collapse').on('show.bs.collapse', function(event) {
                    var sectionstringid = $(event.target).attr('id');
                    var sectionid = sectionstringid.substring(sectionstringid.lastIndexOf('-') + 1);
                    if (!sectiontoggles.hasOwnProperty(sectionid)) {
                        sectiontoggles[sectionid] = "true";
                        M.util.set_user_preference('sections-toggle-' + $args.course, JSON.stringify(sectiontoggles));
                    }
                    /*$(event.target).siblings('div.section-summary-activities').each(function(i, item) {
                        $(item).hide();
                    });*/
                });

                $('.collapse').on('hide.bs.collapse', function(event) {
                    var sectionstringid = $(event.target).attr('id');
                    var sectionid = sectionstringid.substring(sectionstringid.lastIndexOf('-') + 1);
                    if (sectiontoggles.hasOwnProperty(sectionid)) {
                        delete sectiontoggles[sectionid];
                        M.util.set_user_preference('sections-toggle-' + $args.course, JSON.stringify(sectiontoggles));
                    }
                    /*$(event.target).siblings('div.section-summary-activities').each(function(i, item) {
                        $(item).show();
                    });*/
                });
            });
        }
    };
});
/* jshint ignore:end */
