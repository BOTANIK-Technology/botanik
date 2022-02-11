
let create  = document.querySelector('#create');

// client.addEventListener('change', checkClient);

function send () {
    let data = {
        'client_id': client.value,
        'service_id': scheduleWin.service.value,
        'address_id': scheduleWin.address.value,
        'date': scheduleWin.recordTime.date,
        'time': scheduleWin.recordTime.time
    };
    if (scheduleWin.master.value) data.user_id = scheduleWin.master.value;
    let Request = postRequest(create.dataset.href, data);
    Request.onload = function() {
        if (Request.status >= 200 && Request.status < 400) {
            closeModal();
        } else {
            showErrors(Request.response);
        }
    };
}


