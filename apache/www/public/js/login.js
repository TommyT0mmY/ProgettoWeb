import Form from '/js/modules/form.js';

const configs = {
    endpoint: '/api/auth/login',
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

const formElement = document.getElementById('login-form');
const loginForm = new Form(formElement, configs);
loginForm.init();

