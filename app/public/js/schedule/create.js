
let create  = document.querySelector('#create');

let client  = document.querySelector('#client');
let service = document.querySelector('#service');
let address = document.querySelector('#address');
let master  = document.querySelector('#master');

let date    = document.querySelector('#date');
let time    = document.querySelector('#time');

client.addEventListener('change', checkClient);

function checkClient(){
    if (this.value){

    }
}

function send () {
    let data = {
        'client_id': client.value,
        'service_id': service.value,
        'address_id': address.value,
        'date': scheduleWin.recordTime.date.substr(5),
        'time': scheduleWin.recordTime.time
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


document.addEventListener('DOMContentLoaded', function(){


});
