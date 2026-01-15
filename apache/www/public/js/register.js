import Form from '/js/modules/form.js';

const configs = {
    endpoint: '/api/auth/register',
    submitButtonText: 'Registering...',
    validityMessages: {
        username: {
            valueMissing: 'Please enter your username.'
        },
        password: {
            valueMissing: 'Please enter your password.',
            tooShort: 'Password must be at least 6 characters long.'
        },
        firstname: {
            valueMissing: 'Please enter your first name.'
        },
        lastname: {
            valueMissing: 'Please enter your last name.'
        },
        facultyid: {
            valueMissing: 'Please enter your faculty.'
        },
    },
    responseErrorsMapping: {
        USERNAME_ALREADY_EXISTS: {
            field: 'username',
            message: 'This username is already taken. Please choose another one.'
        },
        FACULTY_INVALID: {
            field: 'facultyid',
            message: 'The selected faculty is invalid. Please choose a valid faculty.'
        },
        USERNAME_REQUIRED: {
            field: 'username',
            message: 'Username is required.'
        },
        PASSWORD_REQUIRED: {
            field: 'password',
            message: 'Password is required.'
        },
        FIRSTNAME_REQUIRED: {
            field: 'firstname',
            message: 'First name is required.'
        },
        LASTNAME_REQUIRED: {
            field: 'lastname',
            message: 'Last name is required.'
        },
        FACULTY_REQUIRED: {
            field: 'facultyid',
            message: 'Faculty is required.'
        }
    }
}

const formElement = document.getElementById('register-form');
const loginForm = new Form(formElement, configs);
loginForm.init();

