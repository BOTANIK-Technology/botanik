window.onload = function() {
    document.body.innerHTML = document.body.innerHTML.replace(/\u2028/g, '');
}

function design() {
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
        'age_start': document.getElementById('age_start').value,
        'age_end': document.getElementById('age_end').value,
        'sex': document.getElementById('sex').value,
        'frequency': document.getElementById('frequency').value,
        'button': document.getElementById('button').value,
        'last_service': document.getElementById('last_service').value,
        'favorite_service': document.getElementById('favorite_service').value,
    };
}

document.addEventListener('DOMContentLoaded', function(){
    let createBtn = document.getElementById('createMail');
    if (createBtn) {
        design();
        createBtn.addEventListener('click', sendEvent);
    }

    let closeBtn = document.getElementById('modal-close-btn');
    closeBtn.addEventListener('click', function(e) {
        closeModal(e);
    });
});
