const daysOfWeek = ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'];
const monthsOfYear = ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'];
const msInHour = 3_600_000;
const msInMinute = 60_000;

function isToday(date) {
    const now = new Date();

    return date.toDateString() === now.toDateString();
}

function isYesterday(date) {
    const now = new Date();
    const yesterday = new Date(now);
    yesterday.setDate(now.getDate() - 1);

    return date.toDateString() === yesterday.toDateString();
}

function isWeek(date) {
    const now = new Date();
    const diffDays = (now - date) / (1000 * 60 * 60 * 24);

    return diffDays < 7 && !isToday(date) && !isYesterday(date);
}

function padWithZero(num) {
    return num < 10 ? '0' + num : num;
}

function formatDate(elem, receivedDate, isAjax) {
    const now = new Date();
    const diff = now.getTime() - receivedDate.getTime();
    const pattern = `${padWithZero(receivedDate.getHours())}:${padWithZero(receivedDate.getMinutes())}`;
    let result;

    if (isToday(receivedDate)) {
        if (diff < msInHour) {
            if (diff < msInMinute) {
                result = 'Только что';
            } else {
                result = `${Math.round(diff / msInMinute)} мин. назад`;
            }
        } else {
            result = `Сегодня в ${pattern}`;
        }
    } else if (isYesterday(receivedDate)) {
        result = `Вчера в ${pattern}`;
    } else if (isWeek(receivedDate)) {
        result = `${daysOfWeek[receivedDate.getDay()]} в ${pattern}`;
    } else {
        result = `${receivedDate.getDate()} ${monthsOfYear[receivedDate.getMonth()]} ${receivedDate.getFullYear()}`;
    }

    elem.textContent = result;

    if (isAjax) {
        const start = (receivedDate.getTime() + msInMinute) - new Date().getTime();
        setTimeout(formatDate, start, elem, receivedDate, true);
    }
}

function dateFormatter(elem, datePublished, isAjax = false) {
    const receivedDate = new Date(new Date(datePublished).getTime() - (new Date().getTimezoneOffset() * 60 * 1000));

    if (isAjax) {
        const start = (receivedDate.getTime() + msInMinute) - new Date().getTime();
        elem.classList.remove('ajax');
        setTimeout(formatDate, start, elem, receivedDate, true);
    } else {
        formatDate(elem, receivedDate, false);
    }
}

export { dateFormatter };
