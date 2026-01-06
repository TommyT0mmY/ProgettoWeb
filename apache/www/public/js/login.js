const getErrorElement = (input) => {
    const errorId = `${input.id}-error`;
    const errorElement = document.getElementById(errorId);
    return errorElement;
}

const setAccessibilityError = (input, message) => {
    if (!input || !message) return;
    const errorElement = getErrorElement(input);
    if (!errorElement) return;
    errorElement.textContent = message;
    input.setAttribute('aria-invalid', 'true');
    input.setAttribute('aria-describedby', errorElement.id);
};

const cleanAccessibilityError = (input) => {
    const errorElement = getErrorElement(input);
    if (!errorElement) return;
    errorElement.textContent = '';
    input.removeAttribute('aria-invalid');
    input.removeAttribute('aria-describedby');
}

/**
 * Handles form submission via AJAX.
 * @param {HTMLFormElement} formElement - The form element to handle.
 * @param {Object} validations - An object containing validation rules and messages.
 * @param {string} endpoint - The API endpoint to submit the form data to.
 */
async function handleFormSubmit(formElement, validations, endpoint) {
    const inputs = formElement.querySelectorAll('input, textarea, select');
    const submitButton = formElement.querySelector('button[type="submit"]');
    formElement.addEventListener('submit', async function(event) {
        event.preventDefault();
        // Client-side validation
        let hasError = false;
        inputs.forEach(input => {
            const validation = validations[input.name];
            if (validation && !validation.validate(input.value)) {
                setAccessibilityError(input, validation.message);
                hasError = true;
                return;
            }
            cleanAccessibilityError(input);
        });
        if (hasError) {
            return;
        }
        // Submission
        const originalButtonText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Attempting login...';
        const formData = new FormData(formElement);
        const data = Object.fromEntries(formData.entries()); 
        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });
            if (!response.ok) throw new Error(`Server error: ${response.statusText}`);
            const result = await response.json();
            if (result.success) {
                formElement.reset();
                window.location.href = result.redirect || '/';
                return;
            }
            // Handle field-specific errors
            Object.entries(result.fieldErrors || {}).forEach(([fieldName, errorMessage]) => {
                const input = formElement.querySelector(`[name="${fieldName}"]`);
                setAccessibilityError(input, errorMessage);
            });
            throw new Error(result.generalError || 'An unknown error occurred.');
        } catch (error) {
            Popup.throwError('Login failed. ' + error.message);
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = originalButtonText;
        }
    });
    // Resetting error messages on input
    inputs.forEach(input => {
        input.addEventListener('input', () => {
            cleanAccessibilityError(input);
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    const validations = {
        username: {
            validate: (value) => value.trim() !== '',
            message: 'Username is required.'
        },
        password: {
            validate: (value) => value.length >= 8,
            message: 'Password must be at least 8 characters long.'
        }
    };
    handleFormSubmit(loginForm, validations, '/api/auth/login');
});
