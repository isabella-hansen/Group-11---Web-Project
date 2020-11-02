//Created by Mariia Nema
var editor_full_description;

$(function () {
    //Daterangepicker config
    $('#date_start').daterangepicker({ 
        opens: 'right',
        singleDatePicker: true,
        drops: 'down',
        showDropdowns: true,
        autoUpdateInput: true,
        timePicker: false,
        singleDate: true,        
        maxDate: moment(),
        locale: {
            format: 'YYYY-MM-DD',
            cancelLabel: 'Clear',
            applyButtonClasses:'btn-info'
        },       
    })

    $('.date-picker').val('');

    $('.readonly').keydown(function (e) {
        e.preventDefault();
    });

    $('.date-picker').on('apply.daterangepicker', function (e,picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));

    });
    $('.date-picker').on('cancel.daterangepicker', function (e,picker) {
        $(this).val('');
    });


    //Editor config    
    ClassicEditor
        .create(document.querySelector('#full_description'))
        .then( editor => {
            editor_full_description = editor;
            console.log(editor);
        })
            .catch( error => {                
            });
});

function refreshforms() {
    //refresh details
    $('#title').val('');
    $('#leader').val('');
    $('#date_start').val('');
    $('#short_description').val(''); 
    editor_full_description.setData('');

    //refresh in areas checkboxes
    $('.checkbox-item-area').each(function () {
        if (this.checked == true)
            this.checked = false;
    });

    //refresh members
    document.getElementById('participants').innerHTML = '';

    //refresh validate state  
    $('.needs-validation').each(function () {
        this.classList.remove('was-validated');        
    });   

    $('.date-picker').val('');
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
            document.getElementById('participants').removeChild(list[i]);
        }
    }
}

//Submits forms
function Submit() {
    var forms = $('.needs-validation');
    console.log(editor_full_description.getData());
    editor_full_description.updateSourceElement();

    //Check if forms are valid
    var valid = true;
    var val = Array.prototype.filter.call(forms, function (form) {
        form.classList.add('was-validated');
        valid = form.checkValidity() && valid;            
    });

    //If valid - get data from forms and send to server
    console.log(valid);
    if (valid===true) {
        //Get data from forms
        let title_formData = new FormData(document.getElementById('title-form'));
        let leader_formData = new FormData(document.getElementById('leader-form'));
        let short_descr_formData = new FormData(document.getElementById('short-description-form'));
        let areas_formData = new FormData(document.getElementById('areas-form'));

        console.log('title: ' + title_formData.get('title'));
        console.log('leader: ' + leader_formData.get('leader'));
        console.log('date_start_picker: ' + $('.date-picker').val());
        console.log('short_description: ' + short_descr_formData.get('short_description'));
        console.log('full description: ' + editor_full_description.getData());        

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
        var new_project = {
            "title": title_formData.get('title'),
            "leader": leader_formData.get('leader'),
            "date_start": $('.date-picker').val(),
            "short_description": short_descr_formData.get('short_description'),
            "full_description": editor_full_description.getData(),
            "areas": areas.join(', '),
            "members": members.join(', ')     
        }
        
        //Send request    
        fetch('/phpscripts/admininsertproject.php', {
            method: 'POST',
            body: JSON.stringify(new_project),
            headers: {
                'Content-type': 'application/json',
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(answer => {
                if (answer === true) {
                    SuccessCreateAlert(title);
                    refreshforms();
                }
                else
                    FailedCreateAlert(title);
                    refreshforms();
            });
    }
}

function closeAlert() {
    $('#alert').removeClass('show');
}

function SuccessCreateAlert(title) {
    $('#modalAlert').text("The project is successfully created.");
    $('#modalAlert').addClass('alert-success fade show');
    $('#alert').modal('show');
}

function FailedCreateAlert(title) {
    $('#modalAlert').text("Failed to create project!");
    $('#modalAlert').addClass('alert-danger fade show');
    $('#alert').modal('show');
}

function PartlyCreateAlert(title) {
    $('#modalAlert').text("Project is created. Failed to add research areas");
    $('#modalAlert').addClass('alert-warning fade show');
    $('#alert').modal('show');
}