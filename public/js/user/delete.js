let delUserBtn = document.getElementById('delete-user');
if (delUserBtn) {
    delUserBtn.addEventListener('click', function () {
        let Request = postRequest(CURRENT_URL+'/confirm');
        Request.onload = function() {
            if (Request.status >= 200 && Request.status < 400) {
                if (note && note.length)
                    closeModal(false, 'modal', note);
                closeModal();
            } else {
                showErrors(Request.response);
            }
        };
    });
}
