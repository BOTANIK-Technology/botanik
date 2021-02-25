let confBtn = document.getElementById('confirm');
if (confBtn) {
    confBtn.addEventListener('click', function () {
        let Request = postRequest(confBtn.dataset.src, {id: confBtn.dataset.id});
        Request.onload = function() {
            if (Request.status >= 200 && Request.status < 400)
                closeModal();
            else
                alert('Произошла ошибка, повторите операцию');
        }
    });
}