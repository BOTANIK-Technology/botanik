const PAGE = 'users';
const COOKIE_URL = '/'+SLUG+'/'+PAGE+'/';

function unsetCookies(idVal) {
    deleteCookie('inputs');
    deleteCookie('timetable');
}
