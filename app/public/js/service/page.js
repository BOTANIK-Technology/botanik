const PAGE = 'services';


function addressSelectors(idVal = null) {
    return document.getElementsByName('addresses' + suffix(idVal) + '[]');
}

function addAddressSelector(idVal = null) {
    let options = document.getElementById('address').innerHTML;

    let addressesBlock = document.getElementById('addresses' + suffix(idVal));
    addressesBlock.insertAdjacentHTML('beforeEnd', '<select class="address" name="addresses' + suffix(idVal) + '[]">' + options + '</select>')

}

const clearCloseModal = () => {
    unsetCookies(id);
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


function unsetCookies(idVal) {
    deleteCookie('inputs' + suffix(idVal));
    deleteCookie('timetable' + suffix(idVal));
    deleteCookie('checked' + suffix(idVal));
}


/*
 * Create/save service
 */
let saveBtn = document.getElementById('save-service');
let id = saveBtn.dataset.service;
saveBtn.addEventListener('click', function () {
    let data = send(id);
    data.id = id;
    let Request = postRequest(CURRENT_URL + '/confirm', data);
    Request.onload = function () {
        if (Request.status >= 200 && Request.status < 400) {
            unsetCookies(id);
            closeModal();
        } else {
            showErrors(Request.response)
        }
    };
});

function send(idVal = null) {
    return {
        'name': document.getElementById('service-name' + suffix(idVal)).value,
        'type': document.getElementById('service-type' + suffix(idVal)).value,
        'addresses': addressesVal(idVal),

        'durationHours': checkedByNameVal('durationHours'),
        'durationMinutes': checkedByNameVal('durationMinutes'),
        'intervalHours': checkedByNameVal('intervalHours'),
        'intervalMinutes': checkedByNameVal('intervalMinutes'),

        'timetable': getCookie('timetable' + suffix(idVal)),

        'price': document.getElementById('price' + suffix(idVal)).value,
        'bonus': document.getElementById('bonus' + suffix(idVal)).value,
        'grouped': groupVal(idVal),
        'quantity': document.getElementById('quantity' + suffix(idVal)).value,
        'message': document.getElementById('message' + suffix(idVal)).value,
        'prepay': document.getElementById('prepay' + suffix(idVal)).checked,
        'cashpay': document.getElementById('cashpay' + suffix(idVal)).checked,
        'onlinepay': document.getElementById('onlinepay' + suffix(idVal)).checked,
        'bonuspay': document.getElementById('bonuspay' + suffix(idVal)).checked,
        'prepay_message': document.getElementById('prepay-message' + suffix(idVal)).value,
        'prepay_card': document.getElementById('card' + suffix(idVal)).value,
    }
}

function addressesVal(idVal = null) {
    let addresses = document.getElementsByName('addresses' + suffix(idVal) + '[]');

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
    return getCheckedVal(document.getElementsByName('group' + suffix(idVal)))
}

const suffix = (idVal) => {
    if (idVal) {
        return '-' + idVal;
    }
    return '';
}


const setInitialData = (idVal) => {
    let inputs = getCookie('inputs' + suffix(idVal));
    if (inputs && Object.keys(inputs).length) {
        document.getElementById('service-name' + suffix(idVal)).value = inputs.name;
        document.getElementById('service-type' + suffix(idVal)).value = inputs.type;

        document.getElementById('price' + suffix(idVal)).value = inputs.price;
        document.getElementById('bonus' + suffix(idVal)).value = inputs.bonus;
        document.getElementById('quantity' + suffix(idVal)).value = inputs.quantity;
        document.getElementById('message' + suffix(idVal)).value = inputs.message;
        document.getElementById('prepay' + suffix(idVal)).checked = inputs.prepay;
        document.getElementById('cashpay' + suffix(idVal)).checked = inputs.cashpay;
        document.getElementById('onlinepay' + suffix(idVal)).checked = inputs.onlinepay;
        document.getElementById('bonuspay' + suffix(idVal)).checked = inputs.bonuspay;
        document.getElementById('prepay-message' + suffix(idVal)).value = inputs.prepay_message;
        document.getElementById('card' + suffix(idVal)).value = inputs.prepay_card;

        let selectors = addressSelectors(idVal);
        if (selectors.length < inputs.addresses.length) {
            for (let i = selectors.length; i < inputs.addresses.length; i++) {
                addAddressSelector(idVal);
            }
        }
        selectors = addressSelectors(idVal);
        setValues(selectors, inputs.addresses);

        setChecked(document.getElementsByName('group' + suffix(idVal)), inputs.grouped);

        setChecked(document.getElementsByName('durationHours'), inputs.durationHours);
        setChecked(document.getElementsByName('durationMinutes'), inputs.durationMinutes);
        setChecked(document.getElementsByName('intervalHours'), inputs.intervalHours);
        setChecked(document.getElementsByName('intervalMinutes'), inputs.intervalMinutes);
    }

    /**
     * Group
     */
    let groupBlock = document.getElementById('group-service' + suffix(idVal));
    let groupBtns = document.getElementsByName('group' + suffix(idVal));
    if (groupVal(idVal) ) {
        groupOff(groupBlock, idVal);
    }
    else {
        groupBlock.classList.remove('hidVale');
    }
    Object.keys(groupBtns).forEach((el) => {
        groupBtns[el].addEventListener('change', function () {
            if (!this.value) {
                groupOff(groupBlock, idVal);
            }
            else {
                groupBlock.classList.remove('hidVale');
            }
        })
    });

    let prepayBlock = document.getElementById('prepay-service' + suffix(idVal));
    let prepayBtn = document.querySelector('#prepay' + suffix(idVal));
    if (!prepayBtn.checked) {
        prepayOff(prepayBlock, idVal);
    }
    else {
        prepayBlock.classList.remove('hidVale');
    }
    prepayBtn.addEventListener('change', function () {
        if (this.checked)
            prepayBlock.classList.remove('hidVale');
        else
            prepayOff(prepayBlock, idVal)
    });
}
function groupOff(groupBlock, id) {
    groupBlock.classList.add('hide');
    document.getElementById('quantity' + suffix(id)).value = '';
    document.getElementById('message' + suffix(id)).value = '';
}

function prepayOff(prepayBlock, id) {
    prepayBlock.classList.add('hide');
    document.getElementById('card' + suffix(id)).value = '';
    document.getElementById('prepay-message' + suffix(id)).value = '';
}

function intervalVal(id) {
    return getCheckedVal(document.getElementsByName('interval' + suffix(id)))
}
function checkedByNameVal (name) {
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
calendar.addEventListener('click', function () {
    setCookie('inputs' + suffix(id), send(id) );
    window.location.href = this.dataset.href;
});
