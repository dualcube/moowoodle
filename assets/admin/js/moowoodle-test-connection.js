(function ($) {
    'use strict';

    $(document).ready(function () {
        var course_id = '';
        var course_empty = '';
        var user_id = '';

        $(".test-connection").click(function () {
            // status div clear 
            $(".test-connection-contains").html("");
            // posttipe clear .test_connect_posttype
            //posttype titel
            var actions_desc = {
                'get_catagory' : 'Course Category Sync',
                'get_course_by_fuild': 'Course Data Sync',
                'get_course': 'Course Sync',
                'create_user': 'User Creation',
                'get_user': 'User Data Sync',
                'update_user': 'User Data Update',
                'enrol_users': 'User Enrolment',
                'unenrol_users': 'User Unenrolment',
                'delete_users': 'All Test'
                };
            var actions = Object.keys(actions_desc);
            callajax(actions,actions_desc);
        });
        function callajax(actions,actions_desc) {

            // display the number

            // decrease the number value
            const action = actions.shift();
            $.ajax({
                method: 'post',
                url:  'admin-ajax.php' ,
                data: {
                    action: action,
                    user_id: user_id,
                    course_id: course_id,

                },
                success: function(response){
                    if(response['message']){
                        if(response['message']== 'success'){
                            var massage = '<span class="test-connection-status-icon"><i class="mw-success-icon dashicons dashicons-yes-alt"></i></span>';
                        }
                        else{
                            var massage =  '<span class="test-connection-status-icon"><i class="mw-error-icon dashicons dashicons-dismiss"></i>' + response['message'] + '</span>';
                        }
                        $('.test-connection-contains').append($('<div class="test-connection-status"><span class="test-connection-status-content">' + actions_desc[action] + ' :</span>  ' + massage + ' </div>'));
                        course_empty = response['course_empty'];
                        course_id = response['course_id'];
                        user_id = response['user_id'];
                        // base case
                        if (actions.length !== 0 ) {
                            callajax(actions,actions_desc);
                        }
                    }

                }
            });
            
        }
    });
})(jQuery);
