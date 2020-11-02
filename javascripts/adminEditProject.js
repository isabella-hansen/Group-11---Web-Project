//Created by Mariia Nema
var editor_full_description;
var editor_result;
$(function () { 
    //Daterangepicker config
    $('#date_end').daterangepicker({
        opens: 'right',
        singleDatePicker: true,
        drops: 'down',
        showDropdowns: true,
        autoUpdateInput: false,
        timePicker: false,
        singleDate: true,
        maxDate: moment(),
        locale: {
            format: 'YYYY-MM-DD',
            cancelLabel: 'Clear'
        },
    })

    $('.date-picker').val('');

    $('.date-picker').on('apply.daterangepicker', function (e,picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
     
    });
    $('.date-picker').on('cancel.daterangepicker', function (e,picker) {
        $(this).val('');       
    });

    //Editor config
    ClassicEditor
        .create(document.querySelector('#full_description'))
        .then(editor => {
            editor_full_description = editor;
            console.log(editor);
        })
        .catch(error => {           
        });
    ClassicEditor
        .create(document.querySelector('#result'))
        .then(editor => {
            editor_result = editor;
            console.log(editor);
        })
        .catch(error => {
        });

    //Searchbar config
    $("#search-input").bind("keyup change", function () {
        populateProjectsDropdown();
    });    
})

