const PAGE = 'users';
const COOKIE_URL = '/'+SLUG+'/'+PAGE+'/';

function unsetCookies(idVal) {
    deleteCookie('inputs' + suffix(idVal));
    deleteCookie('timetable' + suffix(idVal));
}
