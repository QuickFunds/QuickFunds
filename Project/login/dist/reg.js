// Function to check if a user exists and validate password strength
function checkUserExists() {
  var username = document.getElementById('username').value;
  var email = document.getElementById('email').value;
  var password = document.getElementById('password').value;
  var isPasswordStrong = updatePasswordStrength(password);

  if (!isPasswordStrong || !isValidUsername(username)) {
      showAlert('Please provide a strong password and a valid username.', 'error');
      return;
  }

  // AJAX call to check user existence
  $.ajax({
      type: 'POST',
      url: '../../Phpfiles/check_user.php',
      data: { username: username, email: email },
      success: function (response) {
          if (response.trim() === 'exists') {
              showAlert('Username or Email already exists', 'error');
              resetFormFields();
          } else {
              showPrivacyPolicyPopup();
          }
      },
      error: function () {
          showAlert('Error checking user existence', 'error');
      }
  });
}

// Function to show the Privacy Policy pop-up
function showPrivacyPolicyPopup() {
  const popupContainer = document.getElementById('popupContainer');
  popupContainer.style.display = 'block';

  // Close the pop-up and submit the form
  const agreeButton = document.getElementById('agreeButton');
  agreeButton.addEventListener('click', function () {
      popupContainer.style.display = 'none';
      submitForm();
  });
}

// Function to simulate form submission and insert data into the database
function submitForm() {
  // Get the form data
  var username = document.getElementById('username').value;
  var email = document.getElementById('email').value;
  var password = document.getElementById('password').value;

  // AJAX call to insert data into the database
  $.ajax({
      type: 'POST',
      url: '../../Phpfiles/register.php',
      data: { username: username, email: email, password: password },
      success: function (response) {
          if (response.trim() === 'exists') {
              showAlert('Username or Email already exists', 'error');
              resetFormFields();
          } else if (response.trim() === 'success') {
              showAlert('Your account has been created successfully. Please check your email for a verification link to login.', 'success');
              resetFormFields();
          } else {
              showAlert('Error occurred while registering', 'error');
              // Additional error handling logic if needed
          }
      },
      error: function () {
          showAlert('Error occurred while registering', 'error');
          // Additional error handling logic if needed
      }
  });
}

// Function to display alerts
// Function to display styled alerts
function showAlert(message, type) {
  var alertElement = document.createElement('div');
  alertElement.textContent = message;
  alertElement.style.padding = '10px';
  alertElement.style.borderRadius = '5px';
  alertElement.style.marginBottom = '10px';
  alertElement.style.fontSize = '14px';
  alertElement.style.fontWeight = 'bold';
  alertElement.style.position = 'fixed'; // Set position to fixed
  alertElement.style.top = '12%'; // Center vertically
  alertElement.style.left = '50.5%'; // Center horizontally
  alertElement.style.transform = 'translate(-50%, -50%)'; // Center horizontally and vertically

  if (type === 'success') {
      alertElement.style.backgroundColor = '#d4edda';
      alertElement.style.borderColor = '#c3e6cb';
      alertElement.style.color = '#155724';
  } else if (type === 'error') {
      alertElement.style.backgroundColor = '#f8d7da';
      alertElement.style.borderColor = '#f5c6cb';
      alertElement.style.color = '#721c24';
  }

  // Set z-index to a high value to ensure it's on top of other elements
  alertElement.style.zIndex = '9999';

  // Append the alert element to the body
  document.body.appendChild(alertElement);

  // Set a timeout to remove the alert after a certain duration
  setTimeout(function () {
      alertElement.remove();
  }, 5000); // Remove the alert after 5 seconds (adjust duration as needed)
}


// Form submission handling for sign-up form
document.getElementById('signupForm').addEventListener('submit', function (event) {
  event.preventDefault(); // Prevent default form submission
  checkUserExists(); // Call the function to check user existence and validate password
});

// Trigger the showPrivacyPolicyPopup function after successful registration
const registrationSuccessButton = document.getElementById('registrationSuccessButton');
registrationSuccessButton.addEventListener('click', showPrivacyPolicyPopup);
