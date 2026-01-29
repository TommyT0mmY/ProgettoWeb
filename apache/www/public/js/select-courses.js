import Form from '/js/modules/form.js';

const configs = {
    endpoint: '/api/select-courses',
    submitButtonText: 'Saving...',
    validityMessages: {
    },
    responseErrorsMapping: {
        GENERIC_ERROR: {
            message: 'An error occurred. Please try again.'
        },
        FACULTY_INVALID: {
            field: 'facultyid',
            message: 'The selected faculty is invalid.'
        }
    },
    getPayload: getPayload
}

let courseCache = {};
let prevFaculty = null;
let payload = {
    "unsubscribeFrom": [],
    "subscribeTo": [],
    "csrf-key": window.csrfKey,
    "csrf-token": window.csrfToken
};

const formElement = document.getElementById('select-courses-form');
const selectCoursesForm = new Form(formElement, configs);
selectCoursesForm.init();

const facultySelector = document.getElementById('facultyid');
facultySelector.addEventListener('change', onFacultyChange);
onFacultyChange();

const coursesContainer = document.querySelectorAll('.courses-container')[0];




/*        return Response::create()->json([
            "success" => true,
            "courses" => $courses,
            "subscribedCourses" => $subscribedCourses
        ]);
*/

async function onFacultyChange() {
    if (prevFaculty !== null) { //store previous checkboxes in courseCache and payload
        storeCurrentSelections();
    }
    const facultyId = facultySelector.value;
    prevFaculty = facultyId;
    selectCoursesForm.setStatusMessage('Loading courses...');
    // Check if already in cache
    if (courseCache[facultyId]) {
        fillContainer(courseCache[facultyId]);
        return;
    }
    const response = await fetch('/api/select-courses/faculty/' + facultyId);
    if (!response.ok) {
        selectCoursesForm.setGeneralError("An error occurred while loading courses.");
        return;
    }
    let responseData = await response.json();
    if (!responseData.success) {
        selectCoursesForm.setGeneralError("An error occurred while loading courses.");
        return;
    }
    let finalData = {"courses": {}};
    const { courses, subscribedCourses } = responseData;
    courses.forEach(course => {
        finalData.courses[course.courseId] = {
            id: course.courseId,
            name: course.courseName,
            subscribed: false
        }
    });
    subscribedCourses.forEach(subsCourse => {
        finalData.courses[subsCourse.courseId].subscribed = true;
    });



    courseCache[facultyId] = finalData;
    fillContainer(finalData);
}

/**
 * Stores the current selections of courses into the payload object
 * by comparing with the cached data.
 */
function storeCurrentSelections() {
    if (!prevFaculty || !courseCache[prevFaculty]) {
        return;
    }
    const cachedData = courseCache[prevFaculty];
    const checkboxes = coursesContainer.querySelectorAll('input[type="checkbox"]');
    
    checkboxes.forEach(checkbox => {
        const courseId = checkbox.value;
        const isCurrentlyChecked = checkbox.checked;
        const wasOriginallySubscribed = cachedData.courses[courseId]?.subscribed ?? false;
        
        // Update the cache with current selection state
        if (cachedData.courses[courseId]) {
            cachedData.courses[courseId].subscribed = isCurrentlyChecked;
        }
        
        // Determine if this is a subscription or unsubscription change
        if (isCurrentlyChecked && !wasOriginallySubscribed) {
            // User wants to subscribe to this course
            if (!payload.subscribeTo.includes(courseId)) {
                payload.subscribeTo.push(courseId);
            }
            // Remove from unsubscribe if it was there
            const unsubIndex = payload.unsubscribeFrom.indexOf(courseId);
            if (unsubIndex > -1) {
                payload.unsubscribeFrom.splice(unsubIndex, 1);
            }
        } else if (!isCurrentlyChecked && wasOriginallySubscribed) {
            // User wants to unsubscribe from this course
            if (!payload.unsubscribeFrom.includes(courseId)) {
                payload.unsubscribeFrom.push(courseId);
            }
            // Remove from subscribe if it was there
            const subIndex = payload.subscribeTo.indexOf(courseId);
            if (subIndex > -1) {
                payload.subscribeTo.splice(subIndex, 1);
            }
        }
    });
}


function fillContainer(data) {
    const courseTemplate =  document.getElementById('course-template');
    coursesContainer.textContent = '';
    for (let courseId in data.courses) {
        const courseElement = courseTemplate.content.cloneNode(true);
        const { name, id, subscribed } = data.courses[courseId];
        const labelElement = courseElement.querySelector('label');
        const checkboxElement = courseElement.querySelector('input[type="checkbox"]');
        labelElement.textContent = name;
        checkboxElement.value = id;
        if (subscribed) {
            checkboxElement.checked = true;
        }
        coursesContainer.appendChild(courseElement);
    }

    selectCoursesForm.setStatusMessage();
}

/**
 * @returns {Object|false|Promise<Object|false>}
 */
async function getPayload() {
    storeCurrentSelections();
    // clone payload and invalidate cache and reset payload 
    let payloadClone = {...payload};
    payload = {
        "unsubscribeFrom": [],
        "subscribeTo": [],
        "csrf-key": window.csrfKey,
        "csrf-token": window.csrfToken
    };
    courseCache = {};
    return payloadClone;
}
