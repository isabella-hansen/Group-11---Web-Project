// Created by Mariia Nema
$(function () {

    //Datepicker config
    $('.date-picker').daterangepicker({
        showDropDowns: true,
        opens: 'right',
        drops: 'down',
        showDropdowns: true,
        autoUpdateInput: false,
        timePicker: false,
        maxDate: moment(),
        locale: {
            format: 'YYYY/MM/DD',
            cancelLabel: 'Clear'
        },
        ranges: {
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'This Year': [moment().startOf('year'), moment().endOf('year')],
            'All time': [new Date('1977/01/01'), moment()]
        }
    })

    $('.date-picker').on('apply.daterangepicker', function (e, picker) {
        $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
        filter(null, 'next');
    });
    $('.date-picker').on('cancel.daterangepicker', function (e, picker) {
        $(this).val('');
        filter(null, 'next');
    });

    console.log('Research areas configuring...');
    //Research area filter config
    $('.checkbox-item-area').change(function () {
        filter(null, 'next');
    });

    console.log('Search configuring...');
    //Searchbar config
    $("#search-input").bind("keyup change", function () {
        filter(null, 'next');
    });

    console.log('Status configuring...');
    //Status filter config
    $('#active').click(function () {
        $('#date-end-picker').val('');
        $('#div-date-end-picker').css("display", "none");
    });
    $('#finish').click(function () {
        $('#div-date-end-picker').css("display", "");
    });
    $('#all').click(function () {
        $('#div-date-end-picker').css("display", "");
    });
    $('.radio-item-status').click(function () {
        filter(null, 'next');
    });

    filter(null, 'next');

});

//Filter projects
function filter(start_id, direction) {
    let formData = new FormData(document.getElementById('filter'));
    var areas = new Array();
    $('.checkbox-item-area').each(function () {
        if (formData.get(this.name) === 'on') {
            areas.push(this.name);
        }
    });

    var areas_array = areas.join(', ');

    var filter_pager = {
        "status": (formData.get('status') === "null" ? null : formData.get('status')),
        "search_expr": (formData.get('search_expr') === "" ? null : formData.get('search_expr')),
        "project_start_from": (formData.get('start_range') === "" ? null : formData.get('start_range').substring(0, 10).replaceAll("/", "-")),
        "project_start_to": (formData.get('start_range') === "" ? null : formData.get('start_range').substring(13, 23).replaceAll("/", "-")),
        "project_end_from": (formData.get('end_range') === "" ? null : formData.get('end_range').substring(0, 10).replaceAll("/", "-")),
        "project_end_to": (formData.get('end_range') === "" ? null : formData.get('end_range').substring(13, 23).replaceAll("/", "-")),
        "start_id": start_id,
        "limit": 5,
        "areas_array": (areas_array === "" ? null : areas_array),
        "direction": direction
    };

    console.log(filter_pager);

    //Fetch data from server    
    fetch('phpscripts/filterprojects.php', {
        method: 'POST',
        body: JSON.stringify(filter_pager),
        headers: {
            'Content-type': 'application/json',
            'Accept': 'application/json'
        }
    })
        .then(response => response.json())
        .then(json => {
            console.log(json[0]['num_rows']);

            //Fill in projects list
            let project_list = document.getElementById('projects-list');
            while (project_list.lastChild) {
                project_list.removeChild(project_list.lastChild);
            }
            var nextIsDisabled = false;
            var previousIsDisabled = false;

            if (json[0]['num_rows'] > 0 && direction == "next") {
                //If next is pushed and there is at least one project
                console.log('next, >0');
                if (start_id == null) {
                    //In case it's fresh search/new search
                    console.log('next, >0, start_id=null,');
                    $('#previous-page').val(null);
                    paginationStyle('previous-page', 'li-previous-page', 'disabled');
                    previousIsDisabled = true;
                    console.log('previous = null');
                    console.log('previous disabled');
                }
                else {
                    //In case filter wasn't changed, not fresh/new search
                    console.log('next, >0, start_id NOT null,');
                    $('#previous-page').val(json[1]['proj'][0]['id']);
                    paginationStyle('previous-page', 'li-previous-page', 'active');
                    previousIsDisabled = false;
                    console.log('previous: ' + json[1]['proj'][0]['id']);
                    console.log('previous active');
                }

                if (json[0]['num_rows'] > 4) {
                    console.log('next, >0, >4');
                    console.log('splice');
                    json[1]['proj'].splice(4, 1);
                    //if return more than place on page - show next button                     
                    paginationStyle('next-page', 'li-next-page', 'active');
                    nextIsDisabled = false;
                    $('#next-page').val(json[1]['proj'][3]['id']);
                    console.log('next active');
                    console.log('next: ' + json[1]['proj'][3]['id']);
                }
                else {
                    console.log('next, >0, <=4');
                    //if returns less than place on page - hide next button                
                    paginationStyle('next-page', 'li-next-page', 'disabled');
                    nextIsDisabled = true;
                    $('#next-page').val(null);
                    console.log('next disable');
                    console.log('next: = null');
                }
            }

            if (json[0]['num_rows'] > 0 && direction == "prev") {
                //If previous is pushed and there is at least one project
                console.log('prev, >0');
                if (json[0]['num_rows'] > 4) {
                    //if return more than place on page - show prev button      
                    console.log('prev, >0, >4');
                    console.log('splice');
                    json[1]['proj'].splice(0, 1);
                    $('#previous-page').val(json[1]['proj'][0]['id']);
                    paginationStyle('previous-page', 'li-previous-page', 'active');
                    previousIsDisabled = false;
                    console.log('prev active');
                    console.log('prev: ' + json[1]['proj'][0]['id']);
                    //if return more than place on page - show next button (if prev is pressed then it should be next) 
                    paginationStyle('next-page', 'li-next-page', 'active');
                    nextIsDisabled = false;
                    $('#next-page').val(json[1]['proj'][3]['id']);
                    console.log('next active');
                    console.log('next: ' + json[1]['proj'][3]['id']);
                }
                else {
                    //if return less than place available on page - hide prev button   
                    console.log('prev, >0, <=4');
                    $('#previous-page').val(null);
                    paginationStyle('previous-page', 'li-previous-page', 'disabled');
                    previousIsDisabled = true;
                    console.log('prev disabled');
                    console.log('prev = null');
                    //if return less than place available on page - next will get pointer to last project on the page
                    //can happen that a project is added while user is watching through projects
                    paginationStyle('next-page', 'li-next-page', 'active');
                    nextIsDisabled = false;
                    $('#next-page').val(json[1]['proj'][json[1]['proj'].length - 1]['id']);
                    console.log('next active');
                    console.log('next: ' + json[1]['proj'][json[1]['proj'].length - 1]['id']);
                }
            }
            else if (json[0]['num_rows'] == 0 && direction == 'next') {
                //if no project was found set when next pushed 
                //like if project was deleted from that end while usr wwas watcing through the list
                console.log('next, =0');
                paginationStyle('next-page', 'li-next-page', 'disabled');
                nextIsDisabled = true;
                $('#next-page').val(null);
                console.log('next disabled');
                console.log('next: =null');
            }
            else if (json[0]['num_rows'] == 0 && direction == 'prev') {
                //if no project was found set when prev pushed 
                //like if project was deleted from that end while usr was watcing through the list
                console.log('prev, =0');
                paginationStyle('previous-page', 'li-previous-page', 'disabled');
                previousIsDisabled = true;
                $('#previous-page').val(null);
                console.log('previous disabled');
                console.log('prev = null');
            }

            //Creating HTML for project card
            if (json[0]['num_rows'] > 0) {
                json[1]['proj'].forEach(project => {

                    var project_card = createProjectCard(project);
                    project_list.appendChild(project_card);
                });
            }
        });
}

