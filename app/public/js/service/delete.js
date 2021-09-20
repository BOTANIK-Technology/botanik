let deleteBtn = document.getElementById('delete');

if (deleteBtn) {
    deleteBtn.addEventListener('click', function () {
        let Request = postRequest(url+'/confirm');
        Request.onload = function() {
            if (Request.status >= 200 && Request.status < 400) {
                closeModal();
            } else {
                closeModal();
                //showErrors(Request.response)
            }
        };
    });
}
