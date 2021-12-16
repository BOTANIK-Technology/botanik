let modal = document.getElementsByClassName('modal');
let close_modal = document.getElementsByClassName('close');
let app = document.getElementById('app');

function closeError(id = 'error-modal') {
    document.getElementById(id).remove();
}
function closeModal(event, id = 'modal', href = false) {
    removeClass(app, 'bg-blur');
    let refresh = document.getElementById('refresh-'+id);
    if (refresh) {
        if (href !== false)
            refresh.href = href;
        refresh.click();
    } else {
        document.getElementById(id).remove();
    }

}
if (close_modal.length > 0) {
    Object.keys(close_modal).forEach((k) => {
        close_modal[k].addEventListener('click', closeModal)
    });
}
function addClass (obj, add) {
    if (obj.classList) {
        obj.classList.forEach((cl) => {
            if (cl === add) return false;
        });
        obj.classList.add(add);
        return true;
    }
    return false;
}
function removeClass (obj, remove) {
    if (obj.classList) {
        obj.classList.forEach((cl) => {
            if (cl === remove) {
                obj.classList.remove(remove);
                return true;
            }
        });
    }
    return false;
}

function isArray(obj) {
    return Object.prototype.toString.call(obj) === '[object Array]';
}

function isObject(obj) {
    return obj === Object(obj);
}

function inputActive(input) {
    if (isArray(input))
        input.forEach((obj) => {
            inputActive(obj);
        });
    else if (isObject(input) && input.length > 1) {
        Object.keys(input).forEach((k) => {
            inputActive(input[k]);
        });
    }
    else {
        input.addEventListener('change', function () {
            if (this.value.length > 0) addClass(this, 'active');
            else removeClass(this, 'active');
        });
    }
}

function selectActive() {
    let selects = document.getElementsByTagName('select');
    Object.keys(selects).forEach((el) => {
        selects[el].addEventListener('focus', function () {
            addClass(selects[el],'active');
        });
        selects[el].addEventListener('blur', function () {
            removeClass(selects[el],'active');
        });
    });
}

if(modal.length)addClass(app, 'bg-blur');
else removeClass(app, 'bg-blur');

if(document.getElementById('modal-empty'))removeClass(app, 'bg-blur');


let loadMore = document.querySelector('.load-block');
if (loadMore !== null) {

    function scrollToElement(theElement) {
        let selectedPosX = 0;
        let selectedPosY = 0;
        while (theElement != null) {
            selectedPosX += theElement.offsetLeft;
            selectedPosY += theElement.offsetTop;
            theElement = theElement.offsetParent;
        }
        window.scrollTo(selectedPosX,selectedPosY);
    }

    document.addEventListener('DOMContentLoaded', function () {
        scrollToElement(loadMore);
    });
}
