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
    }
}

const formElement = document.getElementById('login-form');
const loginForm = new Form(formElement, configs);
loginForm.init();

