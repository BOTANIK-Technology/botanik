let btn = document.getElementById('custom');
let inputs = [document.getElementById('end-date'), document.getElementById('start-date')];
inputActive(inputs);
inputs.forEach((inp) => {
    inp.addEventListener('keydown', function(event) {
        let curLen = inp.value.length;
        const key = event.key;
        if( key != "Backspace" && key != "Delete" ) {
            if (curLen > 9) {
                inp.value = inp.value.substr(0, 9);
            }
        }
    });
    inp.addEventListener('keyup', function(event) {
        let curLen = inp.value.length;
        const key = event.key;
        if( key != "Backspace" && key != "Delete" ) {

            if (curLen == 2)
                inp.value = inp.value + ".";

            if (curLen == 5)
                inp.value = inp.value + ".";

        }
    });
    inp.addEventListener('change', function() {
        let val1 = inputs[0].value.length;
        let val2 = inputs[1].value.length;
        if (
            (val1 != 10 && val1 != 0) ||
            (val2 != 10 && val2 != 0)
        ) {
            btn.setAttribute('disabled', 'disabled');
        }
        else {
            btn.removeAttribute('disabled');
        }
    });
});