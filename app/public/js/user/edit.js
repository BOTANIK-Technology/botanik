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
            send.addresses = getValues(addressSelects);
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
