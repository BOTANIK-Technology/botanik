let timeBtn = document.getElementById('time-confirm');
if (timeBtn) {

    let checked = getCookie('checked-'+timeBtn.dataset.id);
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
        if (!Object.keys(cookies).length == 0) {
            setCookie('timetable-'+timeBtn.dataset.id, JSON.stringify(cookies), {'path':COOKIE_URL});
            setCookie('checked-'+timeBtn.dataset.id, JSON.stringify(checked), {'path':COOKIE_URL});
        } else {
            deleteCookie('timetable-'+timeBtn.dataset.id, COOKIE_URL);
            deleteCookie('checked-'+timeBtn.dataset.id, COOKIE_URL);
        }
        closeModal();
    });

}