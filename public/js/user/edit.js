let editUserBtn = document.getElementById('edit-user');
if (editUserBtn) {
    editUserBtn.addEventListener('click', function () {
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

        unsetCookies(countService);

        let Request = postRequest(editRoute+'/edit-user', send);
        Request.onload = function() {
            if (Request.status >= 200 && Request.status < 400) {
                deleteCookie('input', COOKIE_URL);
                for (let i = 0; i < countService; i++) {
                    deleteCookie('timetable-'+i, COOKIE_URL);
                    deleteCookie('checked-'+i, COOKIE_URL);
                }
                closeModal();
            } else {
                showErrors(Request.response);
            }
        };

    });

}
