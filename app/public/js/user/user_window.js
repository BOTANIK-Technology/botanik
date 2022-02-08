let CreateUserWindow = function () {
    let isInit = true;
    this.token = null;

    let allServiceList = {};
    let allAddressList = {};

    this.slug = null;

    this.address = null;

    this.master = null;

    this.init = function () {
        this.token = document.querySelector('#token_id');
        this.slug = document.querySelector('#url_slug');
        this.loadAllServices();
        // this.initAllServices();
        // document.querySelector('input[name="role"]);

        window.setTimeout(toggleServices, 10);

    }

    this.initAllServices = function () {
        console.log(services);
        for (let i in userData) {
            if(userData[i]) {
                this.setCurrentData(userData[i]);
                this.satAdminCurrentData(userData[i]);
            }
        }
    }


    this.changeServiceType = function (num) {
        let serviceType = document.querySelector('#service-type-' + num);
        if (serviceType && serviceType.value) {
            let option = serviceType.querySelector('option.placeholder');
            if (option) {
                serviceType.removeChild(option);
            }
            let service_type_id = Number(serviceType.value);
            this.loadServices(num, service_type_id);
            if (!isInit) {
                this.saveCurrentData(num);
            }
        }
    }

    this.changeService = function (num, value = null) {
        let service = document.querySelector('#service-' + num);
        if (service.value || value) {
            let option = service.querySelector('option.placeholder');
            if (option) {
                service.removeChild(option);
            }
            if (value) {
                service.value = value;
            }
            let service_id = Number(service.value);

            this.loadAddresses(num, service_id);
            if (!isInit) {
                this.saveCurrentData(num);
            }
        }
    }

    this.changeAddress = function (num) {
        this.saveCurrentData(num);
    }

    this.changeAdminAddress = function (num) {
        this.saveAdminCurrentData(num);
    }

    this.loadAllServices = function () {
        let _this = this;
        this.post({
            url: '/api/services_list', success: function (response) {
                console.log(response.services);
                allServiceList = response.services;
                _this.loadAllAddresses();
            }
        });
    }

    this.loadAllAddresses = function () {
        let _this = this;
        this.post({
            url: '/api/services_addresses',
            success: function (response) {
                console.log(response.addresses);
                allAddressList = response.addresses;
                _this.initAllServices();
            }
        });
    }

    this.loadServices = function (num, service_type_id, value = null) {
        let cnt = '<option class="placeholder" value="0" selected>Выберите услугy</option>';

        let servL = allServiceList[service_type_id];

        for (let serv in servL) {
            cnt += "<option value='" + servL[serv].id + "'>" + servL[serv].name + "</option>";
        }
        let services = document.querySelector('#service-' + num);
        services.innerHTML = cnt;
        document.querySelector('#service-' + num).classList.remove('hide');
        if (value) {
            services.value = value;
        }
    }

    this.loadAddresses = function (num, service_id, value = null) {
        let cnt = '<option class="placeholder" value="0" selected>Выберите адрес</option>';
        let addrL = allAddressList[service_id];

        for (let addr in addrL) {
            cnt += "<option value='" + addrL[addr][0].id + "'>" + addrL[addr][0].address + "</option>";
        }
        let address = document.querySelector('#address-' + num);
        address.innerHTML = cnt;
        document.querySelector('#address-' + num).classList.remove('hide');
        if (value) {
            address.value = value;
        }

    }

    this.saveCurrentData = function (num) {
        let serviceType = document.querySelector('#service-type-' + num);
        let service = document.querySelector('#service-' + num);
        let address = document.querySelector('#address-' + num);
        let service_type_id = Number(serviceType.value);
        let service_id = Number(service.value);
        let address_id = Number(address.value);
        let data = {
            service_type_id: service_type_id,
            service_id: service_id,
            address_id: address_id,
        };
        setCookie('user_data-' + num, JSON.stringify(data));
    }

    this.saveAdminCurrentData = function (num) {
        let address = document.querySelector('#admin-address-' + num);
        let address_id = Number(address.value);
        let data = {
            address_id: address_id
        };
        setCookie('admin_data-' + num, JSON.stringify(data));
    }

    this.setCurrentData = function (data) {
        let num = data.service_id;
        let serviceType = document.getElementById('service-type-' + num);
        serviceType.value = data.service_type_id;
        if (data.service_type_id) {
            this.loadServices(num, data.service_type_id, data.service_id)
        }
        if (data.service_id) {
            this.loadAddresses(num, data.service_id, data.address_id)
        }

        isInit = false;
    }

    this.satAdminCurrentData = function (data) {
        let num = data.service_id;
        let address = document.querySelector('#admin-address-' + num);
        if (data) {
            address.value = data.address_id;
        }
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

let userWin = new CreateUserWindow();

document.addEventListener('DOMContentLoaded', function () {
    userWin.init();
});
