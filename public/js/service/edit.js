if (document.querySelector('.modal-edit')) {
    setInitialData(id);
    //styles
    // inputActive(document.getElementsByClassName('inp'));


    let delBtns = document.getElementsByName('delete-service');


    /**
     * Delete service
     */
    Object.keys(delBtns).forEach((del) => {
        delBtns[del].addEventListener('click', function () {
            let slug = document.getElementById('edit_type_slug').value;
            let send = {'id': delBtns[del].dataset.service};

            let Request = postRequest(CURRENT_URL + '/remove-service', send);
            Request.onload = function () {
                if (Request.status >= 200 && Request.status < 400) {
                    window.location.href = "/" + slug + "/services";
                } else {
                    showErrors(Request.response)
                }
            };
        })
    });
}
