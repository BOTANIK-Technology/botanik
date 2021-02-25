function doReq(send) {
    let Request = postRequest(url + '/confirm', send);
    Request.onload = function() {
        if (Request.status >= 200 && Request.status < 400) {
            closeModal();
        } else {
            showErrors(Request.response);
        }
    };
}
function design() {
    inputActive([document.getElementById('title'), document.getElementById('text'), document.getElementById('img'), document.getElementById('button')]);
    let x = document.getElementById('best-design');
    document.getElementById('button').addEventListener('change', function () {
        if (this.value.length > 0) addClass(x, 'active');
        else removeClass(x, 'active');
    });
}

let createBtn = document.getElementById('createShare');
if (createBtn) {
    design();
    createBtn.addEventListener('click', function () {
        doReq(
            {
                'title': document.getElementById('title').value,
                'text': document.getElementById('text').value,
                'img': document.getElementById('img').value,
                'button': document.getElementById('button').value,
                'user_id': document.getElementById('user_id').value
            }
        );
    });
}

let delBtn = document.getElementById('delete');
if (delBtn)
    delBtn.addEventListener('click', function () {doReq({'id':delBtn.dataset.id})});

let editBtn = document.getElementById('editShare');
if (editBtn) {
    design();
    editBtn.addEventListener('click', function () {
        doReq(
            {
                'title': document.getElementById('title').value,
                'text': document.getElementById('text').value,
                'img': document.getElementById('img').value,
                'button': document.getElementById('button').value,
                'user_id': document.getElementById('user_id').value,
                'id': editBtn.dataset.id
            }
        );
    });
}