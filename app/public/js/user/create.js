let addUserBtn = document.getElementById('add-user');
if (addUserBtn) {

    addUserBtn.addEventListener('click', function () {
        let send = {
            'name': fio.value,
            'phone': phone.value,
            'email': email.value,
            'password': password.value,
            //'addresses': getValues(addressSelects),
            'timetables': getTimetables(),
        };

        if (master.checked) {
            send.role = master.value;
            send.services_type = getValues(serviceSelects);
            send.services = getValues(serviceSelects);
            send.addresses = getValues(addressSelects);
        } else {
            send.role = admin.value;
            send.addresses = getValues(adminAddressSelects);
        }


        let Request = postRequest(createRoute+'/add-user', send);
        Request.onload = function() {
            if (Request.status >= 200 && Request.status < 400) {
                deleteCookie('input');
                for (let i = 0; i < countService; i++) {
                    deleteCookie('timetable-'+i);
                    deleteCookie('checked-'+i);
                    deleteCookie('user_data-'+i);
                    deleteCookie('admin_data-'+i);
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
