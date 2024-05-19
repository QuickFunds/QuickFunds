function toggleProfile() {
    var profileSection = document.querySelector('.profile-section');
    var logoImg = document.getElementById('logoImg');
    var addCardForm = document.getElementById('addCardForm');
    var changePasswordForm = document.getElementById('changePasswordForm');
    var welcomeDiv = $('.welcome-message'); // Select the welcome message div
    var closeProfileTimer; // Variable to store the timer for auto-closing profile

    // Function to close the profile section
    function closeProfile() {
        profileSection.style.left = '-300px';
        addCardForm.style.display = 'none';
        changePasswordForm.style.display = 'none';
    }

    // Function to reset the timer for auto-closing profile
    function resetCloseProfileTimer() {
        clearTimeout(closeProfileTimer);
        closeProfileTimer = setTimeout(closeProfile, 10000); // 5 seconds (7000 milliseconds)
    }

    if (profileSection.style.left === '-300px') {
        profileSection.style.left = '0';
        logoImg.style.display = 'block';
        welcomeDiv.show();
        resetCloseProfileTimer(); // Reset the timer when profile is opened
    } else {
        closeProfile();
    }

    // Event listeners to reset the auto-close timer when interacting with the profile section
    profileSection.addEventListener('mouseenter', resetCloseProfileTimer);
    profileSection.addEventListener('click', resetCloseProfileTimer);
}
$(document).ready(function() {
    $('#settingsOption').click(function() {
        var logoImg = $('#logoImg');
        var addCardForm = $('#addCardForm');
        var changePasswordForm = $('#changePasswordForm');
        var welcomeDiv = $('.welcome-message'); // Select the welcome message div

        // Check if the forms are hidden before animating
        if (addCardForm.is(':hidden') && changePasswordForm.is(':hidden')) {
            // Hide the logo and welcome message with fadeOut effect
            logoImg.fadeOut('fast');
            welcomeDiv.fadeOut('fast', function() {
                // Toggle the visibility of addCardForm and changePasswordForm with fadeIn effect
                addCardForm.fadeIn('slow');
                changePasswordForm.fadeIn('slow');
            });
        } else {
            // Toggle the visibility of addCardForm and changePasswordForm with fadeOut effect
            addCardForm.fadeOut('fast');
            changePasswordForm.fadeOut('fast', function() {
                // Show the logo and welcome message with fadeIn effect after forms are hidden
                logoImg.fadeIn('slow');
                welcomeDiv.fadeIn('slow');
            });
        }
    });
});

$(document).ready(function() {
 // Function to update password strength indicator
 function updatePasswordStrength(password) {
    var strength = 0;
    var regex = [
        /[A-Z]/, // Uppercase letters
        /[a-z]/, // Lowercase letters
        /[0-9]/, // Numbers
        /[^A-Za-z0-9]/ // Special characters
    ];

    for (var i = 0; i < regex.length; i++) {
        if (regex[i].test(password)) {
            strength++;
        }
    }

    var strengthIndicator = $('#passwordStrength');
    if (password.length >= 8 && strength >= 3) {
        strengthIndicator.text('Strong');
        strengthIndicator.css('color', 'green');
    } else if (password.length >= 6 && strength >= 2) {
        strengthIndicator.text('Medium');
        strengthIndicator.css('color', 'orange');
    } else {
        strengthIndicator.text('Weak');
        strengthIndicator.css('color', 'red');
    }

    return (password.length >= 8 && strength >= 3); // Return password strength evaluation
}

// Function to change password
function changePasswordAjax(oldPassword, newPassword) {
    var isStrongPassword = updatePasswordStrength(newPassword); // Check password strength

    if (!isStrongPassword) {
        $('#passwordChangeResult').text("Password too weak. It must contain lower & upper case letters, number, symbol, and be at least 8 digits long.");

        // Hide error message after 3 seconds
        setTimeout(function() {
            $('#passwordChangeResult').text("");
        }, 3000);

        return; // Do not proceed if password is weak
    }

    // Proceed with password change if password is strong
    $.ajax({
        type: 'POST',
        url: '../Phpfiles/change_password.php',
        data: {
            oldPassword: oldPassword,
            newPassword: newPassword
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                $('#passwordChangeResult').text("Password changed successfully!");
                resetAllForms();

            } else {
                $('#passwordChangeResult').text("Failed to change password. " + response.message);
            }

            // Hide error/success message after 3 seconds
            setTimeout(function() {
                $('#passwordChangeResult').text("");
            }, 3000);
        },
        error: function(xhr, status, error) {
            $('#passwordChangeResult').text("Error occurred while processing your request.");
            console.error(error);

            // Hide error message after 3 seconds
            setTimeout(function() {
                $('#passwordChangeResult').text("");
            }, 3000);
        }
    });
}

// Function to handle password input and update strength indicator
$(document).ready(function() {
    $('#newPassword').on('input', function() {
        var newPassword = $(this).val();
        updatePasswordStrength(newPassword);
    });

    // Handle form submission
    $('#changePasswordForm').submit(function(e) {
        e.preventDefault();
        var oldPassword = $('#oldPassword').val();
        var newPassword = $('#newPassword').val();

        changePasswordAjax(oldPassword, newPassword);
    });
  });
});

function resetAllForms() {
    $('form').trigger("reset");
}

function goToSurveyPage() {
    // Redirect to the survey page
    window.location.href = "survey.html";
}

