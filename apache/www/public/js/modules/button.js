/**
 * Button utility class for managing button states and actions
 * Provides consistent handling for loading states, confirmations, and click handlers
 */
class Button {
    /** @type {HTMLButtonElement} */
    #element;
    /** @type {string} */
    #originalText;
    /** @type {string} */
    #originalHTML;
    /** @type {Object} */
    #configs;

    /**
     * @param {HTMLButtonElement} element - The button element to manage
     * @param {Object} configs - Configuration object
     * @param {Function} configs.onClick - Async function to execute on click
     * @param {string} [configs.loadingText] - Text to display while processing
     * @param {string} [configs.confirmMessage] - Confirmation message to show before executing
     * @param {string} [configs.errorMessage] - Generic error message to show on failure
     * @param {boolean} [configs.preventDefault=true] - Whether to prevent default event behavior
     * @param {boolean} [configs.stopPropagation=false] - Whether to stop event propagation
     * @param {Function} [configs.onSuccess] - Callback executed on successful completion
     * @param {Function} [configs.onError] - Callback executed on error
     */
    constructor(element, configs = {}) {
        this.#element = element;
        // Set default configurations
        this.#configs = {
            preventDefault: true,
            stopPropagation: false,
            loadingText: 'Loading...',
            ...configs
        };
        this.#originalText = element.textContent;
        this.#originalHTML = element.innerHTML;
    }

    /**
     * Initialize the button by setting up event listeners
     */
    init() {
        if (!this.#element) {
            console.error('Button element not found');
            return;
        }

        this.#element.addEventListener('click', async (event) => {
            if (this.#configs.preventDefault) {
                event.preventDefault();
            }
            if (this.#configs.stopPropagation) {
                event.stopPropagation();
            }

            // Show confirmation dialog if configured
            if (this.#configs.confirmMessage) {
                if (!confirm(this.#configs.confirmMessage)) {
                    return;
                }
            }

            // Set loading state
            this.setLoading(true);

            try {
                // Execute the onClick handler
                const result = await this.#configs.onClick(event);

                // Execute success callback if provided
                if (this.#configs.onSuccess) {
                    this.#configs.onSuccess(result);
                }
            } catch (error) {
                console.error('Button action error:', error);

                // Execute error callback if provided
                if (this.#configs.onError) {
                    this.#configs.onError(error);
                } else if (this.#configs.errorMessage) {
                    alert(this.#configs.errorMessage);
                }
            } finally {
                // Restore original state
                this.setLoading(false);
            }
        });
    }

    /**
     * Set the loading state of the button
     * @param {boolean} isLoading - Whether the button should be in loading state
     */
    setLoading(isLoading) {
        this.#element.disabled = isLoading;
        
        // If loadingText is empty, preserve the button's HTML content (e.g., images)
        if (this.#configs.loadingText === '') {
            return;
        }
        
        // Otherwise, update text content
        if (isLoading) {
            this.#element.textContent = this.#configs.loadingText;
        } else {
            // Restore original HTML to preserve any child elements (images, icons, etc.)
            this.#element.innerHTML = this.#originalHTML;
        }
    }

    /**
     * Set the text content of the button
     * @param {string} text - New text content
     */
    setText(text) {
        this.#originalText = text;
        this.#element.textContent = text;
    }

    /**
     * Enable or disable the button
     * @param {boolean} disabled - Whether the button should be disabled
     */
    setDisabled(disabled) {
        this.#element.disabled = disabled;
    }

    /**
     * Add a CSS class to the button
     * @param {string} className - Class name to add
     */
    addClass(className) {
        this.#element.classList.add(className);
    }

    /**
     * Remove a CSS class from the button
     * @param {string} className - Class name to remove
     */
    removeClass(className) {
        this.#element.classList.remove(className);
    }

    /**
     * Toggle a CSS class on the button
     * @param {string} className - Class name to toggle
     * @param {boolean} [force] - Force add (true) or remove (false)
     */
    toggleClass(className, force) {
        this.#element.classList.toggle(className, force);
    }

    /**
     * Get the underlying HTML element
     * @returns {HTMLButtonElement}
     */
    getElement() {
        return this.#element;
    }

    /**
     * Destroy the button by removing event listeners and references
     */
    destroy() {
        // Clone and replace to remove all event listeners
        const newElement = this.#element.cloneNode(true);
        this.#element.parentNode?.replaceChild(newElement, this.#element);
        this.#element = null;
    }
}

/**
 * Factory class for creating and managing multiple buttons
 */
class ButtonFactory {
    /** @type {Map<string, Button>} */
    #buttons = new Map();

    /**
     * Create a new button with the given configuration
     * @param {string} id - Unique identifier for the button
     * @param {HTMLButtonElement} element - The button element
     * @param {Object} configs - Button configuration
     * @returns {Button}
     */
    create(id, element, configs) {
        const button = new Button(element, configs);
        button.init();
        this.#buttons.set(id, button);
        return button;
    }

    /**
     * Get a button by its ID
     * @param {string} id - Button identifier
     * @returns {Button|undefined}
     */
    get(id) {
        return this.#buttons.get(id);
    }

    /**
     * Remove and destroy a button
     * @param {string} id - Button identifier
     */
    remove(id) {
        const button = this.#buttons.get(id);
        if (button) {
            button.destroy();
            this.#buttons.delete(id);
        }
    }

    /**
     * Destroy all buttons
     */
    destroyAll() {
        this.#buttons.forEach(button => button.destroy());
        this.#buttons.clear();
    }

    /**
     * Get the number of managed buttons
     * @returns {number}
     */
    get size() {
        return this.#buttons.size;
    }
}

export { Button, ButtonFactory };
export default Button;
