let mouseDown = false;
document.body.addEventListener('mousedown', function () {
    mouseDown = true;
});
document.body.addEventListener('mouseup', function () {
    mouseDown = false;
});


const changeSavedButton = function (savedState) {
    let mButton = document.getElementById('save_month');
    if (savedState) {
        mButton.classList.add('saved');
        mButton.textContent = 'Сохранено';
    } else {
        mButton.classList.remove('saved');
        mButton.textContent = 'Сохранить';
    }
}
changeSavedButton(true)


let month = document.getElementById('month_picker');
let year = document.getElementById('year_picker');
let saveMonth = document.getElementById('save_month');


const showFromStorage = function (yearVal, monthVal, idVal) {
    let timetable = getCookie('timetable-' + idVal);
    if (timetable && yearVal in timetable && monthVal in timetable[yearVal]) {
        let checkedArray = timetable[yearVal][monthVal];
        for (let dateEl in checkedArray) {
            for (let timeEl of checkedArray[dateEl]) {
                let cell = document.getElementById(dateEl + '-' + timeEl);
                if (cell) cell.classList.add('checked')
            }
        }
    }
}
showFromStorage(year.value, month.value, id);

let timeBtn = document.getElementById('time-confirm');
timeBtn.addEventListener('click', function () {
    saveMonthAction(id);
    closeModal();
});

saveMonth.addEventListener('click', () => {
    saveMonthAction(id);
});

let checkboxes = document.getElementsByClassName('checkbox');

Object.keys(checkboxes).forEach((el) => {

    checkboxes[el].addEventListener('mousedown', function () {
        checkboxes[el].classList.toggle('checked');
        changeSavedButton(false);

    });
    checkboxes[el].addEventListener('mouseover', function () {
        if (mouseDown) checkboxes[el].classList.toggle('checked');
    });
});

let all = document.querySelector('#select-all');
all.addEventListener('click', function () {
    Object.keys(checkboxes).forEach((el) => {
        addClass(checkboxes[el], 'checked')
    });
})

let clr = document.querySelector('#clear-all');
clr.addEventListener('click', function () {
    Object.keys(checkboxes).forEach((el) => {
        removeClass(checkboxes[el], 'checked')
    });
})

const saveMonthAction = (id) => {
    let allCookies;
    if (id) {
        allCookies = getCookie('timetable-' + id);
    } else {
        allCookies = getCookie('timetable');
    }
    let cookies = {};

    let times = document.getElementsByClassName('checked');
    Object.keys(times).forEach((el) => {
        if (!cookies[times[el].dataset.day]) {
            cookies[times[el].dataset.day] = [times[el].dataset.time];
        } else {
            cookies[times[el].dataset.day].push(times[el].dataset.time);
        }
    });
    if (!allCookies || !Object.keys(allCookies).length) {
        allCookies = {};
    }

    let indexYear = year.value;
    let indexMonth = month.value;


    if (!(indexYear in allCookies)) {
        allCookies[indexYear] = {};
    }

    allCookies[indexYear][indexMonth] = cookies;

    if (Object.keys(allCookies).length) {
        if (id !== '') {
            setCookie('timetable-' + id, allCookies);
            setCookie('checked-' + id, allCookies);
        } else {
            setCookie('timetable', allCookies);
            setCookie('checked', allCookies);
        }
    } else {
        if (id !== '') {
            deleteCookie('timetable-' + id);
            deleteCookie('checked-' + id);
        } else {
            deleteCookie('timetable');
            deleteCookie('checked');
        }
    }
    changeSavedButton(true);
}
