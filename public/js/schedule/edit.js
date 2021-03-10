function update () {
    let id = this.dataset.id;
    let send = {
        'date': document.querySelector('#date-'+id).value,
        'time': document.querySelector('#time-'+id).value,
    };
    let Request = postRequest(this.dataset.href, send);
    Request.onload = function() {
        if (Request.status >= 200 && Request.status < 400) {
            closeModal();
        } else {
            showErrors(Request.response);
        }
    };
}

