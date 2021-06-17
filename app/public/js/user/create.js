let addUserBtn = document.getElementById('add-user');
if (addUserBtn) {

    addUserBtn.addEventListener('click', function () {
        let send = {
            'name': fio.value,
            'phone': phone.value,
            'email': email.value,
            'password': password.value,
            'addresses': getValues(addressSelects),
            'timetables': getTimetables(),
        };

        if (master.checked) {
            send.role = master.value;
            send.services = addressServices(serviceSelects);
        } else
            send.role = admin.value;

        let Request = postRequest(createRoute+'/add-user', send);
        Request.onload = function() {
            if (Request.status >= 200 && Request.status < 400) {
                deleteCookie('input', COOKIE_URL);
                for (let i = 0; i < countService; i++) {
                    deleteCookie('timetable-'+i, COOKIE_URL);
                    deleteCookie('checked-'+i, COOKIE_URL);
                }
                if (note && note.length)
                    closeModal(false, 'modal', note);
                closeModal();
            } else {
                showErrors(Request.response);
            }
        };

    });

}
