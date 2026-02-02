import Form from '/js/modules/form.js';


// ============================================================================
// Form Setup
// ============================================================================
const configs = {
    endpoint: '/api/edit-course',
    submitButtonText: 'Saving...',
    validityMessages: {
        coursename: {
            valueMissing: 'Please enter the course name.'
        }
    },
    responseErrorsMapping: {
        COURSE_REQUIRED: {
            field: 'coursename',
            message: 'Course name is required.'
        },
        FACULTY_REQUIRED: {
            message: 'Faculty is required.'
        },
        GENERIC_ERROR: {
            message: 'An error occurred. Please try again.'
        }
    },
    onSuccess: handleSuccess
};
// ============================================================================
// DOM Elements
// ============================================================================
const courseIdInput = document.querySelector('input[name="courseid"][type="hidden"]');
const facultyIdInput = document.querySelector('input[name="facultyid"][type="hidden"]');
const formElement = document.getElementById('edit-course-form');
const editCourseForm = new Form(formElement, configs);

editCourseForm.init();

// Mark inputs with values as having content (for label positioning)
document.querySelectorAll('.field-holder input').forEach(input => {
    if (input.value) {
        input.classList.add('has-value');
    }
});

function handleSuccess() {
    editCourseForm.setStatusMessage('Course updated successfully!');
}