function populateProjectsDropdown() {
    //hide button
    document.getElementById('delete_button').style.display = "none";

    if ($('#search-input').val().length == 0) {    
        $('#projects_found').css("display", "none");
    }

    if ($('#search-input').val().length >= 2) {
        query = $('#search-input').val();
        fetch('/phpscripts/adminfindproject.php', {
            method: 'POST',
            body: JSON.stringify(query),
            headers: {
                'Content-type': 'application/json',
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(json => {
                console.log(json);
                var html = '';      
                $('#projects_found').empty();
                json.forEach(proj => {                    
                    html += '<a href="#" class="list-group-item list-group-item-action" onclick="getProjectData( ' + proj['id'] + '); return false; " value=" ' + proj['id'] + '">' + proj['title'] + '</a>';                      
                    $('#projects_found').html(html);                                                         
                });
                $('#projects_found').css("display", "block"); 
            })
    }
}

function getProjectData(project_id) {    
    $('#projects_found').focus();
    $('#projects_found').css("display", "none");
    
    fetch('/phpscripts/adminGetProject.php?id=' + project_id)
        .then(response => response.json())
        .then(proj => {
            console.log(proj);
            fillinforms(proj);
        });        
}

function fillinforms(proj) {
    //refresh forms
    refreshforms();

    //fill in details
    $('#title').val(proj['project']['title']);    
    $('#leader').val(proj['project']['leader']);
    $('#date_end').val(proj['project']['date_end']);
    $('#short_description').val(proj['project']['short_description']);
    editor_full_description.setData(proj['project']['full_description'] == null ? '': proj['project']['full_description']);
    editor_result.setData(proj['project']['result'] == null ? '' : proj['project']['result']);

    //fill in areas checkboxes
    if (proj['project']['areas_array'] !== null) {
        var areas = proj['project']['areas_array'].split(', ');
        console.log(areas);
       
        for (var i in areas) {
            $('.checkbox-item-area').each(function () {
                if (areas[i] == this.name)
                    this.checked=true;                 
            });
        }
    }

    //fill in members
    var members = proj['members'];   
    for (var i in members) {
        appendParticipantTolist(members[i]['member_id'], members[i]['member_fname'] + ' ' + members[i]['member_lname']);          
    }
    //show buttons
    var delete_btn = document.getElementById('delete_button');
    delete_btn.onclick = function () { DeleteDialog(proj['project']['id'], proj['project']['title']) };
    delete_btn.style.display = "block";
    var update_btn = document.getElementById('update_button');
    update_btn.onclick = function () { Submit(proj['project']['id'], proj['project']['title']) }
    update_btn.style.display = "block";
}

function addParticipant(element) {
    var member_id = element.value;
    var member_name = element.options[element.selectedIndex].text;
    var list = document.querySelectorAll('#participants div');   
    var is_in_list = false;
    for (var i = 0; i < list.length; i++) {
        if (list[i].value == member_id) {
            is_in_list = true;
        }            
    }
    if (!is_in_list) {
        appendParticipantTolist(member_id, member_name);
    }          
}

function appendParticipantTolist(id, name) {
    var list_item = document.createElement('div');
    list_item.className = 'list-group-item list-group-item-action p-1';
    list_item.innerText = name;
    list_item.value = id;

    var deletebtn = document.createElement('button');
    deletebtn.className = 'btn btn-info float-right rounded-4';
    deletebtn.onclick = function () { removeParticipantFromList(id) };
    var delete_icon = document.createElement('i');
    delete_icon.className = 'fa fa-times';
    deletebtn.appendChild(delete_icon);
    list_item.insertAdjacentElement('beforeend', deletebtn);
    document.getElementById('participants').appendChild(list_item);
}
function removeParticipantFromList(id) {
    var list = document.querySelectorAll('#participants div');
    for (var i in list) {
        if (list[i].value === id) {
            document.getElementById('participants') .removeChild(list[i]);
        }
    }      
}

function refreshforms() {
    //refresh details
    $('#title').val('');
    $('#leader').val('');
    $('#date_end').val('');
    $('#short_description').val('');
    editor_full_description.setData('');
    editor_result.setData('');

    //refresh in areas checkboxes
    $('.checkbox-item-area').each(function () {
        if (this.checked == true)
            this.checked = false;
    });    

    //refresh members
    document.getElementById('participants').innerHTML = '';

    //take away delete-btn and update_button
    document.getElementById('delete_button').style.display = "none";
    document.getElementById('update_button').style.display = "none";

    //refresh validatestate
    $('.date-picker').val('');

    $('.needs-validation').each(function () {
        this.classList.remove('was-validated');
    });   
}
function closeAlert() {
    $('#alert').removeClass('show');
}

function DeleteDialog(id, title) {
    $('#FeedbackModal').modal('show');
    $('#operation').click(function () { deleteProject(id, title) });   
    $('#deletemsg').text("Are you sure you want to delete '" + title + "'?");
    $('#FeedbackModal').modal('show');
}

function SuccessDeleteAlert(title) {
    $('#modalAlert').text("'" + title + "' is successfully deleted.");
    document.getElementById('modalAlert').className = 'alert alert-success fade show m-0 ';    
    $('#alert').modal('show');
}

function SuccessUpdateAlert(title) {
    $('#modalAlert').text("'" + title + "' is successfully updated.");
    document.getElementById('modalAlert').className = 'alert alert-success fade show m-0';
    $('#alert').modal('show');
}

function FailedDeleteAlert(title) {
    $('#modalAlert').text("Failed to delete '" + title + "'!");
    document.getElementById('modalAlert').className = 'alert alert-danger fade show m-0';
    $('#alert').modal('show');
}

function FailedUpdateAlert(title) {
    $('#modalAlert').text("Failed to update '" + title + "'!");
    document.getElementById('modalAlert').className = 'alert alert-danger fade show m-0';
    $('#alert').modal('show');
}

function deleteProject(id, title) {
    
    var request = { 'id': id }
    fetch('./phpscripts/adminDeleteProject.php', {
        method: 'POST',
        body: JSON.stringify(request),
        headers: {
            'Content-type': 'application/json',
            'Accept': 'application/json'
        }
    })
        .then(response => response.json())
        .then(ans => {
            console.log(ans);
            if (ans > 0) {
                SuccessDeleteAlert(title);
                refreshforms();
            }
            else
                FailedDeleteAlert(title);
                refreshforms();
        });     
}

//Submits forms
function Submit(id, title) {
    var forms = $('.needs-validation');
    console.log(editor_full_description.getData());
    editor_full_description.updateSourceElement();

    console.log(editor_result.getData());
    editor_result.updateSourceElement();

    //Check if forms are valid
    var valid = true;
    var val = Array.prototype.filter.call(forms, function (form) {
        form.classList.add('was-validated');
        valid = form.checkValidity() && valid;
    });

    //If valid - get data from forms and send to server
    console.log(valid);
    if (valid === true) {
        //Get data from forms
        let title_formData = new FormData(document.getElementById('title-form'));
        let leader_formData = new FormData(document.getElementById('leader-form'));
        let short_descr_formData = new FormData(document.getElementById('short-description-form'));
        let areas_formData = new FormData(document.getElementById('areas-form'));

        console.log('title: ' + title_formData.get('title'));
        console.log('leader: ' + leader_formData.get('leader'));
        console.log('date_end_picker: ' + $('.date-picker').val());
        console.log('short_description: ' + short_descr_formData.get('short_description'));
        console.log('full description: ' + editor_full_description.getData());
        console.log('result: ' + editor_result.getData());
        
        var areas = new Array();
        $('.checkbox-item-area').each(function () {
            if (areas_formData.get(this.name) === 'on') {
                areas.push(this.id);
                console.log(this.id);
            }
        });
        console.log('areas: ' + areas.join(', '));        

        var members = new Array();
        $('.list-group-item').each(function () {
            if (this.value) {
                console.log(this.value);
                members.push(this.value);
            }                        
        });
        var lead = leader_formData.get('leader');
        members.push(lead);
        console.log('members: ' + members.join(', ')); 

        //Bind data together
        var update_project = {
            "id": id,
            "title": title_formData.get('title'),
            "leader": leader_formData.get('leader'),                      
            "date_end": $('.date-picker').val(),
            "short_description": short_descr_formData.get('short_description'), 
            "full_description": editor_full_description.getData(),
            "result": editor_result.getData(),
            "areas": areas.join(', '),
            "members": members.join(', ')            
        }

        //Send request    
        fetch('./phpscripts/adminUpdateProject.php', {
            method: 'POST',
            body: JSON.stringify(update_project),
            headers: {
                'Content-type': 'application/json',
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(answer => {
                if (answer === true) {
                    SuccessUpdateAlert(title);
                    refreshforms();
                }
                else
                    FailedUpdateAlert(title);
                    refreshforms();
            });
    }
}