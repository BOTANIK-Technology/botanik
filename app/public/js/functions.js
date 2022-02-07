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
