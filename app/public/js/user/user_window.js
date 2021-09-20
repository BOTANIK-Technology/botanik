let CreateUserWindow = function() {

    this.token = null;

    this.slug = null;

    this.address = null;

    this.master = null;

    this.init = function() {
        this.token = document.querySelector('#token_id');
        this.slug    = document.querySelector('#url_slug');
        this.initAllServices();
        window.setTimeout(toogleServices, 1000);

    }

    this.initAllServices = function() {
        for(let i = 0; i < 256; i++) {
            let data = getCookie('user_data-' + i);
            if(data) {
                data = JSON.parse(data);
                this.changeService(i);
            } else {
                break;
            }
        }
        for(let i = 0; i < 256; i++) {
            let data = getCookie('admin_data-' + i);
            if(data) {
                data = JSON.parse(data);
                let address = document.querySelector('#admin-address-' + i);
                address.value = data.address_id;
            } else {
                break;
            }
        }
    }

    this.changeService= function(num) {
        let service = document.querySelector('#service-type-' + num);
        if(service) {
            let service_id = Number(service.value);
            this.saveCurrentData(num);
            this.loadAddresses(num, service_id);
        }
    }

    this.changeAddress = function(num) {
        this.saveCurrentData(num);
    }

    this.changeAdminAddress = function(num) {
        this.saveAdminCurrentData(num);
    }

    this.loadAddresses = function(num, service_id) {
        var _this = this;
        this.post({
            url: '/api/services_addresses',
            data: {
                service_id: service_id
            },
            success: function(response) {
                if(response.result === "OK") {
                    let cnt = "<option value=''>Адрес *</option>";
                    for(let addr in response.addresses) {
                        cnt += "<option value='" + response.addresses[addr][0].id + "'>" + response.addresses[addr][0].address + "</option>";
                    }
                    let address = document.querySelector('#address-' + num);
                    address.innerHTML = cnt;
                    removeClass(address, 'hide');
                    _this.setCurrentData(num);
                }
            }
        });
    }

    this.saveCurrentData = function(num) {
        let service = document.querySelector('#service-type-' + num);
        let address = document.querySelector('#address-' + num);
        let service_id = Number(service.value);
        let address_id = Number(address.value);
        let data = {
            service_id: service_id,
            address_id: address_id
        };
        setCookie('user_data-' + num, JSON.stringify(data));
    }

    this.saveAdminCurrentData = function(num) {
        let address = document.querySelector('#admin-address-' + num);
        let address_id = Number(address.value);
        let data = {
            address_id: address_id
        };
        setCookie('admin_data-' + num, JSON.stringify(data));
    }

    this.setCurrentData = function(num) {
        let service = document.querySelector('#service-type-' + num);
        let address = document.querySelector('#address-' + num);
        let data = getCookie('user_data-' + num);
        if (data) {
            data = JSON.parse(data);
            service.value = data.service_id;
            address.value = data.address_id;
        }
    }

    this.post = function(options) {
        let url = '/' + this.slug.value + options.url;
        let xhr = new XMLHttpRequest();
        let json = JSON.stringify(options.data);

        xhr.open("POST", url, true)
        xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');
        xhr.setRequestHeader('X-CSRF-TOKEN', this.token.value);
        xhr.onreadystatechange = function(e) {
            if(this.readyState !== 4) return null;
            if(typeof(options.success) !== 'undefined') {
                options.success(JSON.parse(this.responseText));
            }
        };
        xhr.send(json);
    }

}

let userWin = new CreateUserWindow();

document.addEventListener('DOMContentLoaded', function(){
    userWin.init();
});
