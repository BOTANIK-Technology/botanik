inputActive(document.getElementsByClassName('inp'));

let img = document.getElementById('img');
let imgIcon = document.getElementById('img-label');

if (img) {
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
}