//Creating HTML for project card
function createProjectCard(project) {
    console.log('creating card');
    var project_card = document.createElement('div');
    project_card.className = 'project-card row no-gutters border rounded overflow-hidden mb-2 shadow-sm h-md-150 position-relative';
    var project_general_info = document.createElement('div');
    project_general_info.className = 'project-general-info col-9 mr-0 p-3 position-relative';
    var areas = document.createElement('strong');
    areas.className = 'mb-2 project-areas';
    areas.innerText = project['all_areas'];
    project_general_info.appendChild(areas);
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
    project_details.className = 'project-details col-3 m-0 p-3 d-none d-md-block';

    var project_start = document.createElement('p');
    project_start.className = 'mb-0';

    var status_icon = document.createElement('i');
    status_icon.className = 'fa fa-unlock';
    project_start.innerText += (' ' + project['date_start']);
    project_start.insertAdjacentElement('afterbegin', status_icon);

    project_details.appendChild(project_start);

    if (project['date_end'] != null) {
        var project_end = document.createElement('p');
        project_end.className = 'mb-0';

        var end_icon = document.createElement('i');
        end_icon.className = 'fa fa-lock';

        project_end.innerText += (' ' + project['date_end']);
        project_end.insertAdjacentElement('afterbegin', end_icon);

        project_details.appendChild(project_end);
    }

    var leader = document.createElement('p');
    leader.className = 'mb-0';
    var leader_icon = document.createElement('i');
    leader_icon.className = 'fa fa-user';
    var leader_link = document.createElement('a');
    leader_link.className = 'text-info';
    leader_link.href = 'memberinfo.php?id=' + project['leader'];
    leader_link.innerText = (' ' + project['first_name'] + ' ' + project['last_name']);
    leader_link.insertAdjacentElement('afterbegin', leader_icon);
    leader.insertAdjacentElement('beforeend', leader_link);
    project_details.appendChild(leader);
    project_card.appendChild(project_details);
    return project_card;
}

