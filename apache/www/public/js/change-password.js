import Form from '/js/modules/form.js';

const configs = {
    endpoint: '/api/change-password',
    submitButtonText: 'Changing...',
    validityMessages: {
        currentpassword: {
            valueMissing: 'Please enter your current password.'
        },
        newpassword: {
            valueMissing: 'Please enter a new password.',
            tooShort: 'Password must be at least 6 characters long.'
        },
        confirmpassword: {
            valueMissing: 'Please confirm your new password.',
            tooShort: 'Password must be at least 6 characters long.'
        }
    },
    responseErrorsMapping: {
        PASSWORD_REQUIRED: {
            field: 'newpassword',
            message: 'New password is required.'
        },
        PASSWORD_INVALID: {
            field: 'currentpassword',
            message: 'Current password is incorrect.'
        },
        GENERIC_ERROR: {
            message: 'An error occurred. Please try again.'
        }
    },
    before: validatePasswords,
    onSuccess: handleSuccess
};

const formElement = document.getElementById('change-password-form');
const changePasswordForm = new Form(formElement, configs);
changePasswordForm.init();

const newPasswordInput = document.getElementById('newpassword');
const confirmPasswordInput = document.getElementById('confirmpassword');
const confirmPasswordError = document.getElementById('confirmpassword-error');

/**
 * Validates that the new password and confirmation match.
 * @returns {boolean} true if valid, false to cancel submission
 */
function validatePasswords() {
    if (newPasswordInput.value !== confirmPasswordInput.value) {
        confirmPasswordError.textContent = 'Passwords do not match.';
        confirmPasswordInput.setAttribute('aria-invalid', 'true');
        return false;
    }
    return true;
}

function handleSuccess() {
    // Clear form fields
    formElement.reset();
    changePasswordForm.setStatusMessage('Password changed successfully!');
}
