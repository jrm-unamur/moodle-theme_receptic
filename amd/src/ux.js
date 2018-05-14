/**
 * Created by jmeuriss on 3/02/16.
 */
/* jshint ignore:start */
define(['jquery', 'core/log'], function($, log) {

    "use strict";

    return {
        init: function($args) {
            log.debug('UnamurUI AMD module initialized');

            /*if($('body').hasClass('path-course')) {
                $(window).scroll(function (x) {
                    var viewport_top = $(window).scrollTop();
                    var closest = null;
                    var closest_offset = null;

                    $('.section.main').each(function (e, f) {
                        var this_offset = $(f).offset().top;

                        if ($(closest).offset()) {
                            closest_offset = $(closest).offset().top;
                        }
                        if (closest == null || Math.abs(this_offset - viewport_top) < Math.abs(closest_offset - viewport_top)) {
                            closest = f;
                        }
                    });

                    if(closest) {
                        window.sessionStorage.setItem('closest_id', closest.id);
                        window.sessionStorage.setItem('closest_delta', viewport_top - $(closest).offset().top);
                    }
                });
            }*/

            $(document).ready(function($) {
                /*if($('body').hasClass('path-course')) {
                    var closest_id = window.sessionStorage.getItem('closest_id');
                    var closest_delta = window.sessionStorage.getItem('closest_delta');

                    if (closest_id && closest_delta) {
                        var closest = $('#' + closest_id);
                        $(window).scrollTop(closest.offset().top + parseInt(closest_delta));
                    }
                } else {
                    window.sessionStorage.removeItem('closest_id');
                    window.sessionStorage.removeItem('closest_delta');
                }*/
                // ... $('body').scrollTop(0).
                var sectiontoggles = JSON.parse($args.sectionstoggle);

                setTimeout(function () {
                    for (var section in sectiontoggles) {
                        section = '#collapse-' + parseInt(section);
                        $(section).collapse('show');
                    }
                }, 50);

                /*$('#filter-tabs-titlegroup-1 a').on('click', function (e) {
                    e.preventDefault()
                    $(this).tab('show');

                });*/

                // Javascript to enable link to tab
                $('.linktotab').on('click', function() {

                    var href = ($(this).attr('href'));
                    $('.nav-tabs a[href="#'+href.split('#')[1]+'"]').tab('show');
                    console.log($('.nav-tabs a[href="#'+href.split('#')[1]+'"]'));
                });

               /* $('#filter-tabs-titlegroup-1').load('url', function (result) {
                    $('#tab-id').tab('show');
                });*/

               /* var $tabs = $('.nav-tabs'),
                    $tabsA = $tabs.find('a'),
                    $tabsC = $('.tab-pane'),
                    start = window.location.hash || '';
                console.log(start + 'coucou');

                function deactivate() {
                    $tabsA.removeClass('active');
                    $tabsC.hide();
                }
                function activate(href) {
                    $tabsA.filter('[href="' + href + '"]').addClass('active');
                    console.log('id="' + href.split('#')[1] + '"');
                   var test = $($tabsC.filter('[id="' + href.split('#')[1] + '"]')).fadeIn();
                    console.log(test);
                    //$(href).fadeIn();
                }

                function clicked(e) {
                    var href = $(e.target).attr('href');
                    deactivate();
                    activate(href);
                }

                deactivate();
                activate(start);
                $tabs.on('click', 'a', clicked);*/



            /* if (hash.match('#')) {
                 console.log(url.split('#')[1]);
                 $('.nav-tabs a[href="#'+url.split('#')[1]+'"]').tab('show') ;
             }*/

                // With HTML5 history API, we can easily prevent scrolling!
                $('.nav-tabs a').on('shown.bs.tab', function (e) {

                    if(history.pushState) {
                        history.pushState(null, null, e.target.hash);
                    } else {
                        window.location.hash = e.target.hash; //Polyfill for old browsers
                    }
                })

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
                    // Legacy method with 'blocks-collapsed' body class.
                    /*if(e.which == 120 && isCtrl && isShift) {
                        if($('body').hasClass('blocks-hidden')) {
                            M.util.set_user_preference('blocks-collapsed', 'false');
                        } else {
                            M.util.set_user_preference('blocks-collapsed', 'true');
                        }
                        $('body').toggleClass('blocks-hidden');
                    }*/
                    if(e.which == 120 && isCtrl && isShift) {
                        if($('body').hasClass('drawer-open-right')) {
                            M.util.set_user_preference('sidepre-open', 'false');
                        } else {
                            M.util.set_user_preference('sidepre-open', 'true');
                        }
                        $('body').toggleClass('drawer-open-right');
                    }
                });

                // ... $('.summarytext').remove().
                //Disable shortname, fullname and category for course edit form
                $('#page-course-edit.format-topics #id_fullname').prop('readonly', true);
                $('#page-course-edit.format-topics #id_shortname').prop('readonly', true);
                $('#page-course-edit.format-topics #id_category').prop('readonly', true);
                $('#page-course-edit.format-weeks #id_fullname').prop('readonly', true);
                $('#page-course-edit.format-weeks #id_shortname').prop('readonly', true);
                $('#page-course-edit.format-weeks #id_category').prop('readonly', true);
                $('#page-course-edit.format-topics #id_coursedisplay').parent().parent().addClass('hidden');
                $('#page-course-edit.format-weeks #id_coursedisplay').parent().parent().addClass('hidden');

                //$('.block_myoverview [data-action="more-courses"]').removeClass('hidden');
                $('.block_myoverview [data-action="view-more"]').trigger('click');

                // Legacy method for blocks toggling.
                /*$("body").on( "click", ".toggleblocks", function() {
                    if($('body').hasClass('blocks-hidden')) {
                        M.util.set_user_preference('blocks-collapsed', 'false');
                    } else {
                        M.util.set_user_preference('blocks-collapsed', 'true');
                    }
                    $('body').toggleClass('blocks-hidden');
                });*/

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

                $('body').on('click', '.hideflashboxteachers', function(event) {
                    event.preventDefault();
                    var target = event.target;
                    $(target).parent().parent().slideUp(250);
                    M.util.set_user_preference('flashbox-teacher-hidden', 'true');
                });

                $('body').on('click', '.hideflashboxstudents', function(event) {
                    event.preventDefault();
                    var target = event.target;
                    $(target).parent().parent().slideUp(250);
                    M.util.set_user_preference('flashbox-student-hidden', 'true');
                });

                var offset = 200;
                var duration = 500;
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

               /* $('div.media').first().on('click', function() {
                    $('html, body').animate({scrollTop: 150}, 50);
                    return false;
                });*/

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
                $('#chooserform #item_lesson').parent().parent().remove();
                $('.block-myoverview').show();
            });
        }
    };
});
/* jshint ignore:end */