//Managing pager buttons styling
function paginationStyle(button_id, link_id, mode) {
    if (mode == 'disabled') {
        document.getElementById(link_id).setAttribute('class', 'page-item disabled');
        document.getElementById(button_id).setAttribute('aria-disabled', 'true');
    }
    else if (mode == 'active') {
        document.getElementById(link_id).setAttribute('class', 'page-item');
        document.getElementById(button_id).setAttribute('aria-disabled', 'false');
    }
}




//        //Client side search function. Is rewritten and done on server side. 
//        //But let it be here just in case...
//        function filterProjectsOnClient() {
//            //Getting cards collection
//            let all_cards = document.getElementsByClassName('project-card');

//            //Getting search input     
//            var search_expr = $("#search-input").val().toLowerCase();

//            //Creating an array with checked values
//            var checkbox_choices = new Array();
//            let chexbox_all_inputs = document.getElementsByClassName('checkbox-item-area');
//            let checkbox_all_inputs_length = chexbox_all_inputs.length
//            for (var i = 0; i < checkbox_all_inputs_length; i++) {
//                if (chexbox_all_inputs[i].checked == true) {
//                    checkbox_choices.push(chexbox_all_inputs[i].id);
//                }
//            }

//            //Getting status value   
//            let status = 'all';
//            let radio_all_inputs = document.getElementsByClassName('radio-item-status');
//            let radio_all_inputs_length = radio_all_inputs.length
//            for (var i = 0; i < radio_all_inputs_length; i++) {
//                if (radio_all_inputs[i].checked == true) {
//                    status = radio_all_inputs[i].id;
//                }
//            }

//            //Getting dates
//            let project_start_from = new Date($('#date-start-picker').data('daterangepicker').startDate.format('YYYY-MM-DD'));
//            let project_start_to = new Date($('#date-start-picker').data('daterangepicker').endDate.format('YYYY-MM-DD'));
//            let project_end_from = new Date($('#date-end-picker').data('daterangepicker').startDate.format('YYYY-MM-DD'));
//            let project_end_to = new Date($('#date-end-picker').data('daterangepicker').endDate.format('YYYY-MM-DD'));

//            //Going through cards collection

//            let all_cards_length = all_cards.length;
//            for (var i = 0; i < all_cards_length; i++) {

//                let match = true;

//                //Check search
//                if (!($(all_cards[i]).find('.title').text().toLowerCase().indexOf(search_expr) > -1)
//                    && !($(all_cards[i]).find('.short-description').text().toLowerCase().indexOf(search_expr) > -1))
//                    match = false;

//                //Check the status
//                let card_status = $(all_cards[i]).find('.project-status')[0];

//                if (match && status != 'all' && card_status.innerText != status) {
//                    match = false;
//                }

//                //Check start date        
//                if (match && $('#date-start-picker')[0].value.length != 0) {

//                    //Here was variables      

//                    //Get started-data from card
//                    let card_start = new Date($(all_cards[i]).find('.project-start')[0].innerText);

//                    //Compare dates
//                    if (card_start.getTime() < project_start_from.getTime() || card_start.getTime() > project_start_to.getTime()) {
//                        match = false;
//                        console.log('no start date match');
//                    }
//                }

//                //Check end date
//                if (match && $('#date-end-picker')[0].value.length != 0) {

//                    //Here was variables         

//                    //Get finished-data from card            
//                    if (typeof $(all_cards[i]).find('.project-end')[0] === 'undefined')
//                        match = false;
//                    else {
//                        let card_finish = new Date($(all_cards[i]).find('.project-end')[0].innerText);

//                        //Compare dates
//                        if (card_finish.getTime() < project_end_from.getTime() || card_finish.getTime() > project_end_to.getTime()) {
//                            match = false;
//                            console.log('no end date match');
//                        }
//                    }
//                }

//                //Check the areas
//                if (match) {
//                    let card_area = $(all_cards[i]).find('.project-areas')[0];
//                    let checkbox_choices_length = checkbox_choices.length;
//                    for (var j = 0; j < checkbox_choices_length; j++) {
//                        if (!(card_area.innerText.indexOf(checkbox_choices[j]) >= 0)) {
//                            match = false;
//                            break;
//                        }
//                    }
//                }

//                //Hide filtered
//                if (!match)
//                    all_cards[i].style.display = "none";
//                else
//                    all_cards[i].style.display = "";
//            }

//        }
//    }
//})