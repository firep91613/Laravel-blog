class CustomSelect {
    #originalSelect
    #customSelect;
    #customSelectList;
    #liElemForInput;
    #customSelectInput;
    constructor(originalSelect) {
        this.#originalSelect = originalSelect;
        this.#customSelect = this.#initCustomSelect(originalSelect);
        this.#customSelectList = this.#customSelect.querySelector('.custom-select__list');
        this.#liElemForInput = this.#customSelect.querySelector('.custom-select__choice_input');
        this.#customSelectInput = this.#customSelect.querySelector('.custom-select__input');
    }

    init() {
        this.#hideOriginalSelect();
        this.#addCustomSelectList();

        window.addEventListener('pageshow', this.#pageShowHandler.bind(this));

        this.#customSelectInput.addEventListener('focus', () => {
            this.#customSelect.style.borderColor = '#007bff';
        });

        this.#customSelectInput.addEventListener('blur', () => {
            this.#customSelect.style.borderColor = '';
        });

        this.#customSelectInput.addEventListener('click', () => {
            this.#customSelect.classList.toggle('custom-select_open');
        });

        document.addEventListener('click', this.#closeCustomSelectListHandler.bind(this));
        document.addEventListener('click', this.#customSelectChoiceHandler.bind(this));
        document.addEventListener('click', this.#deleteSelectedItem.bind(this));
    }

    #deleteSelectedItem(e) {
        const target = e.target;

        if (!target.classList.contains('delete__item'))
            return;

        const parent = target.parentNode;
        const parentIndex = +parent.dataset.id;
        const item = parent.parentNode;

        item.remove();
        this.#originalSelect.options[parentIndex].selected = false;
        this.#liElemForInput.style.width = this.#calculateCustomInputWidth() + 'px';
    }

    #customSelectChoiceHandler(e) {
        const target = e.target;

        if (!target.classList.contains('custom-select__item'))
            return;

        const index = +target.dataset.id;
        const content = target.innerText;
        const isSelected = this.#hasItem(index);

        if (isSelected) {
            isSelected.parentNode.remove();
            this.#originalSelect.options[index].selected = false;
        } else {
            this.#liElemForInput.before(this.#createLiElem(index, content));
            this.#originalSelect.options[index].selected = true;
        }

        this.#customSelect.classList.remove('custom-select_open');
        this.#liElemForInput.style.width = this.#calculateCustomInputWidth() + 'px';
    }

    #closeCustomSelectListHandler(e) {
        const target = e.target;

        if (!target.closest('.custom-select__list') && target !== this.#customSelectInput) {
            this.#customSelect.classList.remove('custom-select_open');
        }
    }

    #pageShowHandler() {
        const originalSelectLength = this.#originalSelect.options.length;

        for (let i = 0; i < originalSelectLength; i++) {
            if (this.#originalSelect.options[i].selected) {
                this.#liElemForInput.before(this.#createLiElem(i, this.#originalSelect.options[i].innerText));
            }
        }

        this.#liElemForInput.style.width = this.#calculateCustomInputWidth() + 'px';
    }

    #hideOriginalSelect() {
        this.#originalSelect.style.display = 'none';
    }

    #initCustomSelect() {
        this.#originalSelect.insertAdjacentHTML('afterend', this.#getCustomSelectHtml());
        return document.querySelector('.custom-select');
    }

    #getCustomSelectHtml() {
        return `<div class="custom-select">
                    <ul class="custom-select__choices">
                        <li class="custom-select__choice custom-select__choice_input">
                            <input type="text" name="tag_input" placeholder="Выберите теги" class="custom-select__input">
                        </li>
                    </ul>
                    <ul class="custom-select__list"></ul>
                </div>`;
    }

    #addCustomSelectList() {
        const originalSelectLength = this.#originalSelect.options.length;

        for (let i = 0; i < originalSelectLength; i++) {
            const li = `<li class="custom-select__item" data-id="${i}">${this.#originalSelect.options[i].innerText}</li>`;
            this.#customSelectList.insertAdjacentHTML('beforeend', li);
        }
    }

    #createLiElem(index, content) {
        const liElem = document.createElement('li');
        liElem.classList.add('custom-select__choice');
        liElem.append(this.#createTag(index, content));

        return liElem;
    }

    #createTag(index, content) {
        const spanElem = document.createElement('span');
        spanElem.classList.add('selected__item');
        spanElem.innerText = content;
        spanElem.dataset.id = index;

        const deleteBtn = document.createElement('i');
        deleteBtn.classList.add('delete__item')
        deleteBtn.innerHTML = '&times;';
        spanElem.append(deleteBtn);

        return spanElem;
    }

    #calculateCustomInputWidth() {
        const ulElem = document.querySelector('.custom-select__choices');
        const ulElemWidth = ulElem.clientWidth - this.#getElemStyleValue(ulElem, 'paddingRight') - this.#getElemStyleValue(ulElem, 'paddingLeft');
        const liElems = document.querySelectorAll('.custom-select__choice');
        let width = 0;

        for (let i = 0; i < liElems.length; i++) {
            if (liElems[i].classList.contains('custom-select__choice_input'))
                continue;

            width += Math.ceil(liElems[i].offsetWidth + this.#getElemStyleValue(liElems[i], 'marginRight'));
        }

        return ulElemWidth - width;
    }

    #getElemStyleValue(element, type) {
        return parseFloat(getComputedStyle(element)[type]);
    }

    #hasItem(index) {
        const allItems = document.getElementsByClassName('selected__item');

        for (let i = 0; i < allItems.length; i++) {
            if (+allItems[i].dataset.id === index) {
                return allItems[i];
            }
        }

        return false;
    }
}
