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
        window.location.replace(CURRENT_URL + '?service_id=' + id + '&current_month=' + month.value + '&current_year=' + year.value);
    });
}

if(year) {
    year.addEventListener('change', () => {
        window.location.replace(CURRENT_URL + '?service_id=' + id + '&current_month=' + month.value + '&current_year=' + year.value);
    });
}

const showFromStorage = function (yearVal, monthVal, idVal = null) {
    let  timetable = getCookie('timetable' + suffix(idVal) );
    console.log(timetable, timetableDB);
    if (!(Object.keys(timetable).length) ){
        timetable = timetableDB;
        setCookie('timetable' + suffix(idVal), timetable);
    }

    if (timetable && (yearVal in timetable) && (monthVal in timetable[yearVal])) {
        let checkedArray = timetable[yearVal][monthVal];
        for (let dateEl in checkedArray) {
            for (let timeEl of checkedArray[dateEl]) {
                let cell = document.getElementById(dateEl + '-' + timeEl);
                if (cell) cell.classList.add('checked')
            }
        }
    }
}
if (month && year) {
    showFromStorage(year.value, month.value, id);
}




const changeSavedButton = function (savedState) {
    let mButton = document.getElementById('save_month');
    if(mButton) {
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







let timeBtn = document.getElementById('time-confirm');
if (timeBtn) {
    timeBtn.addEventListener('click', function () {
        saveMonthAction(id);
        closeModal();
    });
}

let saveMonth = document.getElementById('save_month');
if(saveMonth) {
    saveMonth.addEventListener('click', () => {
        saveMonthAction(id);
    });
}

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
    let allCookies = getCookie('timetable' + suffix(id));
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
            setCookie('timetable' + suffix(id), allCookies);
    } else {
            deleteCookie('timetable' + suffix(id) );

    }
    changeSavedButton(true);
}
