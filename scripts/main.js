"use strict";

// Block of Code dedicated for validating the create account form.
document.querySelectorAll(".form-control").forEach(function(input) {
    input.addEventListener("blur", function(e) {
        let errors = false;
        // Get the current URL
        const currentUrl = window.location.href;

        let small = e.target.nextElementSibling;
        if(small && small.tagName.toLowerCase() === 'small') {
            small.style.display = '';
        }

        // Name Validation
        if(e.target.id === 'name' && e.target.value === '') {
            errors = true;
            addError(e.target, "Name field is required.");
        }

        // Username Validation
        if(e.target.id === 'username') {
            if(e.target.value === '') {
                errors = true;
                addError(e.target, "Username field is required.");
            } else if(!/^[a-zA-Z0-9]+$/.test(e.target.value)) {
                errors = true;
                addError(e.target, "Username can only contain alphanumeric characters and no spaces.");
            } else if(currentUrl.includes('register.php')) {
                checkUsernameAvailability(e.target.value, function (response) {
                    if (response.status === 'taken') {
                      // Username is already taken
                      errors = true;
                      addError(e.target, "Username is taken.");
                    } else if (response.status === 'available') {
                      // Username is available
                      console.log("Username is available.");
                    } else {
                      // Handle any other response or errors here
                      console.error("Error occurred while checking username availability.");
                    }
                });
            }
        }

        // Email Validation
        if(e.target.id === 'email') {
            if(e.target.value === '') {
                errors = true;
                addError(e.target, "Email field is required.");
            } else if(!/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/.test(e.target.value)) {
                errors = true;
                addError(e.target, "Invalid email format.");
            }
        }

        // Password Validation
        if(e.target.id === 'password') {
            if(e.target.value === '') {
                errors = true;
                addError(e.target, "Password field is required.");
            } else if(!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(e.target.value)) {
                errors = true;
                addError(e.target, "Invalid password.");
            }
        }

        // Add an event listener for real-time password validation
        if (input.id === "password") {
            input.addEventListener("input", function(e) {
                validatePassword(e.target.value);
                validatePasswordStrength(e.target.value);
            });
        }

        // Confirm Password Validation
        if(e.target.id === 'confirm-password') {
            let password = document.getElementById("password");
            if(e.target.value !== password.value) {
                errors = true;
                addError(e.target, "Password confirmation does not match.");
            }
        }

        // Terms Validation
        if(e.target.id === 'terms' && !e.target.checked) {
            errors = true;
            addError(e.target, "You must agree to the terms of service and privacy policy.");
        }

        // Event Title Validation
        if(e.target.id === 'title' && e.target.value === '') {
            errors = true;
            addError(e.target, "Event title field is required.");
        }

        // Event Description Validation
        if(e.target.id === 'description' && e.target.value === '') {
            errors = true;
            addError(e.target, "Event description field is required.");
        }

        // Location Validation
        if(e.target.id === 'location' && e.target.value === '') {
            errors = true;
            addError(e.target, "Location field is required.");
        }

        // Start event time Validation
        if(e.target.id === 'start-event-time') {
            if(e.target.value === '') {
                errors = true;
                addError(e.target, "Start event time field is required.");
            } else if(new Date(e.target.value) < new Date()) {
                errors = true;
                addError(e.target, "Start event time cannot be in the past.");
            }
        }

        // End event time Validation
        if(e.target.id === 'end-event-time') {
            let startTime = document.getElementById("start-event-time");
            if(e.target.value !== '' && new Date(e.target.value) < new Date(startTime.value)) {
                errors = true;
                addError(e.target, "End event time cannot be before start event time.");
            }
        }

        // Meeting start time Validation
        if(e.target.id === 'meeting-time-start') {
            if(e.target.value === '') {
                errors = true;
                addError(e.target, "Meeting start time field is required.");
            } else if(new Date(e.target.value) < new Date()) {
                errors = true;
                addError(e.target, "Meeting start time cannot be in the past.");
            }
        }

        // Meeting end time Validation
        if(e.target.id === 'meeting-time-end') {
            let meetingStartTime = document.getElementById("meeting-time-start");
            if(e.target.value !== '' && new Date(e.target.value) < new Date(meetingStartTime.value)) {
                errors = true;
                addError(e.target, "Meeting end time cannot be before meeting start time.");
            }
        }

        // Signups Validation
        if(e.target.id === 'signups' && e.target.value === '') {
            errors = true;
            addError(e.target, "Signups field is required.");
        }

        // Prevents form inputting
        if(errors) {
            e.preventDefault();
        }
    });

    input.addEventListener("input", function() {
        input.classList.remove("input-error");
        if(input.nextElementSibling && input.nextElementSibling.classList.contains("error-text")) {
            input.nextElementSibling.remove();
        }
        // If there's a following <small> element, show it when the input is updated
        let small = input.nextElementSibling;
        if(small && small.tagName.toLowerCase() === 'small') {
            small.style.display = '';
        }
    });
});

// Display the selected file name for photo upload
let photoInput = document.getElementById('photo');
let photoLabel = document.querySelector('label[for="photo"]');
if (photoInput && photoLabel) {
    photoInput.addEventListener('change', function(e) {
        let fileName = e.target.files[0]?.name;
        if (fileName) {
            photoLabel.textContent = fileName;
        }
    });
}

