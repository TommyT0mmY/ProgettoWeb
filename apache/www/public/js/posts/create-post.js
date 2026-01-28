import Form from '/js/modules/form.js';

const configs = {
    endpoint: '/api/posts/create',
    submitButtonText: 'Creating post...',
    validityMessages: {
        title: {
            valueMissing: 'Please enter a post title.'
        },
        description: {
            valueMissing: 'Please enter post description.'
        }
    },
    responseErrorsMapping: {
        TITLE_REQUIRED: {
            field: 'title',
            message: 'Title is required.'
        },
        DESCRIPTION_REQUIRED: {
            field: 'description',
            message: 'Description is required.'
        },
        COURSE_REQUIRED: {
            field: 'courseId',
            message: 'Course is required.'
        },
        GENERIC_ERROR: {
            message: 'An error occurred. Please try again.'
        }
    }
};

const formElement = document.querySelector('form#create-post-form');
if (formElement) {
    const createPostForm = new Form(formElement, configs);
    createPostForm.init();
}
