/* Edit */
let editBtn = document.getElementById('edit-schedule');
let service = document.querySelector('#service');
let address = document.querySelector('#address');
let master  = document.querySelector('#master');
let date    = document.querySelector('#date');
let time    = document.querySelector('#time');

editBtn.addEventListener('click', function () {
    let send = {

    };
    let Request = postRequest(this.dataset.href, send);
    Request.onload = function() {
        if (Request.status >= 200 && Request.status < 400) {
            closeModal();
        } else {
            showErrors(Request.response);
        }
    };
});

