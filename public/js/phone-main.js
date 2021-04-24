window.onload = function() {
    document.body.innerHTML = document.body.innerHTML.replace(/\u2028/g, '');
}

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

let menuBtn = document.getElementById('menu-open');
let closeMenu = document.getElementById('menu-close');
let menu = document.getElementById('menu-main');
let main = document.getElementById('phone-main');
let nav = document.getElementById('nav');
if (menuBtn && closeMenu) {
    menuBtn.addEventListener('click', function () {
        removeClass(menu, 'hide');
        addClass(main, 'hide');
        addClass(menuBtn, 'hide');
        addClass(nav, 'nav-white');
        removeClass(closeMenu, 'hide');
    });
    closeMenu.addEventListener('click', function () {
        removeClass(main, 'hide');
        removeClass(nav, 'nav-white');
        addClass(closeMenu, 'hide');
        addClass(menu, 'hide');
        removeClass(menuBtn, 'hide');
    });
}
