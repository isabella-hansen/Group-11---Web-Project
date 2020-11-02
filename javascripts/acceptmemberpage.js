//Created by Emelie Wallin
var id = "null";

//onclick event on list-objects
$(document).on('click', '#newMembers li', function () {
    id = this.id;
    $.ajax({
        url: "views/_adminViewNewMember.php?id=" + id + "&table=new_member",
        success: function (result) {
        $("#newMemberInfo").html(result);
        }
    })
});


//creating alerts functions
function createAlertDecline() {
    var decline = "<div class='alert alert-info alert-dissmissible fade' role='alert' id='alertDecline'><button type='button' class='close' data-dismiss='alert'>&times</button>Application has been <strong>declined</strong></div>";
    $('#alertMessage').html($(decline).addClass('show'));
}

function createAlertAccept() {
    var accept = "<div class='alert alert-info alert-dissmissible fade' role='alert' id='alertAccept'><button type='button' class='close' data-dismiss='alert'>&times</button>Application has been <strong>accepted</strong></div>";
    $('#alertMessage').html($(accept).addClass('show'));
}


//onclick event for decline membership
$('#decline').on('click', function () {
    $('#decline').css("display", "none");
    $('#loading2').css("display", "block");

    if (id != "null") {
        $.ajax({
            url: 'phpscripts/declineMail.php?id=' + id,
            success: function () {
                $('#loading2').css("display", "none");
                $('#decline').css("display", "block");
                remove();
                createAlertDecline();
            }
        })
    }
});

//onclick event for accepting membership
$('#accept').on('click', function () {
    $('#accept').css("display", "none");
    $('#loading').css("display", "block");

    if (id != "null") {
        $.ajax({
            url: 'phpscripts/uploadNewMember.php?id=' + id,
            success: function () {
                $('#loading').css("display", "none");
                $('#accept').css("display", "block");
                remove();
                createAlertAccept();
            }
        })
    }
});

//remove application from new-member-list, update list and clear member-preeview
function remove() {
    if (id != "null") {
        $.ajax({
            url: 'phpscripts/removeFromNewMember.php?id=' + id,
            success: function () {
                $.ajax({
                    url: 'phpscripts/loadNewMembers.php',
                    success: function (result) {
                        $('#newMembers').html(result);
                        $('#fname').val('');
                        $('#lname').val('');
                        $('#email').val('');
                        $('#phone').val('');
                        $('#title').val('');
                        $('#area').val('');
                        $('#biography').val('');
                        $('#img').attr('src', 'uploads/default.jpg');
                    }
                })
            }
        })
    }
    id = "null";
}

