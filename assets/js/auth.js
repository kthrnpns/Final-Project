document.addEventListener('DOMContentLoaded', function() {
    // Login form validation
    const loginForm = document.querySelector('.login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            let isValid = true;
            
            // Clear previous errors
            document.querySelectorAll('.error-text').forEach(el => el.remove());
            
            // Email validation
            if (!email.value.trim()) {
                showError(email, 'Email is required');
                isValid = false;
            } else if (!isValidEmail(email.value.trim())) {
                showError(email, 'Please enter a valid email');
                isValid = false;
            }
            
            // Password validation
            if (!password.value.trim()) {
                showError(password, 'Password is required');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Registration form validation
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            let isValid = true;
            
            // Clear previous errors
            document.querySelectorAll('.error-text').forEach(el => el.remove());
            
            // Name validation
            if (!name.value.trim()) {
                showError(name, 'Full name is required');
                isValid = false;
            }
            
            // Email validation
            if (!email.value.trim()) {
                showError(email, 'Email is required');
                isValid = false;
            } else if (!isValidEmail(email.value.trim())) {
                showError(email, 'Please enter a valid email');
                isValid = false;
            }
            
            // Password validation
            if (!password.value.trim()) {
                showError(password, 'Password is required');
                isValid = false;
            } else if (password.value.length < 8) {
                showError(password, 'Password must be at least 8 characters');
                isValid = false;
            }
            
            // Confirm password validation
            if (!confirmPassword.value.trim()) {
                showError(confirmPassword, 'Please confirm your password');
                isValid = false;
            } else if (password.value !== confirmPassword.value) {
                showError(confirmPassword, 'Passwords do not match');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Helper function to show error messages
    function showError(input, message) {
        const error = document.createElement('div');
        error.className = 'error-text';
        error.style.color = '#e74c3c';
        error.style.fontSize = '12px';
        error.style.marginTop = '5px';
        error.textContent = message;
        
        input.parentNode.appendChild(error);
        input.style.borderColor = '#e74c3c';
    }
    
    // Email validation helper
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
});