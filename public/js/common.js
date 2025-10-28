function closeHandler(e) {
    const target = e.target;

    if (!target.classList.contains('alert__close'))
        return;

    target.closest('.alert__message').remove();

    document.removeEventListener('click', closeHandler);
}

document.addEventListener('click', closeHandler);

const searchForm = document.querySelector('.search__form');

searchForm.addEventListener('submit', (e) => {
    const target = e.target;
    const inputElem = target.firstElementChild;

    if (inputElem.value.length === 0) {
        e.preventDefault();
        alert('Введите запрос');
    }
});
