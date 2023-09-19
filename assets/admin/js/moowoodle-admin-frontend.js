(function($) {
    'use strict';
    $(document).ready(function() {
        $(".mw-pro-popup-overlay").click(function() {
            $(".mw-image-overlay").css({
                "opacity": "1",
                "visibility": "visible"
            })
            console.log('123');
        });
        $('.mw-image-overlay').click(function() {
            $(".mw-image-overlay").css({
                "opacity": "0",
                "visibility": "hidden"
            })
        });
        //multiple chekbox unchake for pro
        function handleCheckboxClick(event) {
            const checkbox = event.target;
            // Toggle the checkbox state
            checkbox.checked = !checkbox.checked;
        }
        const checkboxes = document.querySelectorAll('.mw-pro-popup-overlay input[type="checkbox"]');
        // Add event listeners to checkboxes
        checkboxes.forEach(checkbox => {
            // Uncheck if already checked
            if (checkbox.checked) {
                checkbox.checked = false;
            }
            // Add click event listener
            checkbox.addEventListener('click', handleCheckboxClick);
        });
        //forced checked checkbox
        const forcecheckbox = document.querySelectorAll('.forceCheckCheckbox');
        var warningShown = false;
        // Add a click event listener to the checkbox
        if(forcecheckbox)
        forcecheckbox.forEach((checkbox, index) => {
            checkbox.addEventListener('click', function () {
                // Once checked, disable the checkbox
                if (!checkbox.checked) {
                    if(!warningShown){
                        const warningHTML = '<div class="mw-warning-massage" id="warningMessage-' + index + '" style="position: relative;">' + admin_frontend_args.lang.warning_to_force_checked + '</div>';
                        checkbox.parentElement.insertAdjacentHTML("afterend", warningHTML);
                        warningShown = true;
                    }
                    checkbox.checked = true;
                }
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
        //test connection
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
        //sso generat key
        const inputDiv = document.querySelector(".mw-textbox-input-wraper");
        const ssKeyInput = document.getElementById("moowoodlepro-sso-sskey");
        if (ssKeyInput != null) {
            const generatButton = document.createElement("div");
            generatButton.innerHTML = '<button class="generat-key button-secondary" label="Generot Key" type="button">Generate</button>';
            inputDiv.appendChild(generatButton);
            let warningMessage = null;
            $(".generat-key").on("click", function() {
                const randomKey = generateRandomKey(8);
                ssKeyInput.value = randomKey;
                if (!warningMessage) {
                    warningMessage = document.createElement("div");
                    warningMessage.id = "warningMessage";
                    warningMessage.className = "mw-warning-massage";
                    warningMessage.style.color = "red";
                    warningMessage.innerText = "Remember to save your recent changes to ensure they're preserved.";
                    ssKeyInput.insertAdjacentElement("afterend", warningMessage);
                }
            });
            ssKeyInput.addEventListener("input", function() {
                if (warningMessage) {
                    warningMessage.remove();
                    warningMessage = null;
                }
            });

            function generateRandomKey(length) {
                const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
                let key = "";
                for (let i = 0; i < length; i++) {
                    const randomIndex = Math.floor(Math.random() * characters.length);
                    key += characters.charAt(randomIndex);
                }
                return key;
            }
        }
    });
})(jQuery);