// Function to dynamically add errors
function addError(inputElement, errorMessage) {
    let targetElement = inputElement;
    if(inputElement.type === 'checkbox') {
        targetElement = inputElement.parentElement;
    }

    // If there's a following <small> element, hide it
    let small = targetElement.nextElementSibling;
    if(small && small.tagName.toLowerCase() === 'small') {
        small.style.display = 'none';
    }

    if(targetElement.nextElementSibling && targetElement.nextElementSibling.classList.contains("error-text")) {
        targetElement.nextElementSibling.textContent = errorMessage;
    } else {
        let errorText = document.createElement("span");
        errorText.textContent = errorMessage;
        errorText.classList.add("error-text");
        targetElement.parentNode.insertBefore(errorText, targetElement.nextSibling);
    }
    inputElement.classList.add("input-error");
}

// Function to dynamically update the password in real time.
function validatePassword(password) {
    const passwordLength = document.getElementById('password-length');
    const passwordLetter = document.getElementById('password-letter');
    const passwordNumber = document.getElementById('password-number');
    const passwordSpecial = document.getElementById('password-special');

    // Check password length
    if (password.length >= 8) {
        passwordLength.querySelector('span').innerHTML = '<i class="fa-solid fa-check" style="color: #00e62e;"></i>';
    } else {
        passwordLength.querySelector('span').innerHTML = '<i class="fa-solid fa-x" style="color: #ff0000;"></i>'; 
    }

    // Check if password contains a letter
    if (/[a-zA-Z]/.test(password)) {
        passwordLetter.querySelector('span').innerHTML = '<i class="fa-solid fa-check" style="color: #00e62e;"></i>'; 
    } else {
        passwordLetter.querySelector('span').innerHTML = '<i class="fa-solid fa-x" style="color: #ff0000;"></i>'; 
    }

    // Check if password contains a number
    if (/\d/.test(password)) {
        passwordNumber.querySelector('span').innerHTML = '<i class="fa-solid fa-check" style="color: #00e62e;"></i>'; 
    } else {
        passwordNumber.querySelector('span').innerHTML = '<i class="fa-solid fa-x" style="color: #ff0000;"></i>'; 
    }

    // Check if password contains a special character
    if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/.test(password)) {
        passwordSpecial.querySelector('span').innerHTML = '<i class="fa-solid fa-check" style="color: #00e62e;"></i>'; 
    } else {
        passwordSpecial.querySelector('span').innerHTML = '<i class="fa-solid fa-x" style="color: #ff0000;"></i>';
    }
}

// Function to validate password strength
function validatePasswordStrength(password) {
    let strengthIndicator = document.getElementById('password-strength');

    // Remove all classes
    strengthIndicator.classList.remove('red', 'yellow', 'green');

    if (password.length >= 12 && /[a-z]/.test(password) && /[A-Z]/.test(password) && /\d/.test(password) && /[@$!%*?&]/.test(password)) {
        strengthIndicator.classList.add('green');
        strengthIndicator.textContent = 'Your password is strong.';
    } else if (password.length >= 8) {
        strengthIndicator.classList.add('yellow');
        strengthIndicator.textContent = 'Your password is okay.';
    } else {
        strengthIndicator.classList.add('red');
        strengthIndicator.textContent = 'Your password is weak.';
    }
}

// Functions for handling the modal window and viewing of details.
function openModal(id) {
    var modal = document.getElementById("modal");
    var span = document.getElementsByClassName("close")[0];
    
    // Change iframe src to slotdetails.php with the correct id
    document.getElementById('modalIframe').src = 'details.php?id=' + id;
    
    // Display the modal
    modal.style.display = "block";
    
    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
      modal.style.display = "none";
    }
    
    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
}

// Confirmation Dialog
function showConfirmation(action, form, callback) {
    if (window.confirm(`Are you sure you want to ${action}?`)) {
      if (callback && typeof callback === 'function') {
        callback(form);
      } else {
        form.submit();
      }
    }
  }

// Find the "Delete Account" button element
const deleteAccountBtn = document.getElementById('del-btn');

// Add a click event listener to the "Delete Account" button
if (deleteAccountBtn) {
deleteAccountBtn.addEventListener('click', handleDeleteAccountForm);
}

// Function to handle the form submission for the "Delete Account" button
function handleDeleteAccountForm(event) {
    event.preventDefault();
  
    // Display the confirmation dialog using the showConfirmation function
    if (showConfirmation("Delete your account?", event.target)) {
      // If the user confirms, submit the form
      event.target.submit();
    }
}
  
// Function to toggle password visibility
function togglePasswordVisibility() {
const passwordInput = document.getElementById('password');
const passwordToggle = document.getElementById('passwordToggle');

// Toggle the type attribute of the password input
if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    passwordToggle.classList.remove('fa-eye');
    passwordToggle.classList.add('fa-eye-slash');
} else {
    passwordInput.type = 'password';
    passwordToggle.classList.remove('fa-eye-slash');
    passwordToggle.classList.add('fa-eye');
}
}

// Find the eye icon element and add a click event listener to it
const passwordToggle = document.getElementById('passwordToggle');
if (passwordToggle) {
passwordToggle.addEventListener('click', togglePasswordVisibility);
}

// Function to check if a username is available
function checkUsernameAvailability(username, callback) {
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);
                callback(response);
            } else {
                // Error occurred during the request
                console.error("Error occurred while checking username availability.");
            }
        }
    };

    // Send the AJAX request
    xhr.open("GET", "check_username.php?username=" + encodeURIComponent(username));
    xhr.send();
}