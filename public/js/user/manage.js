function unsetCookies() {
    deleteCookie('user');
    deleteCookie('timetables');
    deleteCookie('user');
    deleteCookie('userData');
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
    for (let i of elems) {
        i.classList.add('hide');
    }
}

function showAll(className) {
    let elems = document.getElementsByClassName(className);
    for (let i of elems) {
        i.classList.remove('hide');
    }
}


let admin = document.getElementById('admin');
let master = document.getElementById('master');
let fio = document.getElementById('fio');
let password = document.getElementById('password');
let phone = document.getElementById('phone');
let email = document.getElementById('email');
let calendar = document.getElementsByClassName('calendar-a');
let addedServices = getCookie('service-type');


if (Object.keys(addedServices).length) {
    addedServices.forEach((id) => {
        document.getElementById('service-' + id).insertAdjacentHTML(
            'beforeEnd',
            '<select name="service-' + id + '[]" data-id="' + id + '" class="margin-top">' +
            serviceOptions +
            '</select>'
        );
    });
}

function getData() {
    let serviceTypeSelects = [];
    let serviceSelects = [];
    let addressSelects = [];


    serviceTypeSelects.push(document.getElementsByClassName('master-service-type'));
    serviceSelects.push(document.getElementsByClassName('master-service'));
    addressSelects.push(document.getElementsByClassName('master-address'));

    let data = {
        'name': fio.value,
        'phone': phone.value,
        'email': email.value,
        'password': password.value,
        'addresses': getValues(addressSelects),
        'services': getValues(serviceSelects),
        'types': getValues(serviceTypeSelects),
    };

    if (master.checked)
        data.master = 'checked';
    else if (admin.checked)
        data.admin = 'checked';

    return data;
}


if (calendar.length) {
    Object.keys(calendar).forEach((k) => {
        calendar[k].addEventListener('click', function () {
            userWin.saveCurrentData(k);
        });
    });
}

function setUserData(data, num) {
    let uData = getCookie('userData');
    if (!uData) {
        uData = [];
    }
    uData[num] = data;
    setCookie('userData', uData);
}

function getUserData(num) {
    num = parseInt(num);
    let uData = getCookie('userData');
    return uData[num];
}


let input = getCookie('user');

if (input) {
    fio.value = input.name || '';
    phone.value = input.phone || '';
    email.value = input.email || '';
    password.value = input.password || '';
    master.checked = !input.admin;
    admin.checked = input.admin;
}

toggleServices();

admin.addEventListener('change', function () {
    setCookie('user', getData());
    toggleServices()
});
master.addEventListener('change', function () {
    setCookie('user', getData());
    toggleServices()
});

document.getElementById('add-type').addEventListener('click', function () {
    setCookie('user', getData());
    window.location.href = this.dataset.href;
});


const clearCloseModal = () => {
    resetAll();
    closeModal();
}

/**
 * Close
 */
let close = document.getElementsByClassName('close');
if (close.length > 0) {
    Object.keys(close).forEach((k) => {
        close[k].removeEventListener('click', closeModal);
        close[k].addEventListener('click', clearCloseModal);
    });
}


document.getElementById('edit-user').addEventListener('click', function () {
    let href = this.dataset.ref;
    let data = getData();
    let send = {
        'name': data.name,
        'phone': data.phone,
        'email': data.email,
        'password': data.password,
        'timetables': getCookie('timetables')
    };

    if (master.checked) {
        send.role = master.value;
        send.services = data.services;
        send.addresses = data.addresses;
    } else {
        send.role = admin.value;
        send.addresses = getValues(adminAddressSelects);
    }

    let Request = postRequest(href, send);
    Request.onload = function () {
        if (Request.status >= 200 && Request.status < 400) {
            resetAll();
            closeModal();
        } else {
            showErrors(Request.response);
        }
    };
});


