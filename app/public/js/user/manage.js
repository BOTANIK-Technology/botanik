function getTimetables() {
    let array = [];
    for (let i = 0; i < countService; i++) {
        array.push(JSON.parse(getCookie('timetable-' + i)));
    }
    return array;
}

function unsetCookies(count) {
    if (!count) return;
    for (let i = 0; i < count; i++) {
        deleteCookie('timetable-' + i);
        deleteCookie('checked-' + i);
    }
}

function addressServices(array) {
    let returned = [];
    array.forEach((select) => {
        Object.keys(select).forEach((value) => {
            returned.push(select[value].value)
        });
    });
    return returned;
}

function getValues(array) {
    let returned = [];
    array.forEach((select) => {
        Object.keys(select).forEach((value) => {
            returned.push(select[value].value)
        });
    });
    return returned;
}

function setValues(selects, values) {
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

function toggleServices() {
    let addTypeBtn = document.getElementById('add-type');
    if (master.checked) {
        addTypeBtn.innerHTML = "Добавить услугу к специалисту";
        showAll('master-only');
        hideAll('admin-only');
    } else {
        addTypeBtn.innerHTML = "Добавить адрес к администратору";
        hideAll('master-only');
        showAll('admin-only');
    }
}

function hideAll(className) {
    let elems = document.getElementsByClassName(className);
    for (let i = 0; i < elems.length; i++) {
        elems[i].classList.add('hide');
    }
}

function showAll(className) {
    let elems = document.getElementsByClassName(className);
    for (let i = 0; i < elems.length; i++) {
        elems[i].classList.remove('hide');
    }
}


let admin = document.getElementById('admin');
let master = document.getElementById('master');
let fio = document.getElementById('fio');
let password = document.getElementById('password');
let phone = document.getElementById('phone');
let email = document.getElementById('email');
let calendar = document.getElementsByClassName('calendar-a');
// let serviceOptions = document.getElementById('service-type-99').innerHTML;
let addedServices = getCookie('service-type');

// inputActive([fio, password, phone, email]);
// selectActive();

if (Object.keys(addedServices).length) {
    console.log(addedServices);
    addedServices.forEach((id) => {
        document.getElementById('service-' + id).insertAdjacentHTML(
            'beforeEnd',
            '<select name="service-' + id + '[]" data-id="' + id + '" class="margin-top">' +
            serviceOptions +
            '</select>'
        );
    });
}

let serviceTypeSelects = [];
let serviceSelects = [];
let addressSelects = [];
let adminAddressSelects = [];
for (let i = 0; i < countService; i++) {
    serviceTypeSelects.push(document.getElementsByName('service-type-' + i + '[]'));
    serviceSelects.push(document.getElementsByName('service-' + i + '[]'));
    addressSelects.push(document.getElementsByName('address-' + i + '[]'));
    adminAddressSelects.push(document.getElementsByName('admin-address-' + i + '[]'));
}

if (calendar.length) {
    Object.keys(calendar).forEach((k) => {
        calendar[k].addEventListener('click', function () {
            setCookie('input', JSON.stringify(getData()), {'path': COOKIE_URL});
            window.location.href = this.dataset.href;
        });
    });
} else {
    unsetCookies(countService);
}

let input = getCookie('user');
console.log(input);
if (input) {
    fio.value = input.fio;
    phone.value = input.phone;
    email.value = input.email;
    password.value = input.password;
    master.checked = input.master;
    admin.checked = input.admin;
    for (let serv of input.services) {
        console.log('serv', serv);
        let addresses = setValues(addressSelects, addresses);
        let services = setValues(serviceSelects, services);
        let services_types = setValues(serviceTypeSelects, service_types);
    }
}


admin.addEventListener('change', function () {
    setCookie('input', JSON.stringify(getData()), {'path': COOKIE_URL});
    toggleServices()
});
master.addEventListener('change', function () {
    setCookie('input', JSON.stringify(getData()), {'path': COOKIE_URL});
    toggleServices()
});

document.getElementById('add-type').addEventListener('click', function () {
    setCookie('input', JSON.stringify(getData()), {'path': COOKIE_URL});
    window.location.href = this.dataset.href;
});

let close = document.getElementsByClassName('close');
if (close.length > 0) {
    Object.keys(close).forEach((k) => {
        close[k].addEventListener('click', function () {
            deleteCookie('input', COOKIE_URL);
            for (let i = 0; i < countService; i++) {
                deleteCookie('timetable-' + i, COOKIE_URL);
                deleteCookie('checked-' + i, COOKIE_URL);
            }
        })
    });
}



