if (document.querySelector('.modal-edit') ) {
    setInitialData(id);
    //styles
    inputActive(document.getElementsByClassName('inp'));


    let delBtns = document.getElementsByName('delete-service');




   // function issetTimetable(id) {
   //      let timetable = getCookie('timetable-' + id);
   //      console.log(id, timetable);
   //      return !!(timetable && timetable !== 'undefined' && timetable.length);
   //  }
   //
   //  function getTimetable(id) {
   //      let timetable = getCookie('timetable-' + id);
   //      return timetable;
   //  }
   //
   //  function setValues(objects, values) {
   //      Object.keys(objects).forEach((k) => {
   //          if (values[k])
   //              objects[k].value = values[k];
   //      });
   //  }
   //
   //  function setChecked(objects, value) {
   //      Object.keys(objects).forEach((el) => {
   //          if (objects[el].value == value) objects[el].checked = 'checked';
   //      });
   //  }




    /**
     * Delete service
     */
    Object.keys(delBtns).forEach((del) => {
        delBtns[del].addEventListener('click', function () {
            let slug = document.getElementById('edit_type_slug').value;
            let send = {'id': delBtns[del].dataset.service};

            let Request = postRequest(CURRENT_URL + '/remove-service', send);
            Request.onload = function () {
                if (Request.status >= 200 && Request.status < 400) {
                    window.location.href = "/" + slug + "/services";
                } else {
                    showErrors(Request.response)
                }
            };
        })
    });




    let btnType = document.getElementById('edit-type');
    if (btnType) {
        btnType.addEventListener('click', function () {
            let slug = document.getElementById('edit_type_slug').value;
            let name = document.getElementById('edit_type_name').value;
            let id = document.getElementById('edit_type_id').value;
            let Request = postRequest(CURRENT_URL + '/save', {id: id, name: name});
            Request.onload = function () {
                if (Request.status >= 200 && Request.status < 400) {
                    window.location.href = "/" + slug + "/services?view=types";
                } else {
                    showErrors(Request.response)
                }
            };
        });
    }

    let btnAddr = document.getElementById('edit-addr');
    if (btnAddr) {
        btnAddr.addEventListener('click', function () {
            let slug = document.getElementById('edit_addr_slug').value;
            let name = document.getElementById('edit_addr_name').value;
            let id = document.getElementById('edit_addr_id').value;
            let Request = postRequest(CURRENT_URL + '/save', {id: id, name: name});
            Request.onload = function () {
                if (Request.status >= 200 && Request.status < 400) {
                    window.location.href = "/" + slug + "/services?view=addresses";
                } else {
                    showErrors(Request.response)
                }
            };
        });
    }

}
