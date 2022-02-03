let mouseDown = false;
document.body.addEventListener('mousedown', function() {
    mouseDown = true;
});
document.body.addEventListener('mouseup', function() {
    mouseDown = false;
});

let month = document.getElementById('month_picker');
let year = document.getElementById('year_picker');
month.addEventListener('change', () => {
    window.location.replace(CURRENT_URL + '?current_month=' + month.value + '&current_year=' + year.value);
});
year.addEventListener('change', () => {
    window.location.replace(CURRENT_URL + '?current_month=' + month.value + '&current_year=' + year.value);
});



// Привязка события к конкретному элементу
function bindEvent(element, type, handler) {
    if(element.addEventListener) {
        element.addEventListener(type, handler, false);
    } else {
        element.attachEvent('on'+type, handler);
    }
}

// Привязка события к выборке элементов
function bindEventAll(NodeList, type, handler) {
    for (let i = 0; i < NodeList.length; ++i) {
        let element = NodeList[i];
        bindEvent(element, type, handler);
    }
}

// let monthElements = document.querySelectorAll('#month_picker option');
// bindEventAll(monthElements, 'click', function (){
//     let currentLocation = window.location;
// console.log(currentLocation);
// });


let checkboxes = document.getElementsByClassName('checkbox');

Object.keys(checkboxes).forEach((el) => {

    checkboxes[el].addEventListener('mousedown', function () {
        checkboxes[el].classList.toggle('checked');
    });
    checkboxes[el].addEventListener('mouseover', function () {
        if (mouseDown) checkboxes[el].classList.toggle('checked');
    });
});

let all = document.querySelector('#select-all');
all.addEventListener('click', function () {
    Object.keys(checkboxes).forEach((el) => {
        addClass(checkboxes[el], 'checked')
    });
})

let clr = document.querySelector('#clear-all');
clr.addEventListener('click', function () {
    Object.keys(checkboxes).forEach((el) => {
        removeClass(checkboxes[el], 'checked')
    });
})
