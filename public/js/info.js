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
function getAddresses() {
    let addresses = document.getElementsByName('addresses[]');
    if (!addresses)
        return false;
    let array = [];
    Object.keys(addresses).forEach((object) => {
        array.push(addresses[object].value)
    });
    return array;
}

let createBtn = document.getElementById('createInfo');
if (createBtn) {
    design();
    createBtn.addEventListener('click', function () {
        let send = {
            'title': document.getElementById('title').value,
            'text': document.getElementById('text').value,
            'img': document.getElementById('img').value,
            'button': document.getElementById('button').value,
        };
        let addrs = getAddresses();
        if (addrs)
            send.addresses = addrs;

        doReq(send);
    });
}

let delBtn = document.getElementById('delete');
if (delBtn)
    delBtn.addEventListener('click', function () {doReq({'id':delBtn.dataset.id})});

let editBtn = document.getElementById('editInfo');
if (editBtn) {
    design();
    editBtn.addEventListener('click', function () {
        let send = {
            'title': document.getElementById('title').value,
            'text': document.getElementById('text').value,
            'img': document.getElementById('img').value,
            'button': document.getElementById('button').value,
            'id': editBtn.dataset.id
        };
        let addrs = getAddresses();
        if (addrs)
            send.addresses = addrs;

        doReq(send);
    });
}

let des = document.getElementById('design-btn');
if (des) {
    let inputs = document.getElementsByName('addresses[]');
    Object.keys(inputs).forEach((el) => {
        inputs[el].addEventListener('change', function () {
            if (inputs[el].value.length > 0) {
                addClass(des, 'active');
                return true;
            }
            else removeClass(des, 'active');
        });
    });

    document.getElementById('add-address').addEventListener('click', function () {
        document.getElementById('address-block').innerHTML += '<input type="text" name="addresses[]" class="input-bg map-pin">'
    })
}