let password = document.getElementById('password');
let eleye = document.getElementById('eye');
let bool = false;

function line(obj) {
    if (obj.value)
        return addClass(obj, 'line');
    else
        return removeClass(obj, 'line');
}
function eye(obj) {
    if (!obj.value)
        return addClass(eleye, 'hide');
    else
        return removeClass(eleye, 'hide');
}

eleye.addEventListener('click', function () {
    eleye.classList.toggle('open');
    if  (password.getAttribute('type') === 'password') password.setAttribute('type', 'text');
    else password.setAttribute('type', 'password');
});