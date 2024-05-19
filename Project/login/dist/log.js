// Function to handle AJAX login form submission
function handleLoginSubmission(e) {
  e.preventDefault(); // Prevent form submission
  var username = $('#loginUsername').val();
  var password = $('#loginPassword').val();
  loginUser(username, password);
}

// Function to handle user login
function loginUser(username, password) {
  $.ajax({
    type: 'POST',
    url: '../../Phpfiles/login.php',
    data: { username: username, password: password },
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success') {
        window.location.href = '../../user/all.html';
      } else {
        showAlert(response.message, 'error'); // Display styled alert for invalid login
        $('#loginForm')[0].reset();
      }
    },
    error: function() {
      showAlert('Error occurred while logging in', 'error'); // Display styled alert for login error
      $('#loginForm')[0].reset();
    }
  });
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

// AJAX for login form submission
$(document).ready(function() {
  $('#loginForm').submit(handleLoginSubmission);
});
