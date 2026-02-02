import Form from '/js/modules/form.js';


// ============================================================================
// Form Setup
// ============================================================================
const configs = {
    endpoint: '/api/edit-faculty',
    submitButtonText: 'Saving...',
    validityMessages: {
        facultyname: {
            valueMissing: 'Please enter the faculty name.'
        }
    },
    responseErrorsMapping: {
        FACULTY_REQUIRED: {
            field: 'facultyname',
            message: 'Faculty name is required.'
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
const coursesContainer = document.querySelector('.courses-container');
const facultyIdInput = document.querySelector('input[name="facultyid"][type="hidden"]');
const formElement = document.getElementById('edit-faculty-form');
const editFacultyForm = new Form(formElement, configs);

editFacultyForm.init();

// Mark inputs with values as having content (for label positioning)
document.querySelectorAll('.field-holder input').forEach(input => {
    if (input.value) {
        input.classList.add('has-value');
    }
});

function handleSuccess() {
    editFacultyForm.setStatusMessage('Faculty updated successfully!');
}

// Load courses on page load
if (facultyIdInput) {
    loadCourses(facultyIdInput.value);
}

// ============================================================================
// Core Functions
// ============================================================================

async function loadCourses(facultyId) {
    editFacultyForm.setStatusMessage('Loading courses...');
    
    try {
        const response = await fetch(`/api/faculties/${facultyId}/courses`);
        
        if (!response.ok) {
            throw new Error('Network error');
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error('Server error');
        }
        
        renderCourses(data.courses);
        editFacultyForm.setStatusMessage('');
        
    } catch (error) {
        editFacultyForm.setGeneralError('An error occurred while loading courses.');
    }
}

function renderCourses(courses) {
    if (!courses || courses.length === 0) {
        coursesContainer.innerHTML = '<p>No courses available for this faculty.</p>';
        return;
    }
    
    coursesContainer.innerHTML = '<h4>Associated Courses:</h4>';
    const ul = document.createElement('ul');
    ul.style.listStyle = 'none';
    ul.style.paddingLeft = '1.5rem';
    
    courses.forEach(course => {
        const li = document.createElement('li');
        li.textContent = course.courseName;
        ul.appendChild(li);
    });
    
    coursesContainer.appendChild(ul);
}

