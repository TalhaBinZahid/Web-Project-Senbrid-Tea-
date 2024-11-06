document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("registrationForm");
    const userId = document.getElementById("userId").value; 
    const username = document.getElementById("username");
    const email = document.getElementById("email");
    const role = document.getElementById("role");
    const password = document.getElementById("password");

    const usernameError = document.getElementById("usernameError");
    const emailError = document.getElementById("emailError");
    const roleError = document.getElementById("roleError");
    const passwordError = document.getElementById("passwordError");

    username.addEventListener("input", validateUsername);
    email.addEventListener("input", validateEmail);
    password.addEventListener("input", validatePassword);
    role.addEventListener("input", validateRole);

    form.addEventListener("submit", (event) => {
        if (!validateUsername() || !validateEmail() || !validatePassword() || !validateRole()) {
            event.preventDefault(); // Stop form submission if any field is invalid
        }
    });

    function validateUsername() {
        if (username.value.trim() === "" || username.value.length < 5) {
            usernameError.textContent = "Username is invalid (at least 5 characters)";
            username.classList.add("invalid");
            return false;
        } else {
            usernameError.textContent = "";
            username.classList.remove("invalid");
            username.classList.add("valid");
            return true;
        }
    }

    function validateEmail() {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email.value.match(emailPattern)) {
            emailError.textContent = "Enter a valid email address";
            email.classList.add("invalid");
            return false;
        } else {
            emailError.textContent = "";
            email.classList.remove("invalid");
            email.classList.add("valid");
            return true;
        }
    }

    function validateRole() {
        if (role.value.trim() === "") {
            roleError.textContent = "Role cannot be empty";
            role.classList.add("invalid");
            return false;
        } else {
            roleError.textContent = "";
            role.classList.remove("invalid");
            role.classList.add("valid");
            return true;
        }
    }

    function validatePassword() {
        if (!userId && (password.value.trim() === "" || password.value.length < 6)){
            passwordError.textContent = "Password must be at least 6 characters long";
            password.classList.add("invalid");
            return false;
        } else {
            passwordError.textContent = "";
            password.classList.remove("invalid");
            password.classList.add("valid");
            return true;
        }
    }
});

function confirmSignOut() {
    // Display confirmation dialog
    var result = confirm("Are you sure you want to sign out?");
    if (result) {
        // If the user clicks "OK", redirect to the logout page
        window.location.href = '../Login/login.php'; // Change this to your logout URL
    }
    // If the user clicks "Cancel", do nothing (the function returns false)
    return false;
}