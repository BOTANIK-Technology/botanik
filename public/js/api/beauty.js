let beautyBtn = document.querySelector('#beauty_update')

beautyBtn.addEventListener('click', function () {
    let appId = document.querySelector('#beauty_application_id')
    let dbCode = document.querySelector('#beauty_database_code')
    let appSecret = document.querySelector('#beauty_application_secret')

    let send = {
        'params': {
            'application_id': appId.value ?? null,
            'database_code': dbCode.value ?? null,
            'application_secret': appSecret.value ?? null
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
