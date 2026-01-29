import Form from '/js/modules/form.js';

const configs = {
    endpoint: '/api/edit-profile',
    submitButtonText: 'Saving...',
    validityMessages: {
        firstname: {
            valueMissing: 'Please enter your first name.'
        },
        lastname: {
            valueMissing: 'Please enter your last name.'
        },
        facultyid: {
            valueMissing: 'Please select your faculty.'
        }
    },
    responseErrorsMapping: {
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
        },
        FACULTY_INVALID: {
            field: 'facultyid',
            message: 'The selected faculty is invalid.'
        },
        GENERIC_ERROR: {
            message: 'An error occurred. Please try again.'
        }
    },
    onSuccess: handleSuccess
};

const formElement = document.getElementById('edit-profile-form');
const editProfileForm = new Form(formElement, configs);
editProfileForm.init();

// Mark inputs with values as having content (for label positioning)
document.querySelectorAll('.field-holder input').forEach(input => {
    if (input.value) {
        input.classList.add('has-value');
    }
});

function handleSuccess() {
    editProfileForm.setStatusMessage('Profile updated successfully!');
}
