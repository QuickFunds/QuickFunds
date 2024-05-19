const themes = [
    {
        background: "#1A1A2E",
        color: "#FFFFFF",
        primaryColor: "#0F3460"
    },
    {
        background: "#461220",
        color: "#FFFFFF",
        primaryColor: "#E94560"
    },
    {
        background: "#192A51",
        color: "#FFFFFF",
        primaryColor: "#967AA1"
    },
    {
        background: "#F7B267",
        color: "#000000",
        primaryColor: "#F4845F"
    },
    {
        background: "#F25F5C",
        color: "#000000",
        primaryColor: "#642B36"
    },
    {
        background: "#231F20",
        color: "#FFF",
        primaryColor: "#BB4430"
    }
];

const setTheme = (theme) => {
    const root = document.querySelector(":root");
    root.style.setProperty("--background", theme.background);
    root.style.setProperty("--color", theme.color);
    root.style.setProperty("--primary-color", theme.primaryColor);
    root.style.setProperty("--glass-color", theme.glassColor);
};

const displayThemeButtons = () => {
    const btnContainer = document.querySelector(".theme-btn-container");
    themes.forEach((theme) => {
        const div = document.createElement("div");
        div.className = "theme-btn";
        div.style.cssText = `background: ${theme.background}; width: 25px; height: 25px`;
        btnContainer.appendChild(div);
        div.addEventListener("click", () => setTheme(theme));
    });
};

displayThemeButtons();

$(document).ready(function() {
    $('#loginForm').submit(function(e) {
        e.preventDefault(); // Prevent form submission
        
        var username = $('#username').val();
        var password = $('#password').val();

        // Send an AJAX request to the login PHP script
        $.ajax({
            type: 'POST',
            url: '../phpfiles/adminlogin.php',
            data: { username: username, password: password },
            dataType: 'json', // Expect JSON response
            success: function(response) {
                if (response.status === 'success') {
                    if (response.isAdmin) {
                        // Redirect to dashboard only if the user is an admin
                        window.location.href = '../adminpanel.php';
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
