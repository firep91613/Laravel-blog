class ImageChanger {
    #imageFromInput;
    #oldValue;
    #imageElem = null;
    #isCloseButton = false;
    #imageNameField;
    #width;

    constructor(imageFromInput, width) {
        this.#imageFromInput = imageFromInput;
        this.#oldValue = imageFromInput.dataset.old
        this.#imageNameField = imageFromInput.previousElementSibling;
        this.#width = width;
    }

    init() {
        this.#imageFromInput.addEventListener('change', this.#handler.bind(this));
        window.addEventListener('pageshow', this.#oldValueHandler.bind(this));

        if (this.#oldValue) {
            this.#addPreview(this.#oldValue);
            this.#imageNameField.textContent = this.#getOldFileName();
        }
    }

    #handler(e) {
        const target = e.target;

        if (target.files.length) {
            this.#setNewValue(target.files[0]);
        }
    }

    #setNewValue(image) {
        const reader = new FileReader();

        reader.onload = () => {
            if (!this.#imageElem) {
                this.#addPreview(reader.result);
            } else {
                this.#imageElem.src = reader.result;
            }

            this.#imageNameField.textContent = image.name;

            if (!this.#isCloseButton) this.#setCloseButton();
        };

        reader.readAsDataURL(image);
    }

    #oldValueHandler() {
        if (this.#imageFromInput.value) {
            this.#setNewValue(this.#imageFromInput.files[0]);
        }
    }

    #createCloseButton() {
        const spanElem = document.createElement('span');
        spanElem.innerHTML = '&times;';
        spanElem.classList.add('image__delete');

        return spanElem;
    }

    #closeButtonHandler(e) {
        this.#deleteCloseButton(e.target);
        this.#imageElem.parentNode.remove();
        this.#imageElem = null;

        if (this.#imageFromInput.value) {
            this.#imageFromInput.value = null;
        }
    }

    #setCloseButton() {
        const button = this.#createCloseButton();
        button.addEventListener('click', this.#closeButtonHandler.bind(this));
        this.#imageElem.parentNode.append(button);
        this.#isCloseButton = true;
    }

    #deleteCloseButton(button) {
        button.remove();
        button.removeEventListener('click', this.#closeButtonHandler.bind(this));
        this.#isCloseButton = false;
        document.querySelector('.input-file__text').textContent = '';
    }

    #addPreview(image) {
        const pElem = this.#createPreviewWrap();
        const imgElem = this.#createImgElem(image);
        pElem.append(imgElem);
        this.#imageFromInput.closest('.input-file').before(pElem);
        this.#imageElem = imgElem;
        this.#setCloseButton();
    }

    #createPreviewWrap() {
        const pElem = document.createElement('p');
        pElem.classList.add('preview', 'preview-edit');
        pElem.style.width = this.#width + 'px';

        return pElem;
    }

    #createImgElem(image) {
        const imgElem = document.createElement('img');
        imgElem.src = image;
        imgElem.alt = 'Превью';
        imgElem.classList.add('preview__image');

        return imgElem;
    }

    #getOldFileName() {
        return this.#oldValue.match(/(?<=\/)\w+\.\w+$/i)[0];
    }
}
