//Created by Mariia Nema
//Get documents from IEEE
GetIEEE();
GetJoke();
function GetIEEE() {
    fetch('http://ieeexploreapi.ieee.org/api/v1/search/articles?apikey=n25xej7qeefk9y7c8ay6qpr4&' +
        'format=json&max_records=10&start_record=1&sort_order=asc&sort_field=article_number&' +
        'affiliation=' + 'Malardalen' +
        '&start_year=' + moment().year() +
        '&end_year=' + moment().year())
        .then(response => response.json())
        .then(json => {
            console.log(json);
            if (json['total_records'] > 0) {
                var IEEEContainer = document.getElementById('IEEE');
                var list = document.createElement('ul');
                list.className = 'IEEE-list list-unstyled list-group';
                for (var i in json['articles']) {
                    var item = document.createElement('li');
                    item.className = 'IEEE-item border-bottom';
                    var article = document.createElement('div');

                    var title_link = document.createElement('a');
                    title_link.href = json['articles'][i]['html_url'];
                    var title = document.createElement('small');

                    title.innerText = json['articles'][i]['title'];
                    title_link.appendChild(title);
                    article.appendChild(title_link);

                    var authors = document.createElement('div');
                    authors.className = 'IEEE-authors mt-2';

                    for (var j in json['articles'][i]['authors']['authors']) {
                        authors.innerText += json['articles'][i]['authors']['authors'][j]['full_name'];
                        authors.innerText += ', ';
                    }
                    authors.innerText = authors.innerText.slice(0, authors.innerText.length - 2);

                    article.appendChild(authors);
                    item.appendChild(article);

                    list.appendChild(item);
                    IEEEContainer.appendChild(list);
                }
                document.getElementById('IEEE-card').style.display = "block";
            }
        });
}

function GetJoke() {
    fetch('https://sv443.net/jokeapi/v2/joke/Programming?blacklistFlags=nsfw,religious,political,racist,sexist&type=single')
        .then(response => response.json())
        .then(json => {
            console.log(json);
            if (json['error'] !== true) {
                var joke = document.getElementById('joke');
                joke.innerHTML = json['joke'];
                document.getElementById('joke-card').style.display = "block";
            }

        });
} 