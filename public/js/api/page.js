function requestListener (objects = []) {
    objects.forEach((object) => {
        object.addEventListener('click', function () {
            let xhr = postRequest(this.dataset.url)
            xhr.onload = function() {
                if (xhr.status === 200) {
                    let response = JSON.parse(xhr.response)
                    if (response.errors)
                        showErrors(xhr.response)
                    else
                        Object.keys(response).forEach((k) => {
                            alert(k + ': ' + response[k])
                        })
                        console.log(response)
                }
                else
                    showErrors(xhr.response)
            }
        })
    });
}
