let CreateUserWindow = function() {

    this.token = null;

    this.slug = null;

    this.address = null;

    this.master = null;

    this.init = function() {
        let _this = this;
        this.token = document.querySelector('#token_id');
        this.slug    = document.querySelector('#url_slug');
        this.changeService(0);
    }

    this.changeService= function(num) {
        let service = document.querySelector('#service-type-' + num);
        let service_id = Number(service.value);
        this.loadAddresses(num, service_id);
    }

    this.loadAddresses = function(num, service_id) {
        let _this = this;

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
                    let lblEl = document.querySelector('#address-' + num);
                    lblEl.style.display = 'block';
                }
            }
        });
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
