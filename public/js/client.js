let deleteBtn = document.getElementById('delete-client');
let blockBtn = document.getElementById('block-client');
let inputNames = ['last-name-', 'first-name-', 'middle-name-', 'username-', 'phone-', 'email-', 'age-', 'sex-', 'bonus-'];
let clients = document.getElementsByClassName('client');
let current = [];
let changed = false;
let change = function () {
    changed = true;
};

inputActive(document.getElementById('search'));

function sendUpdate(id) {

    inputNames.forEach((el) => {
        document.getElementById(el+id).addEventListener('change', change)
    });

    document.getElementById('update-'+id).addEventListener('click', function () {
        if (changed) {
            let send = {
                'first_name'  : document.getElementById('first-name-'+id).value,
                'last_name'   : document.getElementById('last-name-'+id).value,
                'middle_name' : document.getElementById('middle-name-'+id).value,
                'username'    : document.getElementById('username-'+id).value,
                'email'       : document.getElementById('email-'+id).value,
                'phone'       : document.getElementById('phone-'+id).value,
                'age'         : document.getElementById('age-'+id).value,
                'sex'         : document.getElementById('sex-'+id).value,
                'bonus'       : document.getElementById('bonus-'+id).value,
            };

            let Request = postRequest(this.dataset.href, send);
            Request.onload = function() {
                if (Request.status >= 200 && Request.status < 400) {
                    unselect(id);
                } else {
                    console.log(Request.response);
                    showErrors(Request.response);
                }
            };
        } else {
            unselect(id);
        }
    })
}

function unselect (userId = false) {
    if (userId === false) {
        if (current.length) {
            current.forEach((id) => {
                let element = document.getElementById('client-menu-'+id);
                if (element) {
                    inputNames.forEach((input) => {
                        let attr = document.getElementById(input+id);
                        attr.setAttribute("disabled", "disabled");
                        attr.removeEventListener('change', change);
                    });
                    document.getElementById(id).classList.remove('active');
                    element.remove();
                }
            });
            current = [];
        }
    } else {
        inputNames.forEach((input) => {
            let attr = document.getElementById(input+userId);
            attr.setAttribute("disabled", "disabled");
            attr.removeEventListener('change', change);
        });
        document.getElementById('' + userId).classList.remove('active');
        document.getElementById('client-menu-'+userId).remove();
    }
    changed = false;
}


if (clients.length) {
    Object.keys(clients).forEach((k) => {
        clients[k].addEventListener('dblclick', function () {
            unselect();
            if (current.indexOf(clients[k].id) === -1) {
                current.push(clients[k].id);
                inputNames.forEach((input) => {
                    let attr = document.getElementById(input + clients[k].id);
                    attr.removeAttribute("disabled");
                });
                clients[k].classList.add('active');
                clients[k].insertAdjacentHTML(
                    'beforeEnd',
                    '<div id="client-menu-' + clients[k].id + '" class="left-5px flex align-items-center justify-content-between">' +
                    '<a class="icon-size chart-icon pointer" href="' + url + '/statistic/' + clients[k].id + urlParams + '"></a>' +
                    '<a class="icon-size history-icon pointer" href="' + url + '/history/' + clients[k].id + urlParams + '"></a>' +
                    '<a class="icon-size lock-icon pointer" href="' + url + '/block/' + clients[k].id + urlParams + '"></a>' +
                    '<i class="icon-size check-icon pointer" id="update-' + clients[k].id + '" data-href="' + url + '/edit/' + clients[k].id + '"></i>' +
                    '<a class="icon-size trash-icon pointer" href="' + url + '/delete/' + clients[k].id + urlParams + '"></a>' +
                    '</div>'
                );
                sendUpdate(clients[k].id);
            }
        })
    });
}

document.getElementById('titles').addEventListener('click', function () {
    unselect();
});

if (deleteBtn) {
    deleteBtn.addEventListener('click', function () {
        let send = {'id': deleteBtn.dataset.id};

        let Request = postRequest(url, send);
        Request.onload = function() {
            if (Request.status >= 200 && Request.status < 400) {
                closeModal();
            } else {
                showErrors(Request.response);
            }
        };
    })
}

if (blockBtn) {
    blockBtn.addEventListener('click', function () {
        let send = {'id': blockBtn.dataset.id};

        let Request = postRequest(url, send);
        Request.onload = function() {
            if (Request.status >= 200 && Request.status < 400) {
                closeModal();
            } else {
                showErrors(Request.response);
            }
        };
    })
}
