class Form {
    /** @type {HTMLFormElement} */
    #form;
    /** @type {Object} */
    #configs;
    /** @type {HTMLOutputElement} */
    #generalErrorElement;
    /** @type {HTMLOutputElement} */
    #generalStatusMessage;
    /** @type {HTMLButtonElement} */
    #submitButton;
    /** @type {Array.<HTMLInputElement|HTMLTextAreaElement|HTMLSelectElement>} */
    #inputFields;

    /**
     * @param {HTMLFormElement} form
     * @param {Object} configs
     * @param {boolean} configs.useMultipart - If true, sends as multipart/form-data instead of JSON
     */
    constructor(form, configs) {
        this.#form = form;
        this.#configs = configs;
    }

    init() {
        this.#generalErrorElement = this.#form.querySelector('.form-error-message');
        this.#generalStatusMessage = this.#form.querySelector('.form-status-message');
        this.#submitButton = this.#form.querySelector('button[type="submit"]');

        this.#form.addEventListener('submit', async (event) => {
            event.preventDefault();
            this.#inputFields = [...this.#form.querySelectorAll('input, textarea, select')];
            // If configs.before is a function, call it 
            if (typeof this.#configs?.before === 'function') {
                const shouldContinue = await this.#configs.before();
                if (shouldContinue === false) {
                    return;
                }
            }
            this.#submitButton.disabled = true;
            const originalButtonText = this.#submitButton.textContent;
            this.#submitButton.textContent = this.#configs?.submitButtonText || 'Submitting...';
            try {
                // Checking validity of all fields before submission
                const isValid = this.#inputFields.map(this.#validateField.bind(this)).every(Boolean); // Using .map before .every to guarantee that every field is validated
                if (!isValid) {
                    return;
                }
                
                // Determine if we should use multipart/form-data or JSON
                const useMultipart = this.#configs?.useMultipart === true;
                let response;
                
                if (useMultipart) {
                    // Use FormData for multipart/form-data (file uploads)
                    const formData = this.#getFormDataForMultipart();
                    response = await fetch(this.#configs.endpoint, {
                        method: 'POST',
                        body: formData // No Content-Type header needed; browser sets it automatically
                    });
                } else {
                    // JSON submission
                    let formData = this.serializeForm();
                    if (typeof this.#configs?.getPayload === 'function') { // If a custom payload function is provided
                        const customPayload = await this.#configs.getPayload();
                        if (customPayload === false) {
                            return;
                        }
                        formData = customPayload;
                    }
                    response = await fetch(this.#configs.endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });
                }
                
                // Response handling
                const responseData = await response.json();
                if (!responseData.success) { // Displaying errors
                    const errorMapping = this.#configs?.responseErrorsMapping ?? {};
                    responseData?.errors?.forEach(errorCode => {
                        const { field, message = 'Invalid input.' } = errorMapping[errorCode] ?? {};
                        (!field) ? this.setGeneralError(message) : this.#setFieldError(field, message);
                    });
                    return;
                }
                
                // Handle file-specific errors (partial success)
                if (responseData.fileErrors && responseData.fileErrors.length > 0) {
                    const errorMapping = this.#configs?.responseErrorsMapping ?? {};
                    responseData.fileErrors.forEach(errorCode => {
                        const { field, message = 'File upload error.' } = errorMapping[errorCode] ?? {};
                        (!field) ? this.setGeneralError(message) : this.#setFieldError(field, message);
                    });
                }
                
                if (!response.ok) { // HTTP response status codes not in 200-299 range
                    throw new Error("Network or server error, response code: " + response.status);
                }
                if (responseData.redirect) {
                    window.location.href = responseData.redirect;
                    return;
                }
                // Call onSuccess callback if provided
                if (typeof this.#configs?.onSuccess === 'function') {
                    await this.#configs.onSuccess(responseData);
                }
            } catch (error) {
                this.setGeneralError('An error occurred. Please try again.');
            } finally { // Re-enable the submit button
                this.#submitButton.disabled = false;
                this.#submitButton.textContent = originalButtonText;
            }
        });
        // Clear errors and status message on input
        this.#form.addEventListener('input', (e) => {
            this.#clearFieldError(e.target);
            this.#setFieldError('');
            this.setGeneralError('');
            this.setStatusMessage('');
            e.target.classList.toggle('has-value', Boolean(e.target.value));
        });
        // If everything is set up correctly, enable the submit button
        this.#submitButton.disabled = false;
    }

    /**
     * Get FormData object for multipart/form-data submission (file uploads)
     * @returns {FormData}
     */
    #getFormDataForMultipart() {
        const formData = new FormData(this.#form);
        
        // Add CSRF tokens
        if (!formData.has('csrf-key')) {
            formData.append('csrf-key', window.csrfKey);
        }
        if (!formData.has('csrf-token')) {
            formData.append('csrf-token', window.csrfToken);
        }
        
        return formData;
    }

    serializeForm() {
        let formData = {};
        const fd = new FormData(this.#form);

        for (const name of fd.keys()) {
            const values = fd.getAll(name);
            formData[name] = values.length > 1 ? values : values[0];
        }
        for (const key in formData) {
            if (key.endsWith('[]')) {
                formData[key.slice(0, -2)] = Array.isArray(formData[key]) ? formData[key] : [formData[key]];
                delete formData[key];
            }
        }
        // Adding CSRF tokens as separate fields if not already present
        if (!formData['csrf-key']) formData['csrf-key'] = window.csrfKey;
        if (!formData['csrf-token']) formData['csrf-token'] = window.csrfToken;

        return formData;
    }

    /**
     * Check validity of the input field and set a field specific error message if invalid
     *
     * Validation is provided by HTML5 constraint validation API. Custom messages can be set
     * via the configs.validityMessages object.
     * 
     * @param {HTMLInputElement|HTMLTextAreaElement|HTMLSelectElement} inputElement
     * @returns {boolean} true if valid, false if invalid
     */
    #validateField(inputElement) {
        const { validity, name } = inputElement;
        if (validity.valid) {
            return true;
        }
        const errorTypes = [
            "badInput",
            "valueMissing",
            "typeMismatch",
            "patternMismatch",
            "tooShort",
            "tooLong",
            "rangeUnderflow",
            "rangeOverflow",
            "stepMismatch"
        ];
        const activeError = errorTypes.find(type => validity[type]);
        const message = this.#configs?.validityMessages?.[name]?.[activeError] || inputElement.validationMessage;
        this.#setFieldError(inputElement, message);
        return false;
    }

    /**
     * Get the error message element associated with the input field
     *
     * @param {HTMLInputElement|HTMLTextAreaElement|HTMLSelectElement} inputElement
     * @returns {HTMLElement|null}
     */
    #getFieldErrorElement(inputElement) {
        return document.getElementById(`${inputElement.id}-error`);
    }

    #getFieldFromName(fieldName) {
        if (!fieldName) return null;
        return this.#form.querySelector(`[name="${fieldName}"]`);
    }

    /**
     * Set the error message for the input field
     *
     * @param {string|HTMLInputElement|HTMLTextAreaElement|HTMLSelectElement} inputElement
     * @param {string} message
     */
    #setFieldError(inputElement, message) {
        if (typeof inputElement === 'string') {
            inputElement = this.#getFieldFromName(inputElement);
        }
        if (!inputElement || !message) return;
        const errorElement = this.#getFieldErrorElement(inputElement);
        if (!errorElement) return;
        errorElement.textContent = message;
        inputElement.setAttribute('aria-invalid', 'true');
    }

    /**
     * Clear the error message for the input field
     *
     * @param {HTMLInputElement|HTMLTextAreaElement|HTMLSelectElement} inputElement
     */
    #clearFieldError(inputElement) {
        if (!inputElement) return;
        const errorElement = this.#getFieldErrorElement(inputElement);
        if (!errorElement) return;
        errorElement.textContent = '';
        inputElement.setAttribute('aria-invalid', 'false');
    }

    setGeneralError(message) {
        if (!message) {
            message = '';
        }
        this.#generalErrorElement.textContent = message;
    }

    setStatusMessage(message) {
        if (!this.#generalStatusMessage) return;
        if (!message) {
            message = '';
        }
        this.#generalStatusMessage.textContent = message;
    }
}

export default Form;
