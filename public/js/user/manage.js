function getTimetables() {
    let array = [];
    for (let i = 0; i < countService; i++)
        array.push(JSON.parse(getCookie('timetable-'+i)));
    return array;
}

function addressServices (array) {
    let returned = [];
    array.forEach((select) => {
        Object.keys(select).forEach((value) => {
            returned.push(select[value].value)
        });
    });
    return returned;
}

function getValues (array) {
    let returned = [];
    array.forEach((select) => {
        Object.keys(select).forEach((value) => {
            returned.push(select[value].value)
        });
    });
    return returned;
}

function setValues (selects, values) {
    let j = 0;
    selects.forEach((select) => {
        Object.keys(select).forEach((k) => {
            select[k].value = values[j];
            j++;
        });
    });
}

function getData() {
    let data = {
        'fio': fio.value,
        'phone': phone.value,
        'email': email.value,
        'password': password.value,
        'addresses': getValues(addressSelects),
        'services': getValues(serviceSelects),
    };

    if (master.checked)
        data.master = 'checked';
    else if (admin.checked)
        data.admin = 'checked';

    return data;
}

function toogleServices(serviceSelects, show) {
    serviceSelects.forEach((selects) => {
        Object.keys(selects).forEach((k) => {
            if (show === true) addClass(selects[k], 'hide');
            else removeClass(selects[k], 'hide');
        });
    });
}

let admin = document.getElementById('admin');
let master = document.getElementById('master');
let fio = document.getElementById('fio');
let password = document.getElementById('password');
let phone = document.getElementById('phone');
let email = document.getElementById('email');
let calendar = document.getElementsByClassName('calendar-a');
let serviceOptions = document.getElementById('service-type').innerHTML;
let addedServices = getCookie('services');

inputActive([fio, password, phone, email]);
selectActive();

if (addedServices) {
    addedServices = JSON.parse(addedServices);
    addedServices.forEach((id) => {
        document.getElementById('service-types-'+id).insertAdjacentHTML(
            'beforeEnd',
            '<select name="service-'+ id +'[]" data-id="'+ id +'" class="margin-top">' +
            serviceOptions +
            '</select>'
        );
    });
}

let serviceSelects = [];
let addressSelects = [];
for (let i = 0; i < countService; i++) {
    serviceSelects.push(document.getElementsByName('service-'+i+'[]'));
    addressSelects.push(document.getElementsByName('address-'+i+'[]'));
}

if (calendar.length) {
    Object.keys(calendar).forEach((k) => {
        calendar[k].addEventListener('click', function () {
            setCookie('input', JSON.stringify(getData()), {'path':COOKIE_URL});
            window.location.href = this.dataset.href;
        });
    });
} else {
    for (let i = 0; i < countService; i++) {
        deleteCookie('timetable-'+i, COOKIE_URL);
        deleteCookie('checked-'+i, COOKIE_URL);
    }
}

let input = getCookie('input');
if (input) {
    input = JSON.parse(input);
    fio.value = input.fio;
    phone.value = input.phone;
    email.value = input.email;
    password.value = input.password;
    addresses = setValues(addressSelects, input.addresses);
    services = setValues(serviceSelects, input.services);
    master.checked = input.master;
    admin.checked = input.admin;
}

if (calendar.length) {
    for (let i = 0; i < countService; i++) {
        if (calendar[i] !== undefined && calendar[i] && calendar[i] !== 'undefined') {
            let timetable = getCookie('timetable-'+i);
            if (timetable) {
                timetable = JSON.parse(timetable);
                let html = '';
                Object.keys(timetable).forEach((time) => {
                    let day;
                    switch (time) {
                        case 'monday':
                            day = 'ПН';
                            break;
                        case 'tuesday':
                            day = 'ВТ';
                            break;
                        case 'wednesday':
                            day = 'СР';
                            break;
                        case 'thursday':
                            day = 'ЧТ';
                            break;
                        case 'friday':
                            day = 'ПТ';
                            break;
                        case 'saturday':
                            day = 'СБ';
                            break;
                        case 'sunday':
                            day = 'ВС';
                    }
                    html += '<span>' + day + '. ' + timetable[time][0] + ' - ' + timetable[time][timetable[time].length-1] + '</span>';
                });
                calendar[i].innerHTML = '<div class="abbr-tt color flex direction-column">' + html + '</div>';
            }
        }
    }
}

if (admin.checked) toogleServices(serviceSelects, true);
admin.addEventListener('change', function () {
    toogleServices(serviceSelects, true)
});
master.addEventListener('change', function () {
    toogleServices(serviceSelects, false)
});

document.getElementById('add-type').addEventListener('click', function () {
    setCookie('input', JSON.stringify(getData()), {'path':COOKIE_URL});
    window.location.href = this.dataset.href;
});

let close = document.getElementsByClassName('close');
if (close.length > 0) {
    Object.keys(close).forEach((k) => {
        close[k].addEventListener('click', function () {
            deleteCookie('input', COOKIE_URL);
            for (let i = 0; i < countService; i++) {
                deleteCookie('timetable-'+i, COOKIE_URL);
                deleteCookie('checked-'+i, COOKIE_URL);
            }
        })
    });
}
