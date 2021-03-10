if (document.querySelector('.modal-edit') !== null && ids !== false) {

    //styles
    inputActive(document.getElementsByClassName('inp'));
    selectActive();

    let saveBtns = document.getElementsByName('save-service');
    let delBtns = document.getElementsByName('delete-service');

    function unsetCookies (without_id = false) {
        ids.forEach((id) => {
            if (without_id && id !== without_id) {
                deleteCookie('inputs-'+id, COOKIE_URL);
                deleteCookie('timetable-'+id, COOKIE_URL);
                deleteCookie('checked-'+id, COOKIE_URL);
            }
        });
    }

    function addressesVal (id) {
        let addresses = document.getElementsByName('addresses-'+id+'[]');
        let array = [];
        Object.keys(addresses).forEach((el) => {
            array.push(addresses[el].value);
        });
        return array;
    }

    let options = document.getElementById('address').innerHTML;
    function addAddressSelector (id) {
        let addressesBlock = document.getElementById('addresses-'+id);
        addressesBlock.insertAdjacentHTML('beforeEnd', '<select name="addresses-'+id+'[]">'+options+'</select>')
    }

    function issetTimetable(id) {
        let timetable = getCookie('timetable-'+id);
        return !!(timetable && timetable !== 'undefined' && timetable.length);
    }

    function getTimetable(id) {
        let timetable = getCookie('timetable-'+id);
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
            if (objects[el].value == value) objects[el].checked = 'checked';
        });
    }

    function groupOff (groupBlock, id) {
        addClass(groupBlock, 'hide');
        document.getElementById('quantity-'+id).value = '';
        document.getElementById('message-'+id).value = '';
    }

    function prepayOff (prepayBlock, id) {
        addClass(prepayBlock, 'hide');
        document.getElementById('card-'+id).value = '';
        document.getElementById('prepay-message-'+id).value = '';
    }

    function intervalVal (id) {return getCheckedVal(document.getElementsByName('interval-'+id))}
    function addressSelectors (id) {return document.getElementsByName('addresses-'+id+'[]')}
    function groupVal (id) {return getCheckedVal(document.getElementsByName('group-'+id))}

    function send (id) {
        return {
            'name': document.getElementById('service-name-'+id).value,
            'type': document.getElementById('service-type-'+id).value,
            'addresses': addressesVal(id),
            'interval': intervalVal(id),
            'range': document.getElementById('range-'+id).value,
            'price': document.getElementById('price-'+id).value,
            'bonus': document.getElementById('bonus-'+id).value,
            'grouped': groupVal(id),
            'quantity': document.getElementById('quantity-'+id).value,
            'message': document.getElementById('message-'+id).value,
            'prepay': document.getElementById('prepay-'+id).checked,
            'cashpay': document.getElementById('cashpay-'+id).checked,
            'onlinepay': document.getElementById('onlinepay-'+id).checked,
            'bonuspay': document.getElementById('bonuspay-'+id).checked,
            'prepay_message': document.getElementById('prepay-message-'+id).value,
            'prepay_card': document.getElementById('card-'+id).value,
        }
    }

    function getCheckedVal(objects) {
        let val = '';
        Object.keys(objects).forEach((el) => {
            if (objects[el].checked) val = objects[el].value;
        });
        return val;
    }

    /**
     * Add more addresses
     */
    let include = document.getElementsByClassName('include-address');
    Object.keys(include).forEach((btn) => {
        include[btn].addEventListener('click', function () {
            addAddressSelector(include[btn].dataset.id);
        });
    });

    ids.forEach((id) => {

        let calendar = document.getElementById('calendar-'+id);

        /**
         * Get cooked inputs
         */
        let inputs = getCookie('inputs-'+id);
        if (inputs && inputs !== undefined && inputs !== 'undefined') {
            inputs = JSON.parse(inputs);
            document.getElementById('service-name-'+id).value = inputs.name;
            document.getElementById('service-type-'+id).value = inputs.type;
            document.getElementById('range-'+id).value = inputs.range;
            document.getElementById('price-'+id).value = inputs.price;
            document.getElementById('bonus-'+id).value = inputs.bonus;
            document.getElementById('quantity-'+id).value = inputs.quantity;
            document.getElementById('message-'+id).value = inputs.message;
            document.getElementById('prepay-'+id).checked = inputs.prepay;
            document.getElementById('cashpay-'+id).checked = inputs.cashpay;
            document.getElementById('onlinepay-'+id).checked = inputs.onlinepay;
            document.getElementById('bonuspay-'+id).checked = inputs.bonuspay;
            document.getElementById('prepay-message-'+id).value = inputs.prepay_message;
            document.getElementById('card-'+id).value = inputs.prepay_card;

            let selectors = addressSelectors(id);
            if (selectors.length < inputs.addresses.length) {
                for (let i = selectors.length; i < inputs.addresses.length; i++) {
                    addAddressSelector(id);
                }
            }
            selectors = addressSelectors(id);
            setValues(selectors, inputs.addresses);

            setChecked(document.getElementsByName('group-'+id), inputs.grouped);
            setChecked(document.getElementsByName('interval-'+id), inputs.interval);
        }

        /**
         * Timetable cookie
         */
        let timetable = getCookie('timetable-'+id);
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
            unsetCookies(id);
            setCookie('inputs-'+id, JSON.stringify(send(id)), {'path': COOKIE_URL});
            window.location.href = this.dataset.href;
        });

        /**
         * Group
         */
        let groupBlock = document.getElementById('group-service-'+id);
        let groupBtns = document.getElementsByName('group-'+id);
        if (groupVal(id) == 0) groupOff(groupBlock, id);
        else removeClass(groupBlock, 'hide');
        Object.keys(groupBtns).forEach((el) => {
            groupBtns[el].addEventListener('change', function () {
                if (this.value == 0) groupOff(groupBlock, id);
                else removeClass(groupBlock, 'hide');
            })
        });

        let prepayBlock = document.getElementById('prepay-service-'+id);
        let prepayBtn = document.querySelector('#prepay-'+id);
        if (!prepayBtn.checked) prepayOff(prepayBlock, id);
        else removeClass(prepayBlock, 'hide');
        prepayBtn.addEventListener('change', function () {
            if (this.checked)
                removeClass(prepayBlock, 'hide');
            else
                prepayOff(prepayBlock, id)
        });

    });

    /**
     * Save service
     */
    Object.keys(saveBtns).forEach((btn) => {
        saveBtns[btn].addEventListener('click', function () {

            let id = saveBtns[btn].dataset.service;
            let data = send(id);
            data.id = id;
            if (issetTimetable(id))
                data.timetable = getTimetable(id);
            ids.forEach((id) => {
                deleteCookie('inputs-'+id, COOKIE_URL);
                deleteCookie('timetable-'+id, COOKIE_URL);
                deleteCookie('checked-'+id, COOKIE_URL);
            });
            let Request = postRequest(CURRENT_URL+'/confirm', data);
            Request.onload = function() {
                if (Request.status >= 200 && Request.status < 400) {
                    location.reload()
                } else {
                    showErrors(Request.response)
                }
            };

        })
    });

    /**
     * Delete service
     */
    Object.keys(delBtns).forEach((del) => {
        delBtns[del].addEventListener('click', function () {

            let send = {'id':delBtns[del].dataset.service};

            let Request = postRequest(CURRENT_URL+'/remove-service', send);
            Request.onload = function() {
                if (Request.status >= 200 && Request.status < 400) {
                    window.location.reload();
                } else {
                    showErrors(Request.response)
                }
            };
        })
    });

    /**
     * Close
     */
    let close = document.getElementsByClassName('close');
    if (close.length > 0) {
        Object.keys(close).forEach((k) => {
            close[k].removeEventListener('click', closeModal);
            close[k].addEventListener('click', unsetCookies);
            close[k].addEventListener('click', closeModal);
        });
    }
}
