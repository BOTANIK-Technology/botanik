let ScheduleWindow = function () {
    this.recordTime = {date: date, time: time};
    this.paymentTypes = {cash_pay: false, bonus_pay: false, online_pay: false};

    this.token = null;
    this.slug = null;

    this.client = null;
    this.service = null;
    this.address = null;
    this.master = null;
    this.calendar = null;

    this.init = function () {
        let _this = this;


        this.token = document.getElementById('token_id');
        this.slug = document.getElementById('url_slug');
        this.client = document.getElementById('client');
        this.service = this.service || document.getElementById('service');
        this.address = this.address || document.getElementById('address');
        this.master = this.master || document.getElementById('master');


        if (this.service.value) {
            _this.loadAddresses();
            _this.loadMasters();
        }

        this.service.addEventListener('change', function () {
            _this.address.classList.add('hide');
            _this.master.classList.add('hide');
           if(_this.calendar) {
               _this.calendar.innerHTML = '';
           }
            _this.loadAddresses();
        });
    }

    this.loadAddresses = function () {
        let _this = this;
        let service_id = this.service.value;

        this.post({
            url: '/api/services_addresses',
            data: {
                service_id: service_id
            },
            success: function (response) {
                if (response.result === "OK") {
                    let cnt = "<option value=''>Адрес *</option>";
                    for (let addr in response.addresses) {
                        cnt += "<option value='" + response.addresses[addr][0].id + "'>" + response.addresses[addr][0].address + "</option>";
                    }
                    _this.address.innerHTML = cnt;
                    let lblEl = document.getElementById('address_label');
                    lblEl.classList.remove('hide');
                    if (response.is_empty) {
                        _this.address.removeEventListener('change', _this.monthCreateListener);
                        _this.address.addEventListener('change', _this.loadMasters);
                    } else {
                        _this.master.classList.add('hide');
                        _this.address.addEventListener('change', _this.monthCreateListener);
                        _this.master.removeEventListener('change', _this.loadMasters);
                    }
                    _this.address.classList.remove('hide');
                }
            }
        });
    }

    this.monthCreateListener = function (event) {
        scheduleWin.loadMonthCreate(currentMonth);
    }

    this.loadMasters = function () {
        let service_id = scheduleWin.service.value;

        scheduleWin.post({
            url: '/api/services_masters',
            data: {
                service_id: service_id
            },
            success: function (response) {
                if (response.result === "OK") {
                    if (response.masters.length) {
                        let cnt = "<option value=''>Специалист</option>";
                        for (let mas in response.masters) {
                            cnt += "<option value='" + response.masters[mas][0].id + "'>" + response.masters[mas][0].name + "</option>";
                        }
                        scheduleWin.master.innerHTML = cnt;
                    }
                    scheduleWin.master.classList.remove('hide');
                    scheduleWin.master.addEventListener('change', scheduleWin.monthCreateListener);
                }
            }
        });
    }

    this.loadMonthCreate = function (month) {
        console.log(this.mode, this.mode == 'create');
        if (this.mode == 'create') {
            let service_id = this.service.value;
            let master_id = this.master.value;
            let address_id = this.address.value;
            this.loadMonth(month, service_id, master_id, address_id)
        } else {
            this.loadMonth(month, service_id, master_id, address_id)
        }
    }

    this.loadMonth = function (month, service_id, master_id, address_id) {
        let _this = this;


        this.post({
            url: '/api/calendar',
            data: {
                month: month,
                service_id: service_id,
                master_id: master_id,
                address_id: address_id
            },
            success: function (response) {
                _this.paymentTypes = response.paymentTypes;
                let table = document.getElementById('user_calendar');
                table.innerHTML = '';
                for (let index in response.monthData) {
                    let row = document.createElement('tr');
                    row.classList.add('calendar_row');

                    for (let i in response.monthData[index]) {
                        let cellData = response.monthData[index][i];
                        let cell = document.createElement('td');
                        cell.classList.add('calendar_cell');
                        // первая строка календаря
                        if (index == 0) {
                            if (i != 1) {
                                // Кнопки
                                if (cellData.text == "⏪") {
                                    cell.classList.add('month_prev');
                                }
                                if (cellData.text == "⏩") {
                                    cell.classList.add('month_next');
                                }
                                cell.addEventListener('click', () => {
                                    _this.loadMonthCreate(cellData.callback_data);
                                });

                            } else {
                                cell.innerHTML = cellData.text;
                            }
                        } else {

                            let num = parseInt(cellData.text);
                            if (!isNaN(num) && num > 0) {
                                cell.setAttribute('onclick', 'scheduleWin.loadDayCreate("' + cellData.callback_data.substr(5) + '")');
                                let radioInput = document.createElement('input');
                                radioInput.setAttribute('type', 'radio');
                                radioInput.setAttribute('id', cellData.callback_data.substr(5));
                                radioInput.setAttribute('name', 'calendar_day');
                                radioInput.setAttribute('value', cellData.callback_data.substr(5));
                                let radioLabel = document.createElement('label');
                                radioLabel.innerHTML = cellData.text;

                                if (date && date == cellData.callback_data.substr(5)) {
                                    radioInput.checked = true
                                }
                                cell.appendChild(radioInput);
                                cell.appendChild(radioLabel);
                            } else {
                                cell.innerHTML = cellData.text;
                            }
                        }
                        row.appendChild(cell);
                    }
                    table.appendChild(row);
                }

            }
        });
    }

    this.loadDayCreate = function (date) {
        if (this.mode === 'create') {
            let service_id = this.service.value;
            let master_id = this.master.value;
            let address_id = this.address.value;
            this.loadDay(date, service_id, master_id, address_id)
        } else {
            this.loadDay(date, service_id, master_id, address_id)
        }
    }

    this.loadDay = function (date, service_id, master_id, address_id) {
        this.recordTime.date = date;
        let _this = this;
        console.log(_this.recordTime.time, _this.mode);

        this.post({
                url: '/api/times',
                data: {
                    day: date,
                    mode: _this.mode,
                    ignored_time: _this.recordTime.time,
                    service_id: service_id,
                    master_id: master_id,
                    address_id: address_id
                },
                success: function (response) {
                    let table = document.getElementById('user_times');
                    table.innerHTML = '';
                    for (let cellTime of response) {
                        let cell = document.createElement('div');
                        cell.setAttribute('onclick', 'scheduleWin.loadPayment("' + cellTime + '")');
                        let radioInput = document.createElement('input');
                        radioInput.setAttribute('type', 'radio');
                        radioInput.setAttribute('id', cellTime);
                        radioInput.setAttribute('name', 'calendar_cellTime');
                        radioInput.setAttribute('value', cellTime);
                        let radioLabel = document.createElement('label');
                        radioLabel.innerHTML = cellTime;

                        cell.appendChild(radioInput);
                        cell.appendChild(radioLabel);
                        cell.classList.add('time_cell');

                        table.appendChild(cell);
                        if (time && time == cellTime) {
                            radioInput.checked = true
                        }
                    }
                }


            }
        );
    }

    this.loadPayment = function (time) {
        this.recordTime.time = time;

        let hasPayment = false;

        let html = '<label for="cash_pay"></label>' +
            '<input class="pay-input" type="radio" name="cash_pay">';
        let container = document.getElementById('payments-block');

        if (this.paymentTypes.cash_pay) {
            hasPayment = true;
        //    document.getElementById('block-cash_pay').classList.remove('hide');
        }
        if (this.paymentTypes.bonus_pay) {
            hasPayment = true;
        //    document.getElementById('block-bonus_pay').classList.remove('hide');
        }
        if (this.paymentTypes.online_pay) {
            hasPayment = true;
        //    document.getElementById('block-online_pay').classList.remove('hide');
        }
        let createBtn = document.getElementById('action');
        createBtn.addEventListener('click', send);
        if (!hasPayment) {
        //    createBtn.classList.remove('hide');
        }
        document.getElementById('user_times').addEventListener('click', () => {
           createBtn.classList.remove('hide');
        });


    }


    this.post = function (options) {
        let url = '/' + this.slug.value + options.url;
        let xhr = new XMLHttpRequest();
        let json = JSON.stringify(options.data);

        xhr.open("POST", url, true)
        xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');
        xhr.setRequestHeader('X-CSRF-TOKEN', this.token.value);
        xhr.onreadystatechange = function (e) {
            if (this.readyState !== 4) return null;
            if (typeof (options.success) !== 'undefined') {
                options.success(JSON.parse(this.responseText));
            }
        };
        xhr.send(json);
    }
}

let scheduleWin = new ScheduleWindow();

document.addEventListener('DOMContentLoaded', function () {
    if (scheduleWin.mode == 'create') {
        scheduleWin.init();
    }
});
