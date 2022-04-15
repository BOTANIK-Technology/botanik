inputActive(document.getElementsByClassName('inp'));

function getInputs() {
    return {
        'password': document.querySelector('#pass'),
        'business_name': document.querySelector('#business-name'),
        'bot_name': document.querySelector('#bot-name'),
        'slug': document.querySelector('#slug'),
        'last_name': document.querySelector('input[name=last_name]'),
        'first_name': document.querySelector('input[name=first_name]'),
        'middle_name': document.querySelector('input[name=middle_name]'),
        'email': document.querySelector('#email'),
        'pay_token': document.querySelector('#pay-token'),
        'tg_token': document.querySelector('#tg-token'),
        'password_confirmation': document.querySelector('#re-pass'),
    }
}

function getRadios() {
    return {
        'catalog': document.querySelector('input[name=catalog]:checked'),
        'package': document.querySelector('input[name=package]:checked'),
    }
}

function getValues() {
    let inputs = getInputs();
    let radios = getRadios();
    let data = {};
    Object.keys(inputs).forEach((name) => {
        data[name] = inputs[name].value ?? null
    });
    Object.keys(radios).forEach((name) => {
        data[name] = radios[name].value ?? null
    });
    return data;
}

function clearValues() {
    let inputs = getInputs();
    Object.keys(inputs).forEach((name) => {
        inputs[name].value = ''
    });
    img.value = '';
}

function sendForm(href, image = false) {
    let data = getValues();
    image === false ? data.image_path = null : data.image_path = image;
    let xhr = postRequest(href, data);
    xhr.onload = function () {
        if (xhr.status === 200) {
            clearValues();
            document.location.href = '/a-level/management';
        } else {
            showErrors(xhr.response);
        }
    };
}

let sendBtn = document.querySelector('#create');
if (sendBtn) {
    sendBtn.addEventListener('click', function () {

        let href = this.dataset.href;
        if (getImage()) {

            let formData = new FormData();
            formData.append('image', getImage());
            formData.append('path', 'logos');
            let request = new XMLHttpRequest();
            request.open('POST', this.dataset.storage);
            request.send(formData);
            request.onload = function () {

                if (request.status >= 200 && request.status < 400) {

                    sendForm(href, JSON.parse(request.response).path);

                } else {
                    showErrors(request.response);
                }

            };

        } else {
            sendForm(href);
        }

    });
}
