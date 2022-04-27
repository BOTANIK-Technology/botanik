let btnAddr = document.getElementById('edit-addr');
if (btnAddr) {
    btnAddr.addEventListener('click', function () {
        let slug = document.getElementById('edit_addr_slug').value;
        let name = document.getElementById('edit_addr_name').value;
        let id = document.getElementById('edit_addr_id').value;
        let Request = postRequest(CURRENT_URL + '/save', {id: id, name: name});
        Request.onload = function () {
            if (Request.status >= 200 && Request.status < 400) {
                window.location.href = "/" + slug + "/services?view=addresses";
            } else {
                showErrors(Request.response)
            }
        };
    });
}
