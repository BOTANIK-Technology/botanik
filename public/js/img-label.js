let img = document.getElementById('img');
let imgIcon = document.getElementById('img-label');
let img_src = '';

if (img) {

    function getImage () {
        if (img_src !== '') return img_src;
        return img.files[img.files.length - 1] ?? false;
    }

    img.addEventListener('change', function () {
        let len = this.files.length;
        let span = document.getElementById('file-name');
        if (len) {
            if (span)
                span.remove();
            addClass(imgIcon, 'active');
            imgIcon.insertAdjacentHTML(
                'beforeEnd',
                '<span class="file-name" id="file-name">' +
                this.files[len - 1].name +
                '</span>'
            );
        }
        else {
            removeClass(imgIcon, 'active');
        }
    })

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

    function sendEvent (event) {
        let url = this.dataset.url;
        if (getImage()) {

            if (img_src !== '')
                sendForm (url, img_src);

            else {
                let formData = new FormData();
                formData.append('image', getImage());
                formData.append('path', slug+'-images');
                let request = new XMLHttpRequest();
                request.open('POST', this.dataset.storage);
                request.send(formData);
                request.onload = function() {

                    if (request.status >= 200 && request.status < 400) {

                        img_src = JSON.parse(request.response).path;
                        sendForm (url, img_src);

                    } else {
                        showErrors(request.response);
                    }

                };
            }

        } else {
            sendForm (url);
        }
    }
}
