scheduleWin.mode = 'edit';
scheduleWin.slug = document.querySelector('#url_slug');
scheduleWin.token = document.querySelector('#token_id');
scheduleWin.loadMonth (month, service_id, master_id, address_id);
scheduleWin.loadDay (date, service_id, master_id, address_id);


function send (id) {
    let send = {
        'date': scheduleWin.recordTime.date,
        'time': scheduleWin.recordTime.time
    };
    let href = document.getElementById('action').getAttribute('data-href');
    let Request = postRequest(href, send);
    Request.onload = function() {
        if (Request.status >= 200 && Request.status < 400) {
            closeModal();
        } else {
            showErrors(Request.response);
        }
    };
}

