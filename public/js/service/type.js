let btnType = document.getElementById('edit-type');
if (btnType) {
    btnType.addEventListener('click', function () {
        let slug = document.getElementById('edit_type_slug').value;
        let name = document.getElementById('edit_type_name').value;
        let id = document.getElementById('edit_type_id').value;
        let Request = postRequest(CURRENT_URL + '/save', {id: id, name: name});
        Request.onload = function () {
            if (Request.status >= 200 && Request.status < 400) {
                window.location.href = "/" + slug + "/services?view=types";
            } else {
                showErrors(Request.response)
            }
        };
    });
}
