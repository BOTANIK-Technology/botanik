month.addEventListener('change', () => {
    window.location.replace(CURRENT_URL + '?service_id=' + id + '&current_month=' + month.value + '&current_year=' + year.value);
});
year.addEventListener('change', () => {
    window.location.replace(CURRENT_URL + '?service_id=' + id + '&current_month=' + month.value + '&current_year=' + year.value);
});
