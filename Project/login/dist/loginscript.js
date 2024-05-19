// Function to update password strength indicator
function updatePasswordStrength(password) {
  var strength = 0;
  // Regular expressions to check for different character types
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

  // Update the strength indicator based on the strength value
  var strengthIndicator = document.getElementById('passwordStrength');
  if (password.length >= 8 && strength >= 3) {
    strengthIndicator.innerHTML = 'Strong';
    strengthIndicator.style.color = 'green';
    return true;
  } else if (password.length >= 6 && strength >= 2) {
    strengthIndicator.innerHTML = 'Medium';
    strengthIndicator.style.color = 'orange';
    return true;
  } else {
    strengthIndicator.innerHTML = 'Weak';
    strengthIndicator.style.color = 'red';
    return false;
  }
}

// Function to handle password input and update strength indicator
document.getElementById('password').addEventListener('input', function() {
  var password = this.value;
  updatePasswordStrength(password);
});

// Function to check if username or email exists and validate password strength
function checkUserExists() {
  var username = document.getElementById('username').value;
  var email = document.getElementById('email').value;
  var password = document.getElementById('password').value;

  var isPasswordStrong = updatePasswordStrength(password);

  function isValidUsername(username) {
    var regex = /^[a-zA-Z0-9]+([._-]?[a-zA-Z0-9]+)*$/; // Alphanumeric characters, dots, underscores, and hyphens
    return regex.test(username);
  }

  if (isPasswordStrong && isValidUsername(username)) {
    // Ajax call remains unchanged
    $.ajax({
      type: 'POST',
      url: '../../Phpfiles/check_user.php',
      data: { username: username, email: email },
      success: function(response) {
        if (response.trim() === 'exists') {
          alert('Username or Email already exists');
          document.getElementById('email').value = '';
          document.getElementById('username').value = '';
          document.getElementById('password').value = '';
          document.getElementById('passwordStrength').value = '';
        } else {
          showPrivacyPolicyPopup();
        }
      },
      error: function() {
        alert('Error checking user existence');
      }
    });
  } else {
    if (!isPasswordStrong && isValidUsername(username)) {
      alert('Password is too weak. Please choose a stronger password.');
      document.getElementById('password').value = '';
      document.getElementById('passwordStrength').value = '';
    } else if (isPasswordStrong && !isValidUsername(username)) {
      alert('Invalid username format! You can only use alphanumericals, numbers, and . _ -');
      document.getElementById('username').value = '';
      document.getElementById('passwordStrength').value = '';
    } else {
      alert('Invalid username and password. Please choose a stronger password and a suitable Username.');
      document.getElementById('username').value = '';
      document.getElementById('password').value = '';
    }
  }
}

  // Form submission handling for sign-up form
  document.getElementById('signupForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission
    checkUserExists(); // Call the function to check user existence and validate password
  });

  // AJAX for login form submission
  $(document).ready(function() {
    $('#loginForm').submit(function(e) {
      e.preventDefault(); // Prevent form submission
      
      var username = $('#loginUsername').val();
      var password = $('#loginPassword').val();

      // Send an AJAX request to the login PHP script
      $.ajax({
          type: 'POST',
          url: '../../Phpfiles/login.php',
          data: { username: username, password: password },
          dataType: 'json', // Expect JSON response
          success: function(response) {
              if (response.status === 'success') {
                  window.location.href = '../../user/all.html'; // Redirect on successful login
              } else {
                  showAlert(response.message, 'error'); // Show alert for invalid login with specific error message
                  $('#loginForm')[0].reset();
              }
          },
          error: function() {
              showAlert('Error occurred while logging in', 'error'); // Display error message using alert
              $('#loginForm')[0].reset();
          }
      });
    });
  });

  // Function to show the Privacy Policy pop-up
  function showPrivacyPolicyPopup() {
    const popupContainer = document.getElementById('popupContainer');
    popupContainer.style.display = 'block';

    // Close the pop-up and submit the form
    const agreeButton = document.getElementById('agreeButton');
    agreeButton.addEventListener('click', function() {
      popupContainer.style.display = 'none';
      // Add form submission logic here
      submitForm();
    });
  }

  // Function to simulate form submission (Replace this with your actual form submission logic)
  function submitForm() {
    // Simulated form submission
    console.log('Form submitted successfully.');
    document.getElementById('signupForm').submit(); // Submit the form after agreement
  }

  // Trigger the showPrivacyPolicyPopup function after successful registration
  const registrationSuccessButton = document.getElementById('registrationSuccessButton');
  registrationSuccessButton.addEventListener('click', showPrivacyPolicyPopup);

  $.ajax({
    type: 'POST',
    url: '../../Phpfiles/register.php',
    data: { username: username, email: email, password: password },
    success: function(response) {
      if (response.trim() === 'exists') {
        alert('Username or Email already exists');
        $('#email').val('');
        $('#username').val('');
        $('#password').val('');
        $('#passwordStrength').val('');
      } else if (response.trim() === 'success') {
        showAlert('Registration successful! Please check your email to verify your account.', 'success');
        // Additional logic to handle success, e.g., show a message to the user
      } else {
        alert('Error occurred while registering'); // Display error message using alert
        // Additional error handling logic if needed
      }
    },
    error: function() {
      alert('Error occurred while registering'); // Display error message using alert
      // Additional error handling logic if needed
    }
  });

  function showAlert(message, type) {
    var alertElement = document.createElement('div');
    alertElement.textContent = message;
    alertElement.style.padding = '10px';
    alertElement.style.borderRadius = '5px';
    alertElement.style.marginBottom = '10px';
    alertElement.style.fontSize = '14px';
    alertElement.style.fontWeight = 'bold';

    if (type === 'success') {
        alertElement.style.backgroundColor = '#d4edda';
        alertElement.style.borderColor = '#c3e6cb';
        alertElement.style.color = '#155724';
    } else if (type === 'error') {
        alertElement.style.backgroundColor = '#f8d7da';
        alertElement.style.borderColor = '#f5c6cb';
        alertElement.style.color = '#721c24';
    }

    document.body.appendChild(alertElement);
}