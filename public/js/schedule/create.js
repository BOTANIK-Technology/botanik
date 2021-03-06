let create  = document.querySelector('#create');

let client  = document.querySelector('#client');
let service = document.querySelector('#service');
let address = document.querySelector('#address');
let master  = document.querySelector('#master');
let date    = document.querySelector('#date');
let time    = document.querySelector('#time');

function send () {
    let data = {
        'client_id': client.value,
        'service_id': service.value,
        'address_id': address.value,
        'date': date.value,
        'time': time.value
    };
    if (master.value) data.user_id = master.value;
    let Request = postRequest(create.dataset.href, data);
    Request.onload = function() {
        if (Request.status >= 200 && Request.status < 400) {
            closeModal();
        } else {
            showErrors(Request.response);
        }
    };
}

inputActive([date, time]);
selectActive([client, service, address, master]);


create.addEventListener('click', send)
