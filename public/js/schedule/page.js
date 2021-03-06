function changeClass(object, id, className, remove) {
    object.addEventListener('click', function () {
        if (remove)
            removeClass(
                document.getElementById(id+this.dataset.id),
                className
            );
        else
            addClass(
                document.getElementById(id+this.dataset.id),
                className
            )
    })
}
function changeClassOnClick (objects, id, className, remove = false) {
    Object.keys(objects).forEach((k) => {
        changeClass(objects[k], id, className, remove)
    })
}

let moreBtn = document.getElementsByClassName('more-icon');
if (moreBtn.length) {
    let closeMore = document.getElementsByClassName('more-menu-close');
    changeClassOnClick(moreBtn, 'menu-', 'hide', true);
    changeClassOnClick(closeMore, 'menu-', 'hide');
}
