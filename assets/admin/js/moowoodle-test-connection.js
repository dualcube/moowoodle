(function ($) {
    'use strict';

    $(document).ready(function () {
        var course_id ,course_empty,enrolment,user_id;

        $(".test-connection").click(function () {
            // status div clear .test-connection-status
            // posttipe clear .test_connect_posttype
            //posttype titel
            $.ajax({
                method: 'post',
                url:  'admin-ajax.php' ,
                data: {
                    action: 'get_catagory',
                },
                success: function(response){
                    $('.test-connection-status').prepend($('<div>get catagory: ' + response['message'] + ' </div>'));
                }
            });

            $.ajax({
                method: 'post',
                url:  'admin-ajax.php' ,
                data: {
                    action: 'get_course_by_fuild',
                },
                success: function(response){
                    $('.test-connection-status').prepend($('<div>get course by fuild: ' + response['message'] + ' </div>'));
                }
            });

            $.ajax({
                method: 'post',
                url:  'admin-ajax.php' ,
                data: {
                    action: 'get_course',
                },
                success: function(response){
                    $('.test-connection-status').prepend($('<div>get course: ' + response['message'] + ' </div>'));
                    course_id = response['course_id'];
                    course_empty = response['course_empty'];
                    $.ajax({
                        method: 'post',
                        url:  'admin-ajax.php' ,
                        data: {
                            action: 'create_user',
                        },
                        success: function(response){
                            $('.test-connection-status').prepend($('<div>create user: ' + response['message'] + ' </div>'));
                            $.ajax({
                                method: 'post',
                                url:  'admin-ajax.php' ,
                                data: {
                                    action: 'get_user',
                                },
                                success: function(response){
                                    $('.test-connection-status').prepend($('<div>get user: ' + response['message'] + ' </div>'));
                                    user_id = response['user_id'];
                                    if(user_id != null){
                                        $.ajax({
                                            method: 'post',
                                            url:  'admin-ajax.php' ,
                                            data: {
                                                action: 'update_user',
                                                user_id: user_id,
                                            },
                                            success: function(response){
                                               $('.test-connection-status').prepend($('<div>update user: ' + response['message'] + ' </div>'));
                                            }
                                        });
                                        if(course_empty != null){
                                            $.ajax({
                                                method: 'post',
                                                url:  'admin-ajax.php' ,
                                                data: {
                                                    action: 'enrol_users',
                                                    user_id: user_id,
                                                    course_id: course_id
                                                },
                                                success: function(response){
                                                   $('.test-connection-status').prepend($('<div>enrol users: ' + response['message'] + ' </div>'));
                                                   enrolment = response['enrolment'];
                                                }
                                            });
                                            $.ajax({
                                                method: 'post',
                                                url:  'admin-ajax.php' ,
                                                data: {
                                                    action: 'unenrol_users',
                                                    user_id: user_id,
                                                    course_id: course_id
                                                },
                                                success: function(response){
                                                   $('.test-connection-status').prepend($('<div>unenrol users: ' + response['message'] + ' </div>'));
                                                }
                                            });
                                        }else{
                                            $('.test-connection-status').prepend($('<div>No course to enroll </div>'));
                                        }
                                        $.ajax({
                                            method: 'post',
                                            url:  'admin-ajax.php' ,
                                            data: {
                                                action: 'delete_users',
                                                user_id: user_id,
                                            },
                                            success: function(response){
                                               $('.test-connection-status').prepend($('<div>delete users: ' + response['message'] + ' </div>'));
                                            }
                                        });
                                    }       
                                }
                            });
                        }
                    });
                }
            });
        });
    });



})(jQuery);
