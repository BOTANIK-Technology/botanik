function getCookie(name) {
    let storData;
    if(!localStorage.data ){
        storData = {};
        localStorage.data = JSON.stringify(storData);
    }
    storData = JSON.parse(localStorage.data);

    if (!(name in storData)) {
        storData[name] = {};
        localStorage.data = JSON.stringify(storData);
    }

    return storData[name];

}

function setCookie(name, value, options = {}) {
    if(! localStorage.data){
        localStorage.data = JSON.stringify({});
    }
    let data = JSON.parse(localStorage.data);

    data[name] = value;
    localStorage.data = JSON.stringify(data);
    return data;


}

function deleteCookie(name, path = '/') {
    if(localStorage.data) {
        let data = JSON.parse(localStorage.data);
        delete data[name];
        localStorage.data = JSON.stringify(data);
    }
}

function resetAll() {
    localStorage.data = JSON.stringify({});
}
