let addService = document.getElementById('add-service');
if (addService) {

    //styles
    inputActive(document.getElementsByClassName('inp'));
    selectActive();

    let svg = document.getElementById('add-icon').innerHTML;
    let calendar = document.getElementById('calendar');

    let addressSelector = document.getElementById('more-addresses');
    let addressSelectorSelect = document.getElementById('more-addresses-select');
    let moreBtn = document.getElementById('include-address');
    let addressesBlock = document.getElementById('addresses');
    function addressSelectors () {return document.getElementsByName('addresses[]')}
    function addAddressSelector () {
        addressesBlock.insertAdjacentHTML('beforeEnd', addressSelector.innerHTML)
    }

    function issetTimetable() {
        let timetable = getCookie('timetable');
        return !!(timetable && timetable !== 'undefined' && timetable.length);
    }

    function getTimetable() {
        let timetable = getCookie('timetable');
        return JSON.parse(timetable);
    }

    function setValues (objects, values) {
        Object.keys(objects).forEach((k) => {
            if (values[k])
                objects[k].value = values[k];
        });
    }

    function setChecked (objects, value) {
        Object.keys(objects).forEach((el) => {
            if (objects[el].value === value) objects[el].checked = 'checked';
        });
    }

    let groupBlock = document.getElementById('group-service');
    function groupOff () {
        addClass(groupBlock, 'hide');
        document.getElementById('quantity').value = '';
        document.getElementById('message').value = '';
    }

    let prepayBlock = document.getElementById('prepay-service');
    function prepayOff () {
        addClass(prepayBlock, 'hide');
        document.getElementById('card').value = '';
        document.getElementById('prepay-message').value = '';
    }

    /**
     * Get cooked inputs
     */
    let inputs = getCookie('inputs');
    if (inputs && inputs !== 'undefined') {
        inputs = JSON.parse(inputs);
        document.getElementById('service-name').value = inputs.name;
        document.getElementById('service-type').value = inputs.type;
        document.getElementById('range').value = inputs.range;
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

        let selectors = addressSelectors();
        if (selectors.length < inputs.addresses.length) {
            for (let i = selectors.length; i < inputs.addresses.length; i++) {
                addAddressSelector();
            }
        }
        selectors = addressSelectors();
        setValues(selectors, inputs.addresses);

        setChecked(document.getElementsByName('group'), inputs.grouped);
        setChecked(document.getElementsByName('interval'), inputs.interval);
    }

    function addressesVal () {
        let addresses = document.getElementsByName('addresses[]');
        let array = [];
        Object.keys(addresses).forEach((el) => {
            let present = false;
            for(let elm in array) {
                if(Number(array[elm]) === Number(addresses[el].value)) {
                    present = true;
                }
            }

            if(present === true) {
                addresses[el].style.borderColor="red";
                alert("Этот адрес уже присутствует в списке");
            } else {
                if (el != 0) array.push(addresses[el].value);
            }
        });
        return array;
    }

    function intervalVal () {
        return getCheckedVal(document.getElementsByName('interval'));
    }

    function groupVal () {
        return getCheckedVal(document.getElementsByName('group'));
    }

    function getCheckedVal(objects) {
        let val = '';
        Object.keys(objects).forEach((el) => {
            if (objects[el].checked) val = objects[el].value;
        });
        return val;
    }

    function send () {
        return {
            'name': document.getElementById('service-name').value,
            'type': document.getElementById('service-type').value,
            'addresses': addressesVal(),
            'interval': intervalVal(),
            'range': document.getElementById('range').value,
            'price': document.getElementById('price').value,
            'bonus': document.getElementById('bonus').value,
            'grouped': groupVal(),
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

    /**
     * Add type
     */
    let typeBtn = document.getElementById('add-type');
    let typeBlock = document.getElementById('type-block');
    let sendType;

    typeBtn.addEventListener('click', function () {
        typeBtn.remove();
        typeBlock.innerHTML = '<input type="text" id="new-type" placeholder="Введите тип"><div id="send-type">' + svg + '</div>';
        sendType = document.getElementById('send-type');
        sendType.addEventListener('click', function () {
            let typesList = document.getElementById('service-type');
            let newType = document.getElementById('new-type');
            if (newType.value.length) {
                let Request = postRequest(CURRENT_URL+'/add-type', {'service': newType.value});
                newType.value = '';
                Request.onload = function() {
                    if (Request.status >= 200 && Request.status < 400) {
                        let response = JSON.parse(Request.response);
                        removeClass(typesList, 'none');
                        typesList.insertAdjacentHTML(
                            'beforeEnd',
                            '<option value="'+ response.id +'">' +
                            response.type +
                            '</option>'
                        );
                    } else {
                        showErrors(Request.response)
                    }
                };
            }
        })
    });

    /**
     * Add address
     */
    let addressBlock = document.getElementById('address-block');
    let addressBtn = document.getElementById('add-address');

    addressBtn.addEventListener('click', function () {
        addressBtn.remove();
        addressBlock.innerHTML = '<input type="text" id="new-address" placeholder="Введите адрес"><div id="send-address">' + svg + '</div>';
        let sendAddress = document.getElementById('send-address');
        sendAddress.addEventListener('click', function () {
            let addrsList = document.getElementById('address');
            let newAddress = document.getElementById('new-address');
            if (newAddress.value.length) {
                let Request = postRequest(CURRENT_URL+'/add-address', {'address': newAddress.value});
                newAddress.value = '';
                Request.onload = function() {
                    if (Request.status >= 200 && Request.status < 400) {
                        let response = JSON.parse(Request.response);
                        removeClass(addrsList, 'none');
                        addrsList.insertAdjacentHTML(
                            'beforeEnd',
                            '<option value="'+ response.id +'">' +
                            response.address +
                            '</option>'
                        );
                        addressSelectorSelect.insertAdjacentHTML(
                            'beforeEnd',
                            '<option value="'+ response.id +'">' +
                            response.address +
                            '</option>'
                        );
                    } else {
                        showErrors(Request.response);
                    }
                };
            }
        })
    });

    /**
     * Add more addresses
     */
    if (moreBtn) {
        moreBtn.addEventListener('click', function () {
            addAddressSelector();
        });
    }


    /**
     * Timetable cookie
     */
    let timetable = getCookie('timetable');
    if (timetable && timetable !== 'undefined') {
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
        calendar.innerHTML = '<div class="abbr-tt color flex direction-column">' + html + '</div>';
    }

    /**
     * Calendar
     */
    calendar.addEventListener('click', function () {
        setCookie('inputs', JSON.stringify(send()), {'path':COOKIE_URL});
        window.location.href = this.dataset.href;
    });

    /**
     * Group
     */
    let groupBtns = document.getElementsByName('group');
    if (groupVal() == 0) groupOff();
    else removeClass(groupBlock, 'hide');
    Object.keys(groupBtns).forEach((el) => {
        groupBtns[el].addEventListener('change', function () {
            if (this.value == 0) groupOff();
            else removeClass(groupBlock, 'hide');
        })
    });

    let prepayBtn = document.querySelector('#prepay');
    if (!prepayBtn.checked) prepayOff();
    else removeClass(prepayBlock, 'hide');
    prepayBtn.addEventListener('change', function () {
        if (this.checked)
            removeClass(prepayBlock, 'hide');
        else
            prepayOff()
    });

    /*
     * Create service
     */
    addService.addEventListener('click', function () {
        let data = send();
        if (issetTimetable())
            data.timetable = getTimetable();
        let Request = postRequest(CURRENT_URL+'/add-service', data);
        Request.onload = function() {
            if (Request.status >= 200 && Request.status < 400) {
                deleteCookie('inputs', COOKIE_URL);
                deleteCookie('timetable', COOKIE_URL);
                deleteCookie('checked', COOKIE_URL);
                closeModal();
            } else {
                showErrors(Request.response)
            }
        };

    });

    /**
     * Close
     */
    let close = document.getElementsByClassName('close');
    if (close.length > 0) {
        Object.keys(close).forEach((k) => {
            close[k].addEventListener('click', function () {
                deleteCookie('inputs', COOKIE_URL);
                deleteCookie('timetable', COOKIE_URL);
                deleteCookie('checked', COOKIE_URL);
            })
        });
    }
}

