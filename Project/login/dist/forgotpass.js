function openForgotPasswordPrompt() {
  var forgotPasswordPopup = document.getElementById('forgotPasswordPopup');
  forgotPasswordPopup.style.display = 'block';
}

function closeForgotPasswordPrompt() {
  var forgotPasswordPopup = document.getElementById('forgotPasswordPopup');
  forgotPasswordPopup.style.display = 'none';
}

function sendPasswordResetEmail() {
  var forgotPasswordEmail = document.getElementById('forgotPasswordEmail').value;

  // Make an AJAX request to the PHP script
  var xhr = new XMLHttpRequest();
  xhr.open('POST', '../../Phpfiles/reset_password.php', true);
  xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function() {
    if (xhr.readyState == 4 && xhr.status == 200) {
      var response = xhr.responseText;
      // Handle the response from the PHP script
      if (response == 'success') {
        // Password reset email sent successfully
        showAlert('Password reset email has been sent. Please check your inbox.', 'success');
      } else if (response == 'Email not found') {
        // Email not found in the database
        showAlert('The provided email address is not registered.', 'error');
      } else {
        // Error sending password reset email
        showAlert('Error sending password reset email. Please try again later.', 'error');
      }
      // Close the forgot password prompt
      closeForgotPasswordPrompt();
    }
  };
  xhr.send('email=' + encodeURIComponent(forgotPasswordEmail));
}

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
  setTimeout(function() {
    alertElement.remove();
  }, 5000); // Remove the alert after 5 seconds (adjust duration as needed)
}
