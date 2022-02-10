function getCookie(name) {
    let storData;
    if(!localStorage.getItem('data') ){
        localStorage.setItem('data', JSON.stringify({}) );
    }
    storData = JSON.parse(localStorage.getItem('data'));

    if (!(name in storData)) {
        storData[name] = {};
        localStorage.setItem('data',  JSON.stringify(storData) );
    }

    return storData[name];

}

function setCookie(name, value, options = {}) {
    if(! localStorage.getItem('data')){
        localStorage.setItem('data', JSON.stringify({}) );
    }
    let data = JSON.parse(localStorage.getItem('data'));
    data[name] = value;
    localStorage.setItem('data', JSON.stringify(data) );
    return data;


}

function deleteCookie(name, path = '/') {
    let data = localStorage.getItem('data');
    if(data ) {
        data = JSON.parse(data);
        delete data[name];
        localStorage.setItem('data', JSON.stringify(data) );
    }
}

function resetAll() {
    localStorage.data = JSON.stringify({});
}
