let modal = document.getElementsByClassName('modal');
let close_modal = document.getElementsByClassName('close');
let app = document.getElementById('app');

function closeError(id = 'error-modal') {
    document.getElementById(id).remove();
}
function closeModal(event, id = 'modal', href = false) {
    app.classList.remove('bg-blur');
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
            if (this.value.length > 0) {
                this.classList.add('active');
            }
            else this.classList.remove(this, 'active');
        });
    }
}

function selectActive() {
    let selects = document.getElementsByTagName('select');
    Object.keys(selects).forEach((el) => {
        selects[el].addEventListener('focus', function () {
            selects[el].classlist.add('active');
        });
        selects[el].addEventListener('blur', function () {
            selects[el].classlist.remove('active');
        });
    });
}

if(modal.length){
    app.classList.add('bg-blur');
}
else {
    app.classList.remove('bg-blur');
}

if(document.getElementById('modal-empty')){
    app.classList.remove('bg-blur');
}


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
