// form_validation.js

document.addEventListener("DOMContentLoaded", function () {

    // Email format validation
    const emailField = document.querySelector('input[type="email"]');
    if (emailField) {
        emailField.addEventListener("blur", function () {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !regex.test(this.value)) {
                alert("Please enter a valid email address.");
                this.value = '';
            }
        });
    }

    // Phone number: 10–15 digits only
    const phoneField = document.querySelector('input[name="phone_number"]');
    if (phoneField) {
        phoneField.addEventListener("blur", function () {
            const digits = /^[0-9]{10,15}$/;
            if (this.value && !digits.test(this.value)) {
                alert("Phone number must be 10–15 digits.");
                this.value = '';
            }
        });
    }

    // Password: Minimum 5 characters
    const passwordField = document.querySelector('input[name="password"]');
    if (passwordField) {
        passwordField.addEventListener("blur", function () {
            if (this.value && this.value.length < 5) {
                alert("Password must be at least 5 characters long.");
                this.value = '';
            }
        });
    }

});
