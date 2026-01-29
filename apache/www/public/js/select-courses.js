import Form from '/js/modules/form.js';

/**
 * Course Selection Manager
 * 
 * Tracks user's course subscription changes across multiple faculties
 * and submits only the differences (subscribe/unsubscribe) to the server.
 */

// ============================================================================
// State
// ============================================================================

/** 
 * Original subscriptions from server, keyed by facultyId
 * @type {Object<string, Set<string>>} 
 */
const serverState = {};

/** 
 * Current user selections (may differ from server), keyed by facultyId  
 * @type {Object<string, Object<string, {id: string, name: string, selected: boolean}>>}
 */
const currentState = {};

/** Currently displayed faculty */
let activeFacultyId = null;

// ============================================================================
// DOM Elements
// ============================================================================

const formElement = document.getElementById('select-courses-form');
const facultySelector = document.getElementById('facultyid');
const coursesContainer = document.querySelector('.courses-container');
const courseTemplate = document.getElementById('course-template');

// ============================================================================
// Form Setup
// ============================================================================

const form = new Form(formElement, {
    endpoint: '/api/select-courses',
    submitButtonText: 'Saving...',
    responseErrorsMapping: {
        GENERIC_ERROR: { message: 'An error occurred. Please try again.' },
        FACULTY_INVALID: { field: 'facultyid', message: 'The selected faculty is invalid.' }
    },
    getPayload: buildPayload,
    onSuccess: handleSubmitSuccess
});

form.init();

// ============================================================================
// Event Handlers
// ============================================================================

facultySelector.addEventListener('change', handleFacultyChange);

// Initial load
handleFacultyChange();

// ============================================================================
// Core Functions
// ============================================================================

/**
 * Handles faculty selection change.
 * Saves current selections before switching, then loads new faculty's courses.
 */
async function handleFacultyChange() {
    // Save current checkbox states before switching
    saveCurrentSelections();
    
    const facultyId = facultySelector.value;
    activeFacultyId = facultyId;
    
    // If we already have data for this faculty, just render it
    if (currentState[facultyId]) {
        renderCourses(facultyId);
        return;
    }
    
    // Fetch from server
    await fetchCourses(facultyId);
}

/**
 * Fetches courses for a faculty from the server.
 */
async function fetchCourses(facultyId) {
    form.setStatusMessage('Loading courses...');
    
    try {
        const response = await fetch(`/api/select-courses/faculty/${facultyId}`);
        
        if (!response.ok) {
            throw new Error('Network error');
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error('Server error');
        }
        
        // Initialize state for this faculty
        initializeFacultyState(facultyId, data.courses, data.subscribedCourses);
        renderCourses(facultyId);
        
    } catch (error) {
        form.setGeneralError('An error occurred while loading courses.');
    }
}

/**
 * Initializes state for a faculty based on server response.
 */
function initializeFacultyState(facultyId, courses, subscribedCourses) {
    // Store original server state (which courses are subscribed)
    const subscribedIds = new Set(subscribedCourses.map(c => String(c.courseId)));
    serverState[facultyId] = subscribedIds;
    
    // Initialize current state with server values
    currentState[facultyId] = {};
    
    courses.forEach(course => {
        const courseId = String(course.courseId);
        currentState[facultyId][courseId] = {
            id: courseId,
            name: course.courseName,
            selected: subscribedIds.has(courseId)
        };
    });
}

/**
 * Saves the current checkbox selections to state.
 * Called before switching faculty or submitting.
 */
function saveCurrentSelections() {
    if (!activeFacultyId || !currentState[activeFacultyId]) {
        return;
    }
    
    const checkboxes = coursesContainer.querySelectorAll('input[type="checkbox"]');
    
    checkboxes.forEach(checkbox => {
        const courseId = checkbox.value;
        if (currentState[activeFacultyId][courseId]) {
            currentState[activeFacultyId][courseId].selected = checkbox.checked;
        }
    });
}

/**
 * Renders the courses for a faculty.
 */
function renderCourses(facultyId) {
    coursesContainer.innerHTML = '';
    
    const courses = currentState[facultyId];
    if (!courses) return;
    
    Object.values(courses).forEach(course => {
        const fragment = courseTemplate.content.cloneNode(true);
        const checkbox = fragment.querySelector('input[type="checkbox"]');
        const label = fragment.querySelector('label');
        
        checkbox.value = course.id;
        checkbox.id = `course-${course.id}`;
        checkbox.checked = course.selected;
        
        label.textContent = course.name;
        label.setAttribute('for', `course-${course.id}`);
        
        coursesContainer.appendChild(fragment);
    });
    
    form.setStatusMessage('');
}

/**
 * Builds the payload for form submission.
 * Compares current selections with original server state to determine changes.
 */
function buildPayload() {
    // Save any pending selections first
    saveCurrentSelections();
    
    const subscribeTo = [];
    const unsubscribeFrom = [];
    
    // Compare current state with server state for all faculties
    for (const facultyId in currentState) {
        const original = serverState[facultyId] || new Set();
        const courses = currentState[facultyId];
        
        for (const courseId in courses) {
            const isSelected = courses[courseId].selected;
            const wasSubscribed = original.has(courseId);
            
            if (isSelected && !wasSubscribed) {
                subscribeTo.push(courseId);
            } else if (!isSelected && wasSubscribed) {
                unsubscribeFrom.push(courseId);
            }
        }
    }
    
    // Build payload
    const payload = {
        subscribeTo,
        unsubscribeFrom,
        'csrf-key': window.csrfKey,
        'csrf-token': window.csrfToken
    };
    
    // Don't reset here - wait for server confirmation via onSuccess callback
    return payload;
}

/**
 * Handles successful form submission.
 * Resets state and reloads fresh data from server.
 */
async function handleSubmitSuccess() {
    resetState();
    await handleFacultyChange(); // Reload with updated server data
    form.setStatusMessage('Preferences saved successfully!');
}

/**
 * Resets all state after successful submission.
 */
function resetState() {
    // Clear all cached state
    for (const key in serverState) delete serverState[key];
    for (const key in currentState) delete currentState[key];
    activeFacultyId = null;
}
