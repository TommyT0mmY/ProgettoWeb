/**
 * Generic handler for admin entity edit/add forms (faculty, course, tag, category)
 */

const form = document.querySelector('form.fullscreen-form');
const endpoint = form.dataset.endpoint;
const mode = form.dataset.mode;
const entityType = form.dataset.entityType;

// Get all input fields that are not hidden, disabled, or CSRF-related
const inputs = Array.from(form.querySelectorAll('input:not([type="hidden"]):not([disabled]):not([name^="csrf"])'));
const submitButton = document.getElementById('submit-btn');

// Helper functions
function showFormError(message) {
    const errorOutput = form.querySelector('.form-error-message');
    if (errorOutput) {
        errorOutput.textContent = message;
    }
}

function showFieldError(input, message) {
    const errorOutput = input.parentElement?.querySelector('.field-error-message');
    if (errorOutput) {
        errorOutput.textContent = message;
        input.setAttribute('aria-invalid', 'true');
    }
}

function clearFieldError(input) {
    const errorOutput = input.parentElement?.querySelector('.field-error-message');
    if (errorOutput) {
        errorOutput.textContent = '';
        input.setAttribute('aria-invalid', 'false');
    }
}

// Add input listeners to clear field errors
inputs.forEach(input => {
    const handleChange = () => {
        clearFieldError(input);
    };
    
    input.addEventListener('input', handleChange);
    input.addEventListener('change', handleChange);
});

// Form submission handler
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Clear previous errors
    const errorOutputs = form.querySelectorAll('.field-error-message, .form-error-message');
    errorOutputs.forEach(output => output.textContent = '');
    
    // Get form data
    const formData = new FormData(form);
    
    // Add CSRF tokens from window (set by admin-layout)
    formData.append('csrf-token', window.csrfToken);
    formData.append('csrf-key', window.csrfKey);
    
    // Determine HTTP method based on mode
    const httpMethod = mode === 'edit' ? 'PUT' : 'POST';
    
    try {
        // Disable button
        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Saving...';
        
        // Convert FormData to JSON for PUT/POST
        const dataObject = {};
        formData.forEach((value, key) => {
            dataObject[key] = value;
        });
        
        const response = await fetch(endpoint, {
            method: httpMethod,
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(dataObject)
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Redirect immediately
            const backButton = form.querySelector('button[type="button"]');
            if (backButton) {
                backButton.click();
            }
        } else {
            // Re-enable button
            submitButton.disabled = false;
            submitButton.textContent = originalText;
            
            // Handle validation errors
            if (data.errors) {
                Object.entries(data.errors).forEach(([field, message]) => {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input && !input.disabled) {
                        showFieldError(input, message);
                    }
                });
            } else if (data.message) {
                showFormError(data.message);
            } else {
                showFormError('An error occurred. Please try again.');
            }
        }
    } catch (error) {
        console.error('Submission error:', error);
        showFormError('Network error. Please check your connection and try again.');
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    }
});
