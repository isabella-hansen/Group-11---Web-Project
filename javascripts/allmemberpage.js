//Created by Emelie Wallin
var titleSelect = "All titles";

//ajax-search function
function search() {
    var input = document.getElementById('search-input').value;

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("rows").innerHTML = this.responseText;
        }
    }

    xmlhttp.open("GET", "views/_searchMembers.php?q=" + input + "&title=" + titleSelect, true);
    xmlhttp.send();
}


//get value from selected title and do a search on that title
$("select").change(function () {
    $("select option:selected").each(function () {
        titleSelect = $(this).text();
    });
    search();
})


//onclick event on table-row ( not head)
$(document).on('click', '#memberTable tr', function () {
    var id = this.id;
    if (id != 'head') {
        if ($('#memberinfo').is(':visible')) { //check if memberinfo is visible (depends on screen size)
            $.ajax({
                url: "views/_memberview.php?id=" + id,
                success: function (result) {
                    $("#memberinfo").html(result);
                }
            });
        }

        else { //else go to new window
            window.location.replace("./memberinfo.php?id=" + id);
        }
    }
});



//sort table on firstName or lastName
function test(input) {
    var table = document.getElementById("memberTable");
    var loop = true;
    var row1Element, row2Element;

    while (loop) {

        loop = false;
        var rows = table.rows;

        for (i = 1; i < (rows.length - 1); i++) {
            
            row1Element = rows[i].getElementsByTagName("TD")[input];
            row2Element = rows[i + 1].getElementsByTagName("TD")[input];

            var result = row1Element.innerHTML.localeCompare(row2Element.innerHTML);
         
            if (result > 0) { //if items is in the wrong order, swap place
                var temp = rows[i].innerHTML;
                rows[i].innerHTML = rows[i + 1].innerHTML;
                rows[i + 1].innerHTML = temp;

                var tempID = rows[i].getAttribute('id');
                rows[i].setAttribute('id', rows[i + 1].getAttribute('id'));
                rows[i + 1].setAttribute('id', tempID);
          
               loop = true;
               break;
            }
        }       
    }    
}