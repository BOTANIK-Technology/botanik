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

    let groupBlock = document.getElementById('group-service');
    function groupOff () {
        groupBlock.classList.add('hide');
        document.getElementById('quantity').value = '';
        document.getElementById('message').value = '';
    }

    let prepayBlock = document.getElementById('prepay-service');
    function prepayOff () {
        prepayBlock.classList.add('hide');
        document.getElementById('card').value = '';
        document.getElementById('prepay-message').value = '';
    }

    /**
     * Group
     */
    let groupBtns = document.getElementsByName('group');
    if (groupVal() == 0) groupOff();
    else groupBlock.classList.remove('hide');
    Object.keys(groupBtns).forEach((el) => {
        groupBtns[el].addEventListener('change', function () {
            if (this.value == 0) groupOff();
            else groupBlock.classList.remove('hide');
        })
    });

    let prepayBtn = document.querySelector('#prepay');
    if (!prepayBtn.checked) prepayOff();
    else prepayBlock.classList.remove('hide');
    prepayBtn.addEventListener('change', function () {
        if (this.checked)
            prepayBlock.classList.remove('hide');
        else
            prepayOff()
    });
}
