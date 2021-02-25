let mouseDown = 0;
document.body.addEventListener('mousedown', function() {
    ++mouseDown;
});
document.body.addEventListener('mouseup', function() {
    --mouseDown;
});

let checkboxes = document.getElementsByClassName('checkbox');
Object.keys(checkboxes).forEach((el) => {
    checkboxes[el].addEventListener('mousedown', function () {
        checkboxes[el].classList.toggle('checked');
    });
    checkboxes[el].addEventListener('mouseover', function () {
        if (mouseDown) checkboxes[el].classList.toggle('checked');
    });
});