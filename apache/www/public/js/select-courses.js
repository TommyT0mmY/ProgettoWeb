import Form from '/js/modules/form.js';

const configs = {
    endpoint: '/api/select-courses',
    submitButtonText: 'Setting Courses...',
    validityMessages: {
    },
    responseErrorsMapping: {
        GENERIC_ERROR: {
            message: 'An error occurred. Please try again.'
        }
    }
}

const formElement = document.getElementById('select-courses-form');
const selectCoursesForm = new Form(formElement, configs);
selectCoursesForm.init();

