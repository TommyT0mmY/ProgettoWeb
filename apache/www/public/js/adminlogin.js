import Form from '/js/modules/form.js';

const configs = {
    endpoint: '/api/auth/adminlogin',
    submitButtonText: 'Logging in...',
    validityMessages: {
        username: {
            valueMissing: 'Please enter your username.'
        },
        password: {
            valueMissing: 'Please enter your password.',
            tooShort: 'Password must be at least 6 characters long.'
        }
    },
    responseErrorsMapping: {
        INVALID_CREDENTIALS: {
            message: 'Invalid username or password.'
        },
        GENERIC_ERROR: {
            message: 'An error occurred. Please try again.'
        }
    }
}

const formElement = document.getElementById('adminlogin-form');
const adminloginForm = new Form(formElement, configs);
adminloginForm.init();

