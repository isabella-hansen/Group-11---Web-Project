//created by Emelie Wallin


//onclick event for biography-tab
$('#bio').click(function () {
    $('#text').html($('#biography').css('display', 'block'));
});

//onclick event for projects-tab
$("#projects").click(function () {

    $('#text').html($('#biography').css('display', 'none'));
    var id = sessionStorage.getItem('id');

    //fetch response from getProjectMember-page
    fetch('phpscripts/getProjectForMember.php?q=' + id)
        .then(function (response) {
            return response.json();
        })
        .then(function (json) {
            if (json[0]['rows'] > 0) { //loop through response array
                json[1]['projects'].forEach(project => {
                    var card = createProjCards(project);
                    $('#text').append(card);
                });
            }
        })
});

//Created by Mariia Nema, small edit by Emelie Wallin
//create cards for projects
function createProjCards(project) {

    var project_card = document.createElement('div');
    project_card.className = 'project-card row no-gutters border rounded overflow-hidden mb-2 h-md-150 position-relative';

    var project_general_info = document.createElement('div');
    project_general_info.className = 'project-general-info col-9 mr-0 p-4 position-relative';

    var title = document.createElement('h3');
    title.className = 'title mb-0';
    title.innerText = project['title'];
    project_general_info.appendChild(title);

    var short_description = document.createElement('p');
    short_description.className = 'short-description card-text mb-auto';
    short_description.innerText = project['short_description'];
    project_general_info.appendChild(short_description);

    var link = document.createElement('a');
    link.className = 'stretched-link text-info';
    link.href = 'project.php?id=' + project['id'];
    link.innerText = 'More';
    project_general_info.appendChild(link);
    project_card.appendChild(project_general_info);

    var project_details = document.createElement('div');
    project_details.className = 'project-details col-3 m-0 p-4 d-none d-md-block';

    var project_status = document.createElement('p');
    project_status.className = 'mb-0';

    if (project['status'] == 'active') { //check status on project

        var status_icon = document.createElement('i');
        status_icon.className = 'fa fa-unlock';
        project_status.appendChild(status_icon);
    }
    else {

        var status_icon = document.createElement('i');
        status_icon.className = 'fa fa-lock';
        project_status.appendChild(status_icon);
    }
  
    project_details.appendChild(project_status);
    project_card.appendChild(project_details);
    return project_card;

}

//onclick event for publication-tab
$("#pubMember").click(function () {
    $('#text').html($('#biography').css('display', 'none'));
    var id = sessionStorage.getItem('id');

    //fetch response from getPublicationsForMember-page
    fetch('phpscripts/getPublicationsForMember.php?q=' + id)
        .then(function (response) {
            return response.json();
        })
        .then(function (json) {
            if (json[0]['rows'] > 0) { //loop through the resoponse array
                json[1]['publications'].forEach(publication => {
                    var card = createPubCards(publication);
                    $('#text').append(card);
                });
            }
        })
});

//Created by Mariia Nema, small edit by Emelie Wallin
//Create cards for publications
function createPubCards(pub) {

    var pub_card = document.createElement('div');
    pub_card.className = 'pub-card row no-gutters border rounded overflow-hidden mb-2 h-md-150 position-relative';

    var pub_general_info = document.createElement('div');
    pub_general_info.className = 'pub-general-info col-9 mr-0 p-4 position-relative';

    var title = document.createElement('h3');
    title.className = 'title mb-0';
    title.innerText = pub['title'];
    pub_general_info.appendChild(title);

    //authors who are members will be listed as a link to their member-page
    pub['authors'].forEach(author => {
        console.log('test');
        if (author['id'] > 0) {
            console.log('test');
            var link = document.createElement('a');
            link.className = "text-info"
            link.href = 'memberinfo.php?id=' + author['id'];
            link.innerText = author['name'] + " ";
            pub_general_info.appendChild(link);
            pub_card.appendChild(pub_general_info);
        }
        else {
            var name = document.createElement('span');
            name.className = 'name card-text mb-auto';
            name.innerText = author['name'];
            pub_general_info.appendChild(name);
            pub_card.appendChild(pub_general_info);
        }
    });

    var short_description = document.createElement('p');
    short_description.className = 'short-description card-text mb-auto';
    short_description.innerText = pub['description'];
    pub_general_info.appendChild(short_description);


    var pub_details = document.createElement('div');
    pub_details.className = 'project-details col-3 m-0 p-4 d-none d-md-block';

    var pub_date = document.createElement('p');
    pub_date.className = 'mb-0';

    var pub_date_text = document.createElement('em');
    pub_date_text.innerText = ("( " + pub['date'] + " )");

    pub_date.appendChild(pub_date_text);
    pub_details.appendChild(pub_date);
    pub_card.appendChild(pub_details);
    return pub_card;

}

//onclick event for edit-tab
$("#edit").click(function () {
    $('#text').html($('#biography').css('display', 'none'));
    $.ajax({
        url: "views/_editInfo.php",
        success: function (result) {
            $("#text").append(result);
        }
    })
});
