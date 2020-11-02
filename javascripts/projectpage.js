// Created by Mariia Nema
//Send an email to the projectLeader
function sendEmailtoLeader(email_address, project_title) {
    
    var email = {
        "email_to": email_address,
        "proj": project_title,
        "msg": document.getElementById('modalInput').value 
    };

    fetch('phpscripts/emailLEader.php', {
        method: 'POST',
        body: JSON.stringify(email),
        headers: {
            'Content-type': 'application/json',
            'Accept': 'application/json'
        }
    })
        .then(response => response.json())
        .then(json => {
            console.log(json);
            if (json == 'Success') {
                $('#modalSuccess').addClass('show');
                $('#ApplyButton').prop('disabled', true);
                $('#ApplyButton').hide();
            }
            else {
                $('#modalError').addClass('show');
            }
            $('#SendButton').prop('disabled', true);
        })
        .catch(error => {
            $('#modalError').addClass('show');
            $('#SendButton').prop('disabled', true);
        });
}

//Reset modal after closing
function closeModal() {
    $('#SendButton').prop('disabled', false);
    $('#ErrorButton').prop('disabled', false);
    $('#modalSuccess').removeClass('show');
    $('#modalError').removeClass('show');
}

//Show how many signs are left from maxlength of the field
function showCharsLeft (textarea, responsearea, maxlength)
{
    var response = document.getElementById(responsearea);
    console.log(textarea.value.length);
    if (parseInt(response.innerText) <= maxlength)
        response.innerText = parseInt(maxlength - textarea.value.length);
}

