if (document.querySelector('.modal-create') ) {
    setInitialData();
    //styles
    // inputActive(document.getElementsByClassName('inp'));

    let svg = document.getElementById('add-icon').innerHTML;


    let addressSelectorSelect = document.getElementById('more-addresses-select');


    // function issetTimetable() {
    //     let timetable = getCookie('timetable');
    //     return !!(timetable && timetable !== 'undefined' && timetable.length);
    // }
    //
    // function getTimetable() {
    //     let timetable = getCookie('timetable');
    //     return JSON.parse(timetable);
    // }
    //
    // function setValues (objects, values) {
    //     Object.keys(objects).forEach((k) => {
    //         if (values[k])
    //             objects[k].value = values[k];
    //     });
    // }
    //
    // function setChecked (objects, value) {
    //     Object.keys(objects).forEach((el) => {
    //         if (objects[el].value === value) objects[el].checked = 'checked';
    //     });
    // }

    let groupBlock = document.getElementById('group-service');
    function groupOff () {
        groupBlock.classList.add('hide');
        document.getElementById('quantity').value = '';
        document.getElementById('message').value = '';
    }

    let prepayBlock = document.getElementById('prepay-service');
    function prepayOff () {
        prepayBlock.classList.add('hide');
        document.getElementById('card').value = '';
        document.getElementById('prepay-message').value = '';
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
                        typesList.classList.remove('none');
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
                        addrsList.classList.remove('none');
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
     * Group
     */
    let groupBtns = document.getElementsByName('group');
    if (groupVal() == 0) groupOff();
    else groupBlock.classList.remove('hide');
    Object.keys(groupBtns).forEach((el) => {
        groupBtns[el].addEventListener('change', function () {
            if (this.value == 0) groupOff();
            else groupBlock.classList.remove('hide');
        })
    });

    let prepayBtn = document.querySelector('#prepay');
    if (!prepayBtn.checked) prepayOff();
    else prepayBlock.classList.remove('hide');
    prepayBtn.addEventListener('change', function () {
        if (this.checked)
            prepayBlock.classList.remove('hide');
        else
            prepayOff()
    });


}

