
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


document.addEventListener('DOMContentLoaded', function(){


    // const pickerBtn = document.querySelector('#select_datetime');


    // pickerBtn.addEventListener('click', function() {
    //     const picker = new SimplePicker();
    //     picker.enableTimeSection();
    //     picker.on('submit', function(selectedDate) {
    //         let day = selectedDate.getDate();
    //         let month = selectedDate.getMonth() + 1;
    //         let year = selectedDate.getFullYear();
    //
    //         let hours = selectedDate.getHours();
    //         let minutes = selectedDate.getMinutes();
    //
    //         if(day < 10) day = '0' + day;
    //         if(month < 10) month = '0' + month;
    //
    //         if(hours < 10) hours = '0' + hours;
    //         if(minutes < 10) minutes = '0' + minutes;
    //
    //
    //         date.value =  day + "." + month + "." + year;
    //         time.value =  hours + ":" + minutes;
    //     });
    //     picker.open();
    // });

});
