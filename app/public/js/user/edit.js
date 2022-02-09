let editUserBtn = document.getElementById('edit-user');
if (editUserBtn) {
    editUserBtn.addEventListener('click', function () {
        let data = getData();
        let send = {
            'name': data.fio,
            'phone': data.phone,
            'email': data.email,
            'password': data.password,
            'timetables': getCookie('timetable')
        };

        if (master.checked) {
            send.role = master.value;
            send.services = data.services;
            send.addresses = data.addresses;
        } else {
            send.role = admin.value;
            send.addresses = getValues(adminAddressSelects);
        }

        let Request = postRequest(editRoute+'/edit-user', send);
        Request.onload = function() {
            if (Request.status >= 200 && Request.status < 400) {
                closeModal();
            } else {
                showErrors(Request.response);
            }
        };

    });

}
