function deleteSchedule() {
    let Request = postRequest(url+'/confirm');
    Request.onload = function() {
        if (Request.status >= 200 && Request.status < 400) {
            closeModal();
        } else {
            showErrors(Request.response);
        }
    };
}
