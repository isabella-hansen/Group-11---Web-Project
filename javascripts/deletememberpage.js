//Created by Emelie Wallin
var input = "null";

//ajax search function
function searchDel(input) {
    if (input == "") {
        document.getElementById('deleteMembers').innerHTML = "<lable><em>No search objects...</em></lable>";
    }

    else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("deleteMembers").innerHTML = this.responseText;
            }
        }

        xmlhttp.open("GET", "views/_searchDeleteMembers.php?q=" + input, true);
        xmlhttp.send();
    }
}


var id = "null";

//onclick event on list-object that will update memberview
$(document).on('click', '#deleteMembers li', function () {
    id = this.id;
    $.ajax({
        url: "views/_adminViewNewMember.php?id=" + id + "&table=deleteMember",
        success: function (result) {
            $("#memberInfo").html(result);
        }
    })
});

//creating alerts functions
function createAlertDone() {
    var done = "<div class='alert alert-info alert-dissmissible fade' role='alert'><button type='button' class='close' data-dismiss='alert'>&times;</button>Member has successfully been <strong>removed</strong></div>";
    $('#alertMessage').html($(done).addClass('show'));
}
function createAlertError() {
    var done = "<div class='alert alert-danger alert-dissmissible fade' role='alert'><button type='button' class='close' data-dismiss='alert'>&times;</button><strong>Error!</strong> Could not remove member. Check if member is a leader</div>";
    $('#alertMessage').html($(done).addClass('show'));
}


//delete member funtion 
function deleteMember() {
    $('#delete').css("display", "none");
    $('#loadingDel').css("display", "block");

    if (id != "null") {
        $.ajax({
            url: '/phpscripts/deleteMember.php?id=' + id,
            success: function (result) {
                if (result == '1') {
                    createAlertDone();
                }

                else { // if member is leader on a project, it cannot be removed until someone else takes over his/her place
                    createAlertError();
                }
                //clear member view
                searchDel(input);
                $('#fname').val('');
                $('#lname').val('');
                $('#email').val('');
                $('#phone').val('');
                $('#title').val('');
                $('#area').val('');
                $('#biography').val('');
                $('#img').attr('src', 'uploads/default.jpg');
                id = "null";

                $('#loadingDel').css("display", "none");
                $('#delete').css("display", "block");
            }
        });
    }
}


