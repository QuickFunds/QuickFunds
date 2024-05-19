 
 $(document).ready(function() {
    // Toggle transaction history visibility
    $('#showHistoryBtn').on('click', function() {
        $('#transactionHistory').slideToggle();
    });
});

document.getElementById('addCardForm').addEventListener('submit', function(event) {
    var ccNumberInput = document.getElementById('ccNumber');
    var ccNumber = ccNumberInput.value.replace(/\s/g, ''); // Remove spaces if entered

    if (!/^\d{16}$/.test(ccNumber)) {
        document.getElementById('ccNumberError').innerText = 'Please enter a 16-digit number.';

        // Set timeout to clear the error message after 3 seconds
        setTimeout(function() {
            document.getElementById('ccNumberError').innerText = '';
        }, 3000);

        event.preventDefault(); // Prevent form submission if validation fails
    } else {
        document.getElementById('ccNumberError').innerText = '';
        // Validation passed, proceed with form submission
    }
});

// Allow only numeric input
document.getElementById('ccNumber').addEventListener('input', function(event) {
    var inputValue = event.data || String.fromCharCode(event.which); // For modern and legacy browsers
    if (!/^\d*$/.test(inputValue)) {
        event.preventDefault();
    }
});
document.addEventListener('DOMContentLoaded', function() {
    const transferForm = document.getElementById('transferForm');
    const amountInput = document.getElementById('amount');

    // Function to validate the input as the user types
    function validateInput() {
        const amountValue = parseFloat(amountInput.value);

        if (amountValue <= 1 || !transferForm.checkValidity()) {
            amountInput.classList.add('is-invalid');
        } else {
            amountInput.classList.remove('is-invalid');
        }
    }

    // Event listener for input change to perform validation
    amountInput.addEventListener('input', function() {
        validateInput();
    });

    // Event listener for form submission
    transferForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        validateInput(); // Validate input on submission

        const amountValue = parseFloat(amountInput.value);

        if (amountValue <= 1 || !transferForm.checkValidity()) {
            alert('Please enter a valid amount greater than 1 for the transfer.');
        } else {
            // Perform AJAX submission here if the form is valid
            var formData = $(this).serialize();
            
            $.ajax({
                type: 'POST',
                url: '../Phpfiles/transfer.php',
                data: formData,
                success: function(response) {
                    // Check if the response contains "Money transfer successful"
                    if (response.includes("Money transfer successful")) {
                        // Check if the response contains a WhatsApp link
                        if (response.includes("wa.me")) {
                            // Extract the WhatsApp link from the response
                            var linkStart = response.indexOf("https://wa.me");
                            var linkEnd = response.indexOf("'", linkStart);
                            var whatsappLink = response.substring(linkStart, linkEnd);
                            // Open the WhatsApp link in a new tab
                            window.open(whatsappLink, '_blank');
                        } else {
                            // If no WhatsApp link, simply alert that the transaction was successful
                            alert("Transaction successful!");
                        }
                    } else {
                        // If not successful, display the response in the appropriate element
                        $('#transactionHistory').html(response);
                    }
            
                    // Load transaction history and user details
                    loadTransactionHistory();
                    loadUserDetails();
            
                    // Reset form validation state and remove 'is-invalid' class
                    transferForm.classList.remove('was-validated');
                    amountInput.classList.remove('is-invalid');
                    amountInput.value = ''; // Clear the input field
                },
                error: function() {
                    // Display an error message if the AJAX call fails
                    $('#transactionHistory').html('Error: Unable to process request.');
                }
            });
            
            
            
            
        }

        return false; // Prevent default form submission
    });
});