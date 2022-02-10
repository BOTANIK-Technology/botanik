// Привязка события к конкретному элементу
function bindEvent(element, type, handler) {
    if (element.addEventListener) {
        element.addEventListener(type, handler, false);
    } else {
        element.attachEvent('on' + type, handler);
    }
}

// Привязка события к выборке элементов
function bindEventAll(NodeList, type, handler) {
    for (let i = 0; i < NodeList.length; ++i) {
        let element = NodeList[i];
        bindEvent(element, type, handler);
    }
}

function isArray(obj) {
    return Object.prototype.toString.call(obj) === '[object Array]';
}

function isObject(obj) {
    return obj === Object(obj);
}

const suffix = (idVal = null) => {
    if (idVal !== null) {
        return '-' + idVal;
    }
    return '';
}


function getValues(array) {
    let returned = [];
    array.forEach((select) => {

        Object.keys(select).forEach((value) => {
            returned.push(select[value].value)
        });
    });
    return returned;
}

function setValues(selects, values) {
    let j = 0;
    selects.forEach((select) => {
        Object.keys(select).forEach((k) => {
            select[k].value = values[j];
            j++;
        });
    });
}
