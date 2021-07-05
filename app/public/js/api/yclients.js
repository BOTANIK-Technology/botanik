let yclientsBtn = document.querySelector('#yclients_update')
let yclientsSend = document.querySelector('#yclients_sendClients')
let yclientsGet = document.querySelector('#yclients_getClients')

yclientsBtn.addEventListener('click', function () {
    return;
    let token = document.querySelector('#yclients_token')
    let login = document.querySelector('#yclients_login')
    let password = document.querySelector('#yclients_password')
    let company_id = document.querySelector('#yclients_company_id')

    let send = {
        'params': {
            'company_id': company_id.value ?? null,
            'partner_token': token.value ?? null,
            'login': login.value ?? null,
            'password': password.value ?? null,
        }
    }
    let xhr = putRequest(this.dataset.url, send)
    xhr.onload = function() {
        if (xhr.status === 200) {
            console.log(xhr.response)
            alert('Сохранено.')
        }
        else
            showErrors(xhr.response)
    }
})

requestListener([yclientsSend, yclientsGet]);
