function design() {
    inputActive([document.getElementById('title'), document.getElementById('text'), document.getElementById('img'), document.getElementById('button')]);
    let x = document.getElementById('best-design');
    document.getElementById('button').addEventListener('change', function () {
        if (this.value.length > 0) addClass(x, 'active');
        else removeClass(x, 'active');
    });
}

function getValues () {
    return {
        'title': document.getElementById('title').value,
        'text': document.getElementById('text').value,
        'button': document.getElementById('button').value,
        'user_id': document.getElementById('user_id').value
    };
}

let createBtn = document.getElementById('createShare');
if (createBtn) {
    design();
    createBtn.addEventListener('click', sendEvent);
}

let delBtn = document.getElementById('delete');
if (delBtn)
    delBtn.addEventListener('click', function () {
        sendForm(this.dataset.url, false, false);
    });

let editBtn = document.getElementById('editShare');
if (editBtn) {
    design();
    editBtn.addEventListener('click', sendEvent);
}
