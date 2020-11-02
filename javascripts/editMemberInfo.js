//Created by Emelie Wallin

//submit forms
function submitForm(id) {
    $('#submit').css("display", "none");
    $('#loadingEdit').css("display", "block");
    //check validation
    var forms = $('.needs-validation');
    var valid = true;
    Array.prototype.filter.call(forms, function (form) {
        form.classList.add('was-validated');
        valid = form.checkValidity() && valid;
    });

    if (valid) {

        //collect form-data
        var firstname = new FormData(document.getElementById('fname-form'));
        var lastname = new FormData(document.getElementById('lname-form'));
        var email = new FormData(document.getElementById('email-form'));
        var phone = new FormData(document.getElementById('phone-form'));
        var title = new FormData(document.getElementById('title-form'));
        var avatar = new FormData(document.getElementById('avatar-form'));
        var bio = $("#bioEdit").val();
        console.log(bio);

        var areas = new Array();
        $.each($("input[name='area']:checked"), function () {
            areas.push(this.id);
        });
        
        //jsonfile
        var updateMember = {
            "id": id,
            "firstname": firstname.get('fname'),
            "lastname": lastname.get('lname'),
            "email": email.get('email'),
            "phone": phone.get('phone'),
            "title": title.get('title'),
            "areas": areas,
            "biography": bio
        }

        console.log(biography.get('bioEdit'));
        //send jsonfile to server
        $.ajax({
            url: './phpscripts/updateMember.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(updateMember),
            dataType: 'json',
            success: function (response) {

                var size = document.getElementById('avatar');
                //if everything goes well, send image to server
                if (response != '0') {

                    //if there is an image to send
                    if (size.files.length > 0) {
                        $.ajax({
                            url: './phpscripts/updateImage.php',
                            type: 'POST',
                            data: avatar,
                            contentType: false,
                            processData: false,
                            success: function (response) {
                                if (response != '0') {
                                    location.reload();
                                }
                                else {
                                    createAlertImageFail();
                                }
                            }
                        });
                    }
                    else {
                        location.reload();
                    }
                }
                else {
                    createAlertUploadFail();
                }
                $('#loadingEdit').css("display", "none");
                $('#submit').css("display", "block");
            }
        });
    }
}

//creating alerts functions
function createAlertImageFail() {
    var alertImageFail = "<div class='alert alert-danger alert-dissmissible fade role='alert' id='alertImageFail'><button type='button' class='close' data-dismiss='alert'>&times;</button>Image upload <strong>failed</strong></div>";
    $('#alertMessage').html($(alertImageFail).addClass('show'));
}

function createAlertUploadFail() {
    var updateImageFail = "<div class='alert alert-danger alert-dissmissible fade role='alert' id='alertUpdateFail'><button type='button' class='close' data-dismiss='alert'>&times;</button><strong>Failed</strong> on updating new info</div>";
    $('#alertMessage').html($(updateImageFail).addClass('show'));
}
