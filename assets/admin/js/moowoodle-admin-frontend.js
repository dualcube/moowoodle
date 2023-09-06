(function($) {
    'use strict';
    $(document).ready(function() {
        $(".mw-image-overlay, .mw-pro-popup-overlay").hover(function() {
            $(".mw-image-overlay").css({
                "opacity": "1",
                "visibility": "visible"
            })
        }, function() {
            $(".mw-image-overlay").css({
                "opacity": "0",
                "visibility": "hidden"
            })
        });
        $(document).click(function() {
            $(".mw-image-overlay").css({
                "opacity": "0",
                "visibility": "hidden"
            })
        });
        //copy text-input value to clipboard 
        $('.mw-copytoclip').on("click", function() {
            var $button = $(this);
            var $inputField = $button.siblings('.mw-setting-form-input');
            var inputValue = $inputField.val();
            copyToClipboard(inputValue);
            $button.text(admin_frontend_args.lang.Copied).prop('disabled', true);
            $('.mw-copytoclip').not($button).prop('disabled', false).text(admin_frontend_args.lang.Copy);
        });
        $('.mw-setting-form-input').on("input", function() {
            var $inputField = $(this);
            var $button = $inputField.siblings('.mw-copytoclip');
            $button.prop('disabled', false).text(admin_frontend_args.lang.Copy);
        });
        function copyToClipboard(text) {
            var tempInput = document.createElement("textarea");
            tempInput.style.position = 'absolute';
            tempInput.style.left = '-1000px';
            tempInput.value = text;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);
        }
        //warning to save changes
        const textInputs = document.querySelectorAll(".mw-setting-form-input");
        const warningMessages = [];
        if (textInputs != null) {
            textInputs.forEach((textInput, index) => {
                const originalValue = textInput.value;
                let warningShown = false;
                textInput.addEventListener("input", function() {
                    if (!warningShown && textInput.value !== originalValue) {
                        const warningHTML = '<div class="mw-warning-massage" id="warningMessage-' + index + '" style="color: red; display: block;">' + admin_frontend_args.lang.warning_to_save + '</div>';
                        textInput.insertAdjacentHTML("afterend", warningHTML);
                        warningShown = true;
                        warningMessages[index] = warningHTML;
                    } else if (warningShown && textInput.value === originalValue) {
                        const warningMessage = document.getElementById("warningMessage-" + index);
                        if (warningMessage) {
                            warningMessage.remove();
                            warningShown = false;
                            warningMessages[index] = null;
                        }
                    }
                });
            });
        }
        //multiple-checkboxs select/Deselect all
        const button = document.getElementById("selectDeselectButton");
        if (button != null) {
            button.addEventListener("click", function() {
                const checkedEnabledCheckboxes = document.querySelectorAll(".mw-toggle-checkbox:checked:enabled");
                const uncheckedCheckboxes = document.querySelectorAll(".mw-toggle-checkbox:not(:checked):enabled");
                if (checkedEnabledCheckboxes.length >= uncheckedCheckboxes.length) {
                    checkedEnabledCheckboxes.forEach(function(checkbox) {
                        if (!checkbox.disabled) checkbox.checked = false;
                    });
                } else {
                    uncheckedCheckboxes.forEach(function(checkbox) {
                        if (!checkbox.disabled) checkbox.checked = true;
                    });
                }
            });
        }
        var course_id = '';
        var course_empty = '';
        var user_id = '';
        $(".test-connection").click(function() {
            // status div clear 
            $(".test-connection-contains").html("");
            // posttipe clear .test_connect_posttype
            //posttype titel
            var actions_desc = admin_frontend_args.testconnection_actions;
            var actions = Object.keys(actions_desc);
            callajax(actions, actions_desc);
        });
        function callajax(actions, actions_desc) {
            // display the number
            // decrease the number value
            const action = actions.shift();
            $.ajax({
                method: 'post',
                url: 'admin-ajax.php',
                data: {
                    action: action,
                    user_id: user_id,
                    course_id: course_id,
                },
                success: function(response) {
                    if (response['message']) {
                        if (response['message'] == 'success') {
                            var massage = '<span class="test-connection-status-icon"><i class="mw-success-icon dashicons dashicons-yes-alt"></i></span>';
                        } else {
                            var massage = '<span class="test-connection-status-icon"><i class="mw-error-icon dashicons dashicons-dismiss"></i>' + response['message'] + '</span>';
                        }
                        $('.test-connection-contains').append($('<div class="test-connection-status"><span class="test-connection-status-content">' + actions_desc[action] + ' :</span>  ' + massage + ' </div>'));
                        course_empty = response['course_empty'];
                        course_id = response['course_id'];
                        user_id = response['user_id'];
                        // base case
                        if (actions.length !== 0) {
                            callajax(actions, actions_desc);
                        }
                    }
                }
            });
        }
    });
})(jQuery);