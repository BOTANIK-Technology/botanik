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

function getValues () {
    let send = {
        'title': document.getElementById('title').value,
        'text': document.getElementById('text').value,
        'button': document.getElementById('button').value
    };
    let addrs = getAddresses();
    if (addrs)
        send.addresses = addrs;
    return send;
}

function design() {
    inputActive([document.getElementById('title'), document.getElementById('text'), document.getElementById('img'), document.getElementById('button')]);
    let x = document.getElementById('best-design');
    document.getElementById('button').addEventListener('change', function () {
        if (this.value.length > 0) addClass(x, 'active');
        else removeClass(x, 'active');
    });
}

let createBtn = document.getElementById('createInfo');
if (createBtn) {
    design();
    createBtn.addEventListener('click', sendEvent);
}

let delBtn = document.getElementById('delete');
if (delBtn)
    delBtn.addEventListener('click', function () {
        sendForm(this.dataset.url, false, false);
    });

let editBtn = document.getElementById('editInfo');
if (editBtn) {
    design();
    editBtn.addEventListener('click', sendEvent);
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
