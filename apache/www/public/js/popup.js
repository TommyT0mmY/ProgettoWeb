class Popup {
    static POPUP_TIMEOUT = 2000;
    static POPUP_ANIMATION_TIME = 550;

    static #popup = null;
    static #popupText = null;
    static #popupClose = null;
    static #closeTimeout = null;
    static #isOpened = false;

    static init() {
        let template = document.querySelector('#popup-template');
        document.body.appendChild(template.content.cloneNode(true));
        this.#popup = document.querySelector('.popup');
        this.#popupText = this.#popup.querySelector('.popup__text');
        this.#popupClose = this.#popup.querySelector('.popup__close');
        this.#popupClose.addEventListener('click', () => this.#close());
        this.#popup.addEventListener('mouseenter', () => clearTimeout(this.#closeTimeout));
        this.#popup.addEventListener('mouseleave', () => this.#closeTimeout = setTimeout(() => this.#close(), this.POPUP_TIMEOUT));
    }

    static throwError(errorMessage) {
        if (this.#closeTimeout) {
            clearTimeout(this.#closeTimeout);
            this.#closeTimeout = null;
        }
        if (this.#isOpened && this.#popupText.textContent != errorMessage) {
            this.#close();
            setTimeout(() => this.#open(errorMessage), this.POPUP_ANIMATION_TIME);
        }
        else {
            this.#open(errorMessage);
        }
        this.#closeTimeout = setTimeout(() => this.#close(), this.POPUP_TIMEOUT);
    }

    static #open(message) {
        this.#popupText.textContent = message;
        this.#popup.classList.add('open');
        this.#isOpened = true;
    }

    static #close() {
        this.#popup.classList.remove('open');
        this.#isOpened = false;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    Popup.init();
});
