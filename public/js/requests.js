function getMeta(metaName) {
    const metas = document.getElementsByTagName('meta');

    for (let i = 0; i < metas.length; i++) {
        if (metas[i].getAttribute('name') === metaName) {
            return metas[i].getAttribute('content');
        }
    }

    return false;
}

function postRequest(url, object = null, async = true) {
    let Request = new XMLHttpRequest();
    Request.open("POST", url, async);
    Request.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
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

function showErrors(response) {
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
            '<button type="button" class="close" onclick="closeModal(\'error-modal\')">×</button>' +
            '</div>' +
            '<div class="modal-body">' +
            '<ul class="error-list">' +
            errors +
            '</ul>' +
            '</div>' +
            '<div class="modal-footer text-align-center">' +
            '<button class="btn error" type="button" onclick="closeModal(\'error-modal\')">ОК</button>' +
            '</div>' +
            '</div>' +
            '</div>'
        ;

        let before = document.getElementById('modal') ?? document.getElementById('app');
        document.body.insertBefore(modal, before);

        return true;
    }

    alert('Произошла ошибка, повторите операцию');
    console.log('response.errors is FALSE');
    return false;

}