$(document).ready(function() {
    // Function to load transaction history
    function loadTransactionHistory() {
        $.ajax({
            type: 'GET',
            url: '../Phpfiles/get_transactions.php',
            success: function(response) {
                $('#transactionHistory').html(response);
            },
            error: function() {
                $('#transactionHistory').html('Error: Unable to fetch transaction history.');
            }
        });
    }

    // Function to load user details
    function loadUserDetails() {
        $.ajax({
            type: 'GET',
            url: '../Phpfiles/get_user_details.php',
            success: function(response) {
                var userDetails = JSON.parse(response);
                if (userDetails.hasOwnProperty('error')) {
                    console.log('Error:', userDetails.error);
                    // Display an error message or handle the error as needed
                } else {
                    $('#userIdPlaceholder').text('User ID: ' + userDetails.UserID);
                    $('#usernamePlaceholder').text('Username: ' + userDetails.UserName);
                    $('#userBalance').text('Balance: $' + userDetails.Balance);

                    var username = userDetails.UserName; // Assuming the user's name is available

                    // Call the function to display the welcome message
                    displayWelcomeMessage(username);

                }
            },
            error: function(xhr, status, error) {
                console.log('Error:', error);
                $('#userDetails').html('Error: Unable to fetch user details.');
            }
        });
    }

    // Load transaction history and user details on page load
    loadTransactionHistory();
    loadUserDetails();

// Function to add credit card
$('#addCardForm').submit(function(e) {
    e.preventDefault();
    var ccNumber = $('#ccNumber').val();
    var ccv = $('#ccv').val();
    var expiryDate = $('#expiryDate').val();

    // Validation for CCV (should allow only 3 digits)
    if (!/^\d{3}$/.test(ccv)) {
        $('#ccvError').text('Please enter a valid 3-digit CCV');
        // Clear the error message after 3 seconds
        setTimeout(function() {
            $('#ccvError').text('');
        }, 3000);
        return; // Prevent form submission if validation fails
    }

    // AJAX request to add credit card
    $.ajax({
        type: 'POST',
        url: '../Phpfiles/add_Card.php',
        data: {
            ccNumber: ccNumber,
            ccv: ccv,
            expiryDate: expiryDate
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                $('#result').text("Credit card added successfully!");
                resetAllForms();
            } else {
                $('#result').text("Oops! Failed to add the credit card. Please try again later.");
            }
        },
        error: function(xhr, status, error) {
            $('#result').text("An error occurred while processing your request. Please try again later.");
        }
    });
});


    // Function to check credit card
    $('#checkCardBtn').click(function() {
        $.ajax({
            type: 'POST',
            url: '../Phpfiles/check_card.php',
            dataType: 'json',
            success: function(response) {
                $('#result').text(JSON.stringify(response));
                resetAllForms();
            },
            error: function(xhr, status, error) {
                var errorMessage = "Error checking credit card. ";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage += xhr.responseJSON.message;
                } else {
                    errorMessage += "Please try again later.";
                }
                $('#result').text(errorMessage);
            }
        });
    });

    $('#checkUserID').click(function() {
        var username = $('#usernameInput').val();

        // Make an AJAX request to your PHP script
        $.ajax({
            type: 'POST',
            url: '../Phpfiles/check_userID.php',
            data: { username: username },
            dataType: 'json',
            success: function(response) {
                $('#result').text(response);
                resetAllForms();
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    });

    // Functions for withdrawal from card to user and vice versa
    $('#userToCardForm').submit(function(e) {
        e.preventDefault();
        var amount = $('#withdrawalAmount').val();

        if (isNaN(amount) || amount <= 0) {
            $('#withdrawalResult').html("Please enter a valid withdrawal amount for User to Card.");
            return;
        }

        $.ajax({
            type: 'POST',
            url: '../Phpfiles/user_to_card.php',
            data: { withdrawalAmount: amount },
            dataType: 'text',
            success: function(response) {
                alert(response);
                $('#withdrawalResult').html(response);
                resetAllForms();
            },
            error: function(xhr, status, error) {
                $('#withdrawalResult').html("An error occurred. Please try again for User to Card.");
                console.error(xhr.responseText);
            }
        });
    });

    $('#cardToUserForm').submit(function(e) {
        e.preventDefault();
        var amount = $('#withdrawalAmount2').val();

        if (isNaN(amount) || amount <= 0) {
            $('#withdrawalResult').html("Please enter a valid withdrawal amount for Card to User.");
            return;
        }

        $.ajax({
            type: 'POST',
            url: '../Phpfiles/card_to_user.php',
            data: { withdrawalAmount: amount },
            dataType: 'text',
            success: function(response) {
                alert(response); // Display the response in an alert
                $('#withdrawalResult').html(response); 
                resetAllForms();
            },
            error: function(xhr, status, error) {
                $('#withdrawalResult').html("An error occurred. Please try again for Card to User.");
                console.error(xhr.responseText);
            }
        });
    });


    // Function to refresh transaction history and user details
    function refreshData() {
        loadTransactionHistory();
        loadUserDetails();
    }

    // Auto-refresh transaction history and user details every 1 second
    setInterval(refreshData, 1000);

    // Automatic logout after 5 minutes of inactivity
    let logoutTimer;

    // Function to start the logout timer
    function startLogoutTimer() {
        logoutTimer = setTimeout(() => {
            $.ajax({
                type: 'POST',
                url: '../Phpfiles/logout.php',
                success: function(response) {
                    window.location.href = '../index.html';
                },
                error: function() {
                    // Handle error, if any
                }
            });
        }, 5 * 60 * 1000); // 5 minutes (adjust as needed)
    }

    // Function to reset the logout timer on user activity
    function resetLogoutTimer() {
        clearTimeout(logoutTimer);
        startLogoutTimer();
    }

    // Event listeners for user activity (click or keypress)
    document.addEventListener('click', resetLogoutTimer);
    document.addEventListener('keypress', resetLogoutTimer);

    // Start the logout timer initially
    startLogoutTimer();
    
    function resetAllForms() {
        $('form').trigger("reset");
    }

    function displayWelcomeMessage(username) {
        var timestamp = new Date().toLocaleString();
        var welcomeMessage = '<div class="welcome-message">' +
            '<p>Hello <span class="username">' + username + '</span>, welcome to QuickFunds! ' +
            'We\'re thrilled to have you here. Are you ready to start managing your transactions? ' +
            '<span class="timestamp">' + timestamp + '</span></p>' +
            '</div>';
    
        $('#welcomeuser').html(welcomeMessage);
    }
});
