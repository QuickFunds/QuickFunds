 // Function to open the phone number form modal
 function openPhoneNumberForm() {
    var modal = document.getElementById('phoneNumberModal');
    modal.style.display = 'block';
  }
  
  // Function to close the phone number form modal
  function closePhoneNumberForm() {
    var modal = document.getElementById('phoneNumberModal');
    modal.style.display = 'none';
  }
  
 // Function to handle form submission
function submitPhoneNumberForm(event) {
    event.preventDefault(); // Prevent default form submission
    
    // Get the phone number from the form
    var phoneNumber = document.getElementById('phoneNumber').value;
  
    // Validate phone number length
    if (phoneNumber.length >= 10 && phoneNumber.length <= 13) {
      // Send an AJAX request to the PHP script to save the phone number
      var xhr = new XMLHttpRequest();
      xhr.open('POST', '../Phpfiles/add_number.php', true);
      xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
      xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
          var response = JSON.parse(xhr.responseText);
          if (response.status === 'success') {
            // Phone number successfully saved
            alert('Thank you for providing your phone number: ' + phoneNumber);
            closePhoneNumberForm(); // Close the modal
          } else {
            // Error saving phone number
            alert('Error: ' + response.message);
          }
        }
      };
      xhr.send('phoneNumber=' + encodeURIComponent(phoneNumber));
    } else {
      // Phone number length is not valid
      alert('Please enter a phone number between 10 and 13 digits.');
    }
  }
