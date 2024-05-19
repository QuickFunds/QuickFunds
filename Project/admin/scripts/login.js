$(document).ready(function() {
    $('#loginForm').submit(function(e) {
        e.preventDefault(); // Prevent form submission
        
        var username = $('#username').val();
        var password = $('#password').val();

        // Send an AJAX request to the login PHP script
        $.ajax({
            type: 'POST',
            url: '../admin/phpfiles/adminlogin.php',
            data: { username: username, password: password },
            dataType: 'json', // Expect JSON response
            success: function(response) {
                if (response.status === 'success') {
                    if (response.isAdmin) {
                        // Redirect to dashboard only if the user is an admin
                        window.location.href = 'tests.php';
                    } else {
                        // Show message or perform action for non-admin users
                        alert('You do not have permission to access the admin dashboard.');
                    }
                } else {
                    // Show alert for invalid login with specific error message
                    alert(response.message);
                    $('#loginForm')[0].reset();
                }
            },
            error: function() {
                alert('Error occurred while logging in'); // Display error message using alert
                $('#loginForm')[0].reset();
            }
        });
    });
});
