let ScheduleWindow = function () {

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

        this.service.addEventListener('change', function () {
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
                    let cnt = "<option value=''>Специалист</option>";
                    for (let mas in response.masters) {
                        cnt += "<option value='" + response.masters[mas][0].id + "'>" + response.masters[mas][0].name + "</option>";
                    }
                    _this.master.innerHTML = cnt;
                    let lblEl = document.querySelector('#master_label');
                    lblEl.style.display = 'block';
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
                console.log(response);
                let table = document.getElementById('user_calendar');
                table.innerHTML = '';
                for (let index in response) {
                    let row = document.createElement('tr');
                    row.classList.add('calendar_row');
                    let baseUrl = '/' + SLUG + '/schedule/create';

                    for (let i in response[index]) {
                        let cell = document.createElement('td');
                        cell.classList.add('calendar_cell');

                        // первая строка календаря
                        if (index == 0) {
                            if (i != 1) {
                            // Кнопка "назад"
                                cell.innerHTML = response[index][i].text;
                                cell.classList.add('pointer');
                                cell.addEventListener('click', () => {
                                    _this.loadMonth(response[index][i]['callback_data']);
                                } );

                            }
                            else {
                                cell.innerHTML = response[index][i].text;
                            }
                        }
                        else {
                            cell.innerHTML = response[index][i].text;
                        }
                        row.appendChild(cell);
                    }
                    table.appendChild(row);
                }

            }
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
