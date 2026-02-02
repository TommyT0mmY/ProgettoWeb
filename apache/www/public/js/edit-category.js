import Form from '/js/modules/form.js';


// ============================================================================
// Form Setup
// ============================================================================
const configs = {
    endpoint: '/api/edit-category',
    submitButtonText: 'Saving...',
    validityMessages: {
        categoryname: {
            valueMissing: 'Please enter the category name.'
        }
    },
    responseErrorsMapping: {
        CATEGORY_REQUIRED: {
            field: 'categoryname',
            message: 'Category name is required.'
        },
        GENERIC_ERROR: {
            message: 'An error occurred. Please try again.'
        }
    },
    onSuccess: handleSuccess
};

const formElement = document.getElementById('edit-category-form');
const editCategoryForm = new Form(formElement, configs);

editCategoryForm.init();

// Mark inputs with values as having content (for label positioning)
document.querySelectorAll('.field-holder input').forEach(input => {
    if (input.value) {
        input.classList.add('has-value');
    }
});

function handleSuccess() {
    editCategoryForm.setStatusMessage('Category updated successfully!');
}

