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
    }
}

const formElement = document.getElementById('adminlogin-form');
const adminloginForm = new Form(formElement, configs);
adminloginForm.init();

