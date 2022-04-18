function getMeta(metaName) {
    const metas = document.getElementsByTagName('meta');

    for (let i = 0; i < metas.length; i++) {
        if (metas[i].getAttribute('name') === metaName) {
            return metas[i].getAttribute('content');
        }
    }

    return false;
}

function addClass(item, classStr){
    item.classList.add(classStr);
}

function removeClass(item, classStr){
    item.classList.remove(classStr);
}

function putRequest(url, object = null, async = true) {
    return defaultRequest(url, 'PUT', object, async);
}

function postRequest(url, object = null, async = true, contentType = 'application/json;charset=UTF-8') {
    return defaultRequest(url, 'POST', object, async, contentType);
}

function defaultRequest (url, method = 'POST', object = null, async = true, contentType = 'application/json;charset=UTF-8') {
    let Request = new XMLHttpRequest();
    Request.open(method, url, async);
    Request.setRequestHeader("Content-Type", contentType);
    Request.setRequestHeader("X-CSRF-TOKEN", getMeta('csrf-token'));
    if (object)
        Request.send(JSON.stringify(object));
    else
        Request.send();
    return Request;
}

function getRequest(url) {
    let Request = new XMLHttpRequest();
    Request.open("GET", url);
    Request.setRequestHeader("X-CSRF-TOKEN", getMeta('csrf-token'));
    Request.send();
    return Request;
}

function redirect(url) {
    document.location.href = url;
}

function showErrors(response, message = '') {
    try {
        response = JSON.parse(response);
    } catch (e) {
        alert('Произошла ошибка, повторите операцию');
        console.log('JSON.parse(response) error');
        return false;
    }

    if (response.errors) {
        let errors = '';
        Object.keys(response.errors).forEach((i) => {
            errors += '<li class="error">' + response.errors[i] + '</li>';
        });

        let modal = document.createElement('div');
        modal.setAttribute('id', 'error-modal');
        modal.setAttribute('class', 'modal error');
        modal.innerHTML =
            '<div class="modal-dialog">' +
            '<div class="modal-content  add-footer ">' +
            '<div class="modal-header flex justify-content-between">' +
            '<div class="back align-self-center">' +
            '<span class="error fw500">Ошибка</span>' +
            '</div>' +
            '<button type="button" class="close" onclick="closeError()">×</button>' +
            '</div>' +
            '<div class="modal-body">' +
            '<p class="modal-message">' + message + '</p>' +
            '<ul class="error-list">' +
            errors +
            '</ul>' +
            '</div>' +
            '<div class="modal-footer text-align-center">' +
            '<button class="btn error" type="button" onclick="closeError()">ОК</button>' +
            '</div>' +
            '</div>' +
            '</div>'
        ;

        let before = document.getElementById('modal') ?? document.getElementById('app');
        document.body.insertBefore(modal, before);

        return true;
    }

    alert('Произошла ошибка, повторите операцию');
    return false;

}
