//Created by Mariia Nema
//Shows how many chars are left from max possible value
function showCharsLeft(textarea, responsearea, maxlength) {
    var response = document.getElementById(responsearea);
    if (parseInt(response.innerText) <= maxlength) {
        response.innerText = parseInt(maxlength - textarea.value.length);
    }
}