import Form from '/js/modules/form.js';

// File upload configuration
const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10 MB
const MAX_FILES = 5;
const ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar'];

const configs = {
    endpoint: '/api/posts/create',
    submitButtonText: 'Creating post...',
    useMultipart: true, // Enable multipart/form-data for file uploads
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
        FILE_TOO_LARGE: {
            field: 'files',
            message: 'One or more files exceed the maximum size of 10 MB.'
        },
        FILE_TYPE_NOT_ALLOWED: {
            field: 'files',
            message: 'One or more files have an unsupported format. Allowed: PDF, DOC, DOCX, TXT, JPG, PNG, GIF, ZIP, RAR.'
        },
        FILE_UPLOAD_ERROR: {
            field: 'files',
            message: 'An error occurred while uploading files. Please try again.'
        },
        FILE_MAX_COUNT_EXCEEDED: {
            field: 'files',
            message: `You can upload a maximum of ${MAX_FILES} files per post.`
        },
        FILE_NAME_TOO_LONG: {
            field: 'files',
            message: 'One or more file names are too long. Maximum 255 characters.'
        },
        GENERIC_ERROR: {
            message: 'An error occurred. Please try again.'
        }
    },
    // Validate files before submission
    before: () => {
        const fileInput = document.getElementById('notesFile');
        const errorOutput = document.getElementById('notesFile-error');
        
        if (fileInput && fileInput.files.length > 0) {
            // Clear previous errors
            if (errorOutput) errorOutput.textContent = '';
            
            // Check file count
            if (fileInput.files.length > MAX_FILES) {
                if (errorOutput) {
                    errorOutput.textContent = `You can upload a maximum of ${MAX_FILES} files.`;
                }
                return false;
            }
            
            // Validate each file
            for (const file of fileInput.files) {
                // Check file size
                if (file.size > MAX_FILE_SIZE) {
                    if (errorOutput) {
                        errorOutput.textContent = `File "${file.name}" exceeds the maximum size of 10 MB.`;
                    }
                    return false;
                }
                
                // Check extension
                const extension = file.name.split('.').pop().toLowerCase();
                if (!ALLOWED_EXTENSIONS.includes(extension)) {
                    if (errorOutput) {
                        errorOutput.textContent = `File "${file.name}" has an unsupported format. Allowed: ${ALLOWED_EXTENSIONS.join(', ').toUpperCase()}.`;
                    }
                    return false;
                }
            }
        }
        
        return true;
    }
};

const formElement = document.querySelector('form#create-post-form');
if (formElement) {
    const createPostForm = new Form(formElement, configs);
    createPostForm.init();
    
    // Setup file input preview
    const fileInput = document.getElementById('notesFile');
    const fileListContainer = document.getElementById('file-list');
    
    if (fileInput && fileListContainer) {
        fileInput.addEventListener('change', () => {
            fileListContainer.innerHTML = '';
            
            if (fileInput.files.length > 0) {
                const list = document.createElement('ul');
                list.className = 'selected-files-list';
                
                for (const file of fileInput.files) {
                    const li = document.createElement('li');
                    const sizeFormatted = file.size < 1024 
                        ? `${file.size} B` 
                        : file.size < 1048576 
                            ? `${(file.size / 1024).toFixed(1)} KB`
                            : `${(file.size / 1048576).toFixed(1)} MB`;
                    
                    li.innerHTML = `
                        <span class="file-icon">📎</span>
                        <span class="file-name">${escapeHtml(file.name)}</span>
                        <span class="file-size">(${sizeFormatted})</span>
                    `;
                    list.appendChild(li);
                }
                
                fileListContainer.appendChild(list);
            }
        });
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
