class SlugGenerator {
    #translitMap = {
        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'yo', 'ж': 'zh',
        'з': 'z', 'и': 'i', 'й': 'y', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o',
        'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'h', 'ц': 'ts',
        'ч': 'ch', 'ш': 'sh', 'щ': 'sch', 'ъ': '', 'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu', 'я': 'ya'
    };
    #isPasting = false;
    #title;
    #slug;

    constructor(title, slug) {
        this.#title = title;
        this.#slug = slug;
    }

    init() {
        this.#title.addEventListener('input', this.#inputHandler.bind(this));
        this.#title.addEventListener('paste', this.#pasteHandler.bind(this));
    }

    #inputHandler(e) {
        if (this.#isPasting) return;

        const target = e.target;
        const targetValue = target.value;

        slug.value = this.#getUpdatedSlug(targetValue);
    }

    #pasteHandler(e) {
        this.#isPasting = true;

        const target = e.target;
        const data = e.clipboardData.getData('text/plain').trim();

        this.#setNewTitleValue(target, data);
        slug.value = this.#getUpdatedSlug(target.value);

        e.preventDefault();
        setTimeout(() => this.#isPasting = false, 0);
    }

    #getUpdatedSlug(targetValue) {
        let slug = '';

        for (let i = 0; i < targetValue.length; i++) {
            const ch = targetValue[i].toLowerCase();

            if (ch === ' ') {
                slug += '-';
            } else if (/[a-zа-яё0-9]/.test(ch)) {
                slug += this.#translitMap[ch] ?? ch.toLowerCase();
            }
        }

        return slug;
    }

    #setNewTitleValue(elem, data) {
        const start = elem.selectionStart;
        const end = elem.selectionEnd;

        elem.value = elem.value.substring(0, start) + data + elem.value.substring(end);
        elem.selectionStart = elem.selectionEnd = start + data.length;
    }
}
