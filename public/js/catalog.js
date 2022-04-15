function doReq(send, object) {
    let Request = postRequest(object.dataset.url, send);
    Request.onload = function() {
        if (Request.status >= 200 && Request.status < 400) {
            closeModal();
        } else {
            showErrors(Request.response);
        }
    };
}

function send() {
    return {
        'title':   document.getElementById('title').value,
        'text':    document.getElementById('text').value,
        'img':     document.getElementById('img').value,
        'price':   document.getElementById('price').value,
        'article': document.getElementById('article').value,
        'count':   document.getElementById('count').value
    }
}

function sendForm (href, image = false, send = true) {
    let data = null;
    if (send) {
        data = getValues();
        image === false ? data.img = null : data.img = image;
    }
    let xhr = postRequest(href, data);
    xhr.onload = function() {
        if (xhr.status === 200) {
            closeModal();
        } else {
            showErrors(xhr.response);
        }
    };
}

function design() {
    inputActive([
        document.getElementById('title'),
        document.getElementById('text'),
        document.getElementById('img'),
        document.getElementById('price'),
        document.getElementById('article'),
        document.getElementById('count'),
    ]);
}

/*let delBtn = document.getElementById('delete-btn');
if (delBtn)
    delBtn.addEventListener('click', function () {doReq({}, delBtn)});*/
let delBtn = document.getElementById('delete');
if (delBtn)
    delBtn.addEventListener('click', function () {
        sendForm(this.dataset.url, false, false);
    });

let createBtn = document.getElementById('create-btn');
if (createBtn) {
    design();
    createBtn.addEventListener('click', function () {
        doReq(send(), createBtn)
    })
}

let editBtn = document.getElementById('edit-btn');
if (editBtn) {
    design();
    editBtn.addEventListener('click', function () {
        let data = send();
        data.id = editBtn.dataset.id;
        doReq(data, editBtn)
    });
}