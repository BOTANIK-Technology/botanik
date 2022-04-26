let mouseDown = false;
document.body.addEventListener('mousedown', function () {
    mouseDown = true;
});
document.body.addEventListener('mouseup', function () {
    mouseDown = false;
});

let month = document.getElementById('month_picker');
let year = document.getElementById('year_picker');


if (month.value) {
    month.addEventListener('change', () => {
        let res = true;
        if (!document.getElementById('save_month').classList.contains('saved')) {
            res = window.confirm('Вы не сохранили текущий месяц. Вы точно хотите продолжить без сохранения?');
        }
        if (res) {
            window.location.replace(CURRENT_URL + '?service_id=' + id
                + '&current_month=' + month.value
                + '&current_year=' + year.value
                + '&currentService=' + currentService
                + '&only_render=' + 1
                +'&mode=' + mode
            );
        }
    });
}

if (year) {
    year.addEventListener('change', () => {
        let res = true;
        if (!document.getElementById('save_month').classList.contains('saved')) {
            res = window.confirm('Вы не сохранили текущий месяц. Вы точно хотите продолжить без сохранения?');
        }
        if (res) {
            window.location.replace(CURRENT_URL + '?service_id=' + id
                + '&current_month=' + month.value
                + '&current_year=' + year.value
                + '&currentService=' + currentService
                + '&only_render=' + 1
                + '&mode=' + mode
            );
        }
    });
}

const showFromStorage = function (yearVal, monthVal, idVal = null) {
    console.log(yearVal, monthVal);
    let checkedArray = getFromStorage(yearVal, monthVal, idVal);
    for (let dateEl in checkedArray) {
        for (let timeEl of checkedArray[dateEl]) {
            let cell = document.getElementById(dateEl + '-' + timeEl);
            if (cell) cell.classList.add('checked')
        }
    }
}

const getFromStorage = function (yearVal, monthVal, idVal = null) {
    let timetables = getCookie('timetables');

    if (timetables.length || Object.keys(timetables)) {
        timetables = timetableDB;
        setCookie('timetables', timetables);
    }
    let timetable = timetables[currentService];
    if (timetable && (yearVal in timetable) && (monthVal in timetable[yearVal])) {
        return timetable[yearVal][monthVal];
    }
    return [];
}




const changeSavedButton = function (savedState) {
    let mButton = document.getElementById('save_month');
    if (mButton) {
        if (savedState) {
            mButton.classList.add('saved');
            mButton.textContent = 'Сохранено';
        } else {
            mButton.classList.remove('saved');
            mButton.textContent = 'Сохранить';
        }
    }
}
changeSavedButton(true)

/**
 * Close
 */

if (close.length > 0) {
    Object.keys(close).forEach((k) => {
        close[k].removeEventListener('click', clearCloseModal);
        close[k].addEventListener('click', function () {
            let res = true;
            if (!document.getElementById('save_month').classList.contains('saved')) {
                res = window.confirm('Вы не сохранили текущий месяц. Вы точно хотите продолжить без сохранения?');
            }
            if (res) {
                closeModal();
            }
        });
    });
}

/*
* Confirm
*/
let timeBtn = document.getElementById('time-confirm');
console.log(id, timeBtn);
if (timeBtn) {
    timeBtn.addEventListener('click', () => {
        saveMonthAction(id);
        setTimeout(closeModal, 500)
    });
}

let saveMonth = document.getElementById('save_month');
if (saveMonth) {
    saveMonth.addEventListener('click', () => {
        saveMonthAction(id);
    });
}

let checkboxes = document.getElementsByClassName('checkbox');
if (mode == 'edit') {
    Object.keys(checkboxes).forEach((el) => {

        checkboxes[el].addEventListener('mousedown', function () {
            checkboxes[el].classList.toggle('checked');
            changeSavedButton(false);

        });
        checkboxes[el].addEventListener('mouseover', function () {
            if (mouseDown) checkboxes[el].classList.toggle('checked');
        });
    });
}

let all = document.querySelector('#select-all');
if (all) {
    all.addEventListener('click', function () {
        changeSavedButton(false);
        Object.keys(checkboxes).forEach((el) => {
            checkboxes[el].classList.add('checked')
        });
    })
}

let clr = document.querySelector('#clear-all');
if (clr) {
    clr.addEventListener('click', function () {
        changeSavedButton(false);
        Object.keys(checkboxes).forEach((el) => {
            checkboxes[el].classList.remove('checked')
        });
    })
}


const saveMonthAction = (id) => {
    let allCookies = getCookie('timetables');
    currentService = parseInt(currentService);
    let serviceTimetable = allCookies[currentService];
    let cookies = {};

    let times = document.getElementsByClassName('checked');
    Object.keys(times).forEach((el) => {
        if (!cookies[times[el].dataset.day]) {
            cookies[times[el].dataset.day] = [times[el].dataset.time];
        } else {
            cookies[times[el].dataset.day].push(times[el].dataset.time);
        }
    });
    if (!serviceTimetable || !Object.keys(serviceTimetable).length) {
        serviceTimetable = {};
    }

    let indexYear = year.value;
    let indexMonth = month.value;


    if (!(indexYear in serviceTimetable)) {
        serviceTimetable[indexYear] = {};
    }

    serviceTimetable[indexYear][indexMonth] = cookies;
    let userData = getCookie('userData');
    if (userData && userData[currentService]) {
        service_id = userData[currentService].service_id;
    }

    let Request = postRequest(CURRENT_URL + '/check-records', {
        'service_id': service_id,
        'master_id': master_id,
        'year': indexYear,
        'month': indexMonth,
        'timetable': serviceTimetable
    });

    Request.onload = function () {
        let res = JSON.parse(Request.response);
        if (res && res.result === 'OK') {
            allCookies[currentService] = serviceTimetable;
            setCookie('timetables', allCookies);
            changeSavedButton(true);
        } else {
            showErrors(Request.response, 'Невозможно сохранить расписание. <br>Имеются записи клиентов на следующие даты:');
        }
    }


}

if (month && year) {
    showFromStorage(year.value, month.value, id);
}
