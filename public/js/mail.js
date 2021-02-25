function design() {
    let x = document.getElementById('best-design');
    document.getElementById('button').addEventListener('change', function () {
        if (this.value.length > 0) addClass(x, 'active');
        else removeClass(x, 'active');
    });
}

let createBtn = document.getElementById('createMail');
if (createBtn) {

    design();

    createBtn.addEventListener('click', function () {
        let send = {
            'title': document.getElementById('title').value,
            'text': document.getElementById('text').value,
            'age_start': document.getElementById('age_start').value,
            'age_end': document.getElementById('age_end').value,
            'sex': document.getElementById('sex').value,
            'frequency': document.getElementById('frequency').value,
            'img': document.getElementById('img').value,
            'button': document.getElementById('button').value,
            'last_service': document.getElementById('last_service').value,
            'favorite_service': document.getElementById('favorite_service').value,
        };
        let Request = postRequest(url + '/confirm', send);
        Request.onload = function() {
            if (Request.status >= 200 && Request.status < 400) {
                closeModal();
            } else {
                showErrors(Request.response);
            }
        };
    });
}