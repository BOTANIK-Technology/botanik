let mouseDown = false;
document.body.addEventListener('mousedown', function () {
    mouseDown = true;
});
document.body.addEventListener('mouseup', function () {
    mouseDown = false;
});
let month = document.getElementById('month_picker');
let year = document.getElementById('year_picker');
let saveMonth = document.getElementById('save_month');



let showFromStorage = function(yearVal, monthVal) {
    let checkedArray = {};
    if (id) {
        checkedArray = getCookie('checked-' + id);
    } else {
        checkedArray = getCookie('checked');
    }
    if (checkedArray) {
        if (! (yearVal.value in checkedArray) ) {
            checkedArray[yearVal.value] = {};
        }
        if (monthVal.value in checkedArray) {
            checkedArray = checkedArray[yearVal.value][monthVal.value];
            for (let dateEl in checkedArray) {
                for (let timeEl of checkedArray[dateEl]) {
                    console.log(dateEl + ' ' + timeEl);
                    let cell = document.getElementById(dateEl + '-' + timeEl);
                    if (cell) cell.classList.add('checked')
                }
            }
        }
    }
}
showFromStorage(year, month);

let timeBtn = document.getElementById('time-confirm');
timeBtn.addEventListener('click', function () {
    saveMonthAction();
    closeModal();
});




month.addEventListener('change', () => {
    window.location.replace(CURRENT_URL + '?current_month=' + month.value + '&current_year=' + year.value);
});
year.addEventListener('change', () => {
    window.location.replace(CURRENT_URL + '?current_month=' + month.value + '&current_year=' + year.value);
});

saveMonth.addEventListener('click', () => {
    saveMonthAction();
});


// Привязка события к конкретному элементу
function bindEvent(element, type, handler) {
    if (element.addEventListener) {
        element.addEventListener(type, handler, false);
    } else {
        element.attachEvent('on' + type, handler);
    }
}

// Привязка события к выборке элементов
function bindEventAll(NodeList, type, handler) {
    for (let i = 0; i < NodeList.length; ++i) {
        let element = NodeList[i];
        bindEvent(element, type, handler);
    }
}

// let monthElements = document.querySelectorAll('#month_picker option');
// bindEventAll(monthElements, 'click', function (){
//     let currentLocation = window.location;
// console.log(currentLocation);
// });


let checkboxes = document.getElementsByClassName('checkbox');

Object.keys(checkboxes).forEach((el) => {

    checkboxes[el].addEventListener('mousedown', function () {
        checkboxes[el].classList.toggle('checked');
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

let saveMonthAction = () => {
    let allCookies;
    if (id) {
        allCookies =getCookie('timetable-' + id);
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
    console.log(allCookies, cookies);

    if (!allCookies) {
        allCookies = {};
    }
    let indexYear = year.value;
    if (!(indexYear in allCookies)) {
        allCookies[indexYear] = {};
    }

    allCookies[indexYear][month.value] = cookies;

console.log(allCookies);

    if (Object.keys(allCookies).length ) {
        if (id !== '') {
            setCookie('timetable-' + id, allCookies, {'path': COOKIE_URL});
            setCookie('checkedCell-' + id, allCookies, {'path': COOKIE_URL});
        } else {
            setCookie('timetable', allCookies, {'path': COOKIE_URL});
            setCookie('checked', allCookies, {'path': COOKIE_URL});
        }
    } else {
        if (id !== '') {
            deleteCookie('timetable-' + id, COOKIE_URL);
            deleteCookie('checkedCell-' + id, COOKIE_URL);
        } else {
            deleteCookie('timetable', COOKIE_URL);
            deleteCookie('checked', COOKIE_URL);
        }
    }
}
