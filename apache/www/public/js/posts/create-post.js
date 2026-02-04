import Form from '/js/modules/form.js';

// File upload configuration
const MAX_FILE_SIZE = 1e7; // 10MB
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
    before: beforeSubmit
};

const createPostForm = new Form(document.querySelector('form#create-post-form'), configs);
createPostForm.init();

const attachmentsInput = document.getElementById('attachments');
attachmentsInput.addEventListener('change', onAttachmentsInputChange);

function onAttachmentsInputChange() {
    const fileList = document.getElementById('file-list');
    fileList.innerHTML = '';
    const files = this.files;
    if (files.length <= 0) return;
    for (const file of files) {
        const item = document.getElementById('file-item-template').content.cloneNode(true);
        item.querySelector('.file-name').textContent = escapeHtml(file.name);
        item.querySelector('.file-size').textContent = formatSize(file.size);
        fileList.appendChild(item);
    }
}

function formatSize(size) {
    const units = ["B", "KB", "MB"];
    let i = 0;
    for (;i < units.length-1 && size >= 1000; i++) {
        size /= 1000;
    }
    return `${size.toFixed(1)} ${units[i]}`;
}

function beforeSubmit() {
    const attachmentsInput = document.getElementById('attachments');
    const attachmentsErrorOutput = document.getElementById('attachments-error');
    const files = attachmentsInput.files;
    if (!attachmentsInput) {
        return true;
    }
    if (files.length <= 0) {
        return true;
    }
    attachmentsErrorOutput.textContent = '';
    if (files.length > MAX_FILES) {
        attachmentsErrorOutput.textContent = `You can upload a maximum of ${MAX_FILES} files.`;
        return false;
    }
    for (const file of files) {
        if (file.size > MAX_FILE_SIZE) {
            attachmentsErrorOutput.textContent = `File "${file.name}" exceeds the maximum size of 10 MB.`;
            return false;
        }
        const extension = file.name.split('.').pop().toLowerCase();
        if (!ALLOWED_EXTENSIONS.includes(extension)) {
            attachmentsErrorOutput.textContent = `File "${file.name}" has an unsupported format. Allowed: ${ALLOWED_EXTENSIONS.join(', ').toUpperCase()}.`;
            return false;
        }
        const type = file.type;
        if (type && !type.startsWith('image/') && !type.startsWith('application/')) {
            attachmentsErrorOutput.textContent = `File "${file.name}" has an unsupported MIME type.`;
            return false;
        }
    }
    return true;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

