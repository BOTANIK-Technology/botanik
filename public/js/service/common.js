let monthsEnRu = {
    'january' : 'Январь',
    'february' : 'Февраль',
    'march' : 'Март',
    'april' : 'Апрель',
    'may' : 'Май',
    'june' : 'Июнь',
    'july' : 'Июль',
    'august' : 'Август',
    'september' : 'Сентябрь',
    'october' : 'Октябрь',
    'november' : 'Ноябрь',
    'december' : 'Декабрь'
}

function addressSelectors(idVal = null) {
    return document.getElementsByName('addresses[]');
}

function addAddressSelector(idVal = null) {
    let options = document.getElementById('address').innerHTML;

    let addressesBlock = document.getElementById('addresses');
    addressesBlock.insertAdjacentHTML('beforeEnd', '<select class="address" name="addresses[]">' + options + '</select>')

}

const clearCloseModal = () => {
    unsetCookies();
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


function unsetCookies() {
    deleteCookie('inputs');
    deleteCookie('timetables');
}


/*
 * Create/save service
 */
let saveBtn = document.getElementById('save-service');
if (saveBtn) {
    saveBtn.addEventListener('click', function () {
        let data = send(id);
        data.id = id;
        let Request = postRequest(CURRENT_URL + '/confirm', data);
        Request.onload = function () {
            if (Request.status >= 200 && Request.status < 400) {
                unsetCookies();
                closeModal();
            } else {
                showErrors(Request.response)
            }
        };
    });
}

function send(idVal = null) {
    return {
        'name': document.getElementById('service-name').value,
        'type': document.getElementById('service-type').value,
        'addresses': addressesVal(idVal),

        'durationHours': checkedByNameVal('durationHours'),
        'durationMinutes': checkedByNameVal('durationMinutes'),
        'intervalHours': checkedByNameVal('intervalHours'),
        'intervalMinutes': checkedByNameVal('intervalMinutes'),

        'timetables': getCookie('timetables'),

        'price': document.getElementById('price').value,
        'bonus': document.getElementById('bonus').value,
        'grouped': groupVal(idVal),
        'quantity': document.getElementById('quantity').value,
        'message': document.getElementById('message').value,
        'prepay': document.getElementById('prepay').checked,
        'cashpay': document.getElementById('cashpay').checked,
        'onlinepay': document.getElementById('onlinepay').checked,
        'bonuspay': document.getElementById('bonuspay').checked,
        'prepay_message': document.getElementById('prepay-message').value,
        'prepay_card': document.getElementById('card').value,
    }
}

function addressesVal(idVal = null) {
    let addresses = document.getElementsByName('addresses[]');

    let array = [];
    Object.keys(addresses).forEach((el) => {
        let present = false;
        for (let elm in array) {
            if (Number(array[elm]) === Number(addresses[el].value)) {
                present = true;
            }
        }

        if (present === true) {
            addresses[el].style.borderColor = "red";
            alert("Этот адрес уже присутствует в списке");
        } else {
            array.push(addresses[el].value);
        }
    });
    return array;
}

function getCheckedVal(objects) {
    let val = '';
    Object.keys(objects).forEach((el) => {
        if (objects[el].checked) val = objects[el].value;
    });
    return val;
}

function groupVal(idVal = null) {
    return getCheckedVal(document.getElementsByName('group'))
}


function setValues(objects, values) {
    Object.keys(objects).forEach((k) => {
        if (values[k])
            objects[k].value = values[k];
    });
}

function setChecked(objects, value) {
    Object.keys(objects).forEach((el) => {
        if (objects[el].value == value) objects[el].checked = 'checked';
    });
}

const setInitialData = (idVal) => {
    let inputs = getCookie('inputs');
    if (inputs && Object.keys(inputs).length) {
        document.getElementById('service-name').value = inputs.name;
        document.getElementById('service-type').value = inputs.type;

        document.getElementById('price').value = inputs.price;
        document.getElementById('bonus').value = inputs.bonus;
        document.getElementById('quantity').value = inputs.quantity;
        document.getElementById('message').value = inputs.message;
        document.getElementById('prepay').checked = inputs.prepay;
        document.getElementById('cashpay').checked = inputs.cashpay;
        document.getElementById('onlinepay').checked = inputs.onlinepay;
        document.getElementById('bonuspay').checked = inputs.bonuspay;
        document.getElementById('prepay-message').value = inputs.prepay_message;
        document.getElementById('card').value = inputs.prepay_card;

        let selectors = addressSelectors(idVal);
        if (selectors.length < inputs.addresses.length) {
            for (let i = selectors.length; i < inputs.addresses.length; i++) {
                addAddressSelector(idVal);
            }
        }
        selectors = addressSelectors(idVal);
        setValues(selectors, inputs.addresses);

        setChecked(document.getElementsByName('group'), inputs.grouped);

        setChecked(document.getElementsByName('durationHours'), inputs.durationHours);
        setChecked(document.getElementsByName('durationMinutes'), inputs.durationMinutes);
        setChecked(document.getElementsByName('intervalHours'), inputs.intervalHours);
        setChecked(document.getElementsByName('intervalMinutes'), inputs.intervalMinutes);
    }

    let slot = document.getElementById('filled-months');
    let slotTimes = usedMonths[0];
    for (let year of Object.keys(slotTimes)) {
        for (let month of Object.keys(slotTimes[year])) {
            let child = document.createElement('div');
            child.innerText = year + '/' + monthsEnRu[month];
            slot.appendChild(child);
        }
    }

    /**
     * Group
     */
    let groupBlock = document.getElementById('group-service');
    let groupBtns = document.getElementsByName('group');
    if (groupBlock) {
        if (groupVal(idVal)) {
            groupOff(groupBlock, idVal);
        } else {
            groupBlock.classList.remove('hide');
        }
        Object.keys(groupBtns).forEach((el) => {
            groupBtns[el].addEventListener('change', function () {
                if (!this.value) {
                    groupOff(groupBlock, idVal);
                } else {
                    groupBlock.classList.remove('hide');
                }
            })
        });
    }

    let prepayBlock = document.getElementById('prepay-service');
    let prepayBtn = document.querySelector('#prepay');
    if (prepayBtn) {
        if (!prepayBtn.checked) {
            prepayOff(prepayBlock, idVal);
        } else {
            prepayBlock.classList.remove('hidVale');
        }
        prepayBtn.addEventListener('change', function () {
            if (this.checked)
                prepayBlock.classList.remove('hidVale');
            else
                prepayOff(prepayBlock, idVal)
        });
    }
}

function groupOff(groupBlock, id) {
    groupBlock.classList.add('hide');
    document.getElementById('quantity').value = '';
    document.getElementById('message').value = '';
}

function prepayOff(prepayBlock, id) {
    prepayBlock.classList.add('hide');
    document.getElementById('card').value = '';
    document.getElementById('prepay-message').value = '';
}

function intervalVal(id) {
    return getCheckedVal(document.getElementsByName('interval'))
}

function checkedByNameVal(name) {
    return getCheckedVal(document.getElementsByName(name));
}

/**
 * Add more addresses
 */
let moreBtn = document.getElementById('include-address');
if (moreBtn) {
    moreBtn.addEventListener('click', function () {
        addAddressSelector(id);
    });
}


/**
 * Calendar
 */
let calendar = document.getElementById('calendar');
if (calendar) {
    calendar.addEventListener('click', function () {
        setCookie('inputs', send(id));
        window.location.href = this.dataset.href;
    });
}

