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
  
    // Update the strength indicator based on the strength value
    var strengthIndicator = document.getElementById('passwordStrength');
    if (password.length >= 8 && strength >= 3) {
      strengthIndicator.innerHTML = 'Strong';
      strengthIndicator.style.color = 'green';
      return true; // Password is strong
    } else if (password.length >= 6 && strength >= 2) {
      strengthIndicator.innerHTML = 'Medium';
      strengthIndicator.style.color = 'orange';
      return true; // Password is medium
    } else {
      strengthIndicator.innerHTML = 'Weak';
      strengthIndicator.style.color = 'red';
      return false; // Password is weak
    }
  }
  
  // Function to check if a username is valid
  function isValidUsername(username) {
    var regex = /^[a-zA-Z0-9]+([._-]?[a-zA-Z0-9]+)*$/; // Alphanumeric characters, dots, underscores, and hyphens
    return regex.test(username);
  }
  
  // Function to validate the sign-up form
  function validateSignupForm() {
    var username = document.getElementById('username').value;
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;
    var isPasswordStrong = updatePasswordStrength(password);
  
    if (!isPasswordStrong || !isValidUsername(username)) {
      handleInvalidInputs(isPasswordStrong, username);
      return false;
    }
  
    // Additional check for password strength
    if (!isPasswordStrong) {
      alert('Password is too weak. Please choose a stronger password.');
      return false;
    }
  
    return true;
  }
  
  // Function to handle invalid inputs
  function handleInvalidInputs(isPasswordStrong, username) {
    if (!isPasswordStrong && isValidUsername(username)) {
     
    } else if (isPasswordStrong && !isValidUsername(username)) {
    
    } else {
     
    }
    resetFormFields();
  }
  
  // Function to reset form fields
  function resetFormFields() {
    document.getElementById('email').value = '';
    document.getElementById('username').value = '';
    document.getElementById('password').value = '';
    document.getElementById('passwordStrength').innerHTML = ''; // Reset password strength indicator
  }
  
  // Event listener for password input to update strength indicator
  document.getElementById('password').addEventListener('input', function() {
    var password = this.value;
    updatePasswordStrength(password);
  });
  
  // Event listener for form submission
  document.getElementById('signupForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission
    validateSignupForm(); // Call the function to validate the sign-up form
  });
  