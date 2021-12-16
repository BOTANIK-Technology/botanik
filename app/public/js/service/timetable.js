let timeBtn = document.getElementById('time-confirm');
if (timeBtn) {

    let checked = getCookie('checked-'+id);
    if (checked) {
        checked = JSON.parse(checked);
        checked.forEach((el) => {
            let item = document.getElementById(el);
            if (item) item.classList.add('checked')
        });
    }

    timeBtn.addEventListener('click', function () {
        let cookies = new Object();
        let checked = [];
        let times = document.getElementsByClassName('checked');
        Object.keys(times).forEach((el) => {
            if (!cookies[times[el].dataset.day])
                cookies[times[el].dataset.day] = [times[el].dataset.time];
            else
                cookies[times[el].dataset.day].push(times[el].dataset.time);
            checked.push(times[el].id);
        });
        console.log(cookies);

        if (!Object.keys(cookies).length == 0) {
            if(id !== '') {
                setCookie('timetable-'+id, JSON.stringify(cookies), {'path':COOKIE_URL});
                setCookie('checked-'+id, JSON.stringify(checked), {'path':COOKIE_URL});
            } else {
                setCookie('timetable', JSON.stringify(cookies), {'path':COOKIE_URL});
                setCookie('checked', JSON.stringify(checked), {'path':COOKIE_URL});
            }
        } else {
            if(id !== '') {
                deleteCookie('timetable-'+id, COOKIE_URL);
                deleteCookie('checked-'+id, COOKIE_URL);
            } else {
                deleteCookie('timetable', COOKIE_URL);
                deleteCookie('checked', COOKIE_URL);
            }
        }

        closeModal();
    });

}
