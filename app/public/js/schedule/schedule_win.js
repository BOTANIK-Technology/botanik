let ScheduleWindow = function () {
    this.recordTime = {date: "", time: ""};
    this.paymentTypes = {cash_pay: false, bonus_pay: false, online_pay: false};

    this.token = null;

    this.slug = null;

    this.service = null;

    this.address = null;

    this.master = null;

    this.init = function () {
        let _this = this;

        this.token = document.querySelector('#token_id');
        this.slug = document.querySelector('#url_slug');
        this.service = document.querySelector('#service');
        this.address = document.querySelector('#address');
        this.master = document.querySelector('#master');

        if(this.service.value) {
            this.loadAddresses();
            this.loadMasters();
        }

        this.service.addEventListener('click', function () {
                _this.loadAddresses();
                _this.loadMasters();
        });
        this.master.addEventListener('change', function () {
            _this.loadMonth(currentMonth);
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
                    let lblEl = document.querySelector('#address_label');
                    lblEl.style.display = 'block';
                }
            }
        });
    }

    this.loadMasters = function () {
        let _this = this;
        let service_id = this.service.value;

        this.post({
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
                        _this.master.innerHTML = cnt;
                        let lblEl = document.querySelector('#master_label');
                        lblEl.style.display = 'block';
                    }
                }
            }
        });
    }

    this.loadMonth = function (month) {
        let _this = this;
        let service_id = this.service.value;
        let master_id = this.master.value;
        let address_id = this.address.value;

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
                    let baseUrl = '/' + SLUG + '/schedule/create';

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
                                    _this.loadMonth(cellData.callback_data);
                                });

                            } else {
                                cell.innerHTML = cellData.text;
                            }
                        } else {

                            let num = parseInt(cellData.text);
                            if (!isNaN(num) && num > 0) {
                                cell.setAttribute('onclick', 'scheduleWin.loadDay("' + cellData.callback_data + '")');
                                let radioInput = document.createElement('input');
                                radioInput.setAttribute('type', 'radio');
                                radioInput.setAttribute('name', 'calendar_day');
                                radioInput.setAttribute('value', cellData.callback_data);
                                let radioLabel = document.createElement('label');
                                radioLabel.innerHTML = cellData.text;

                                cell.appendChild(radioLabel);
                                cell.appendChild(radioInput);
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

    this.loadDay = function (day) {
        this.recordTime.date = day;

        let _this = this;
        let service_id = this.service.value;
        let master_id = this.master.value;
        let address_id = this.address.value;


        this.post({
                url: '/api/times',
                data: {
                    day: day,
                    service_id: service_id,
                    master_id: master_id,
                    address_id: address_id
                },
                success: function (response) {
                    console.log(response);
                    let table = document.getElementById('user_times');
                    table.innerHTML = '';
                    for (let time of response) {
                        let baseUrl = '/' + SLUG + '/schedule/create';
                        let cell = document.createElement('div');
                        cell.setAttribute('onclick', 'scheduleWin.loadPayment("' + time + '")');
                        let radioInput = document.createElement('input');
                        radioInput.setAttribute('type', 'radio');
                        radioInput.setAttribute('name', 'calendar_time');
                        radioInput.setAttribute('value', time);
                        let radioLabel = document.createElement('label');
                        radioLabel.innerHTML = time;

                        cell.appendChild(radioLabel);
                        cell.appendChild(radioInput);
                        cell.classList.add('time_cell');

                        table.appendChild(cell);
                    }
                }


            }
        );
    }

    this.loadPayment = function (time) {
        this.recordTime.time = time;
        console.log(this.recordTime, this.paymentTypes);
        let html = '<label for="cash_pay"></label>' +
            '<input class="pay-input" type="radio" name="cash_pay">';
        let container = document.getElementById('payments-block');
        console.log('paymentType', this.paymentTypes, this.paymentTypes.cash_pay)
        if(this.paymentTypes.cash_pay) {
            document.getElementById('block-cash_pay').classList.remove('hide');
        }
        if(this.paymentTypes.bonus_pay) {
            document.getElementById('block-bonus_pay').classList.remove('hide');
        }
        if(this.paymentTypes.online_pay) {
            document.getElementById('block-online_pay').classList.remove('hide');
        }
        let createBtn = document.getElementById('create');
        createBtn.addEventListener('click', send);
        document.getElementById('payments-block').addEventListener('click', () => {
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
    scheduleWin.init();
});
