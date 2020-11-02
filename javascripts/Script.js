var modal = document.getElementById("login_modal");
var btn = document.getElementById("login_btn");
console.log(btn);
var close = document.getElementsByClassName("login_close")[0];
btn.onclick = function () {
    modal.style.display = "block";
}
close.onclick = function () {
    modal.style.display = "none";
}
window.onclick = function (event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
function failedToLogin() {
    modal.style.display = "block";
    alert("Incorect login credentials, please try again!");
}

function showActiveTab(tabID) {
    if (tabID == "views/_project.php") {
        tabID = "views/_projects.php";
    } else if (tabID == "views/_forgottenPassword.php") {
        document.getElementById('login_btn').style.border = "1mm solid white";
        document.getElementById('login_btn').style.borderRadius = "3mm";
        document.getElementById('login_btn').style.padding = "4mm 11mm 4mm 11mm";
        return;
    }
    document.getElementById(tabID).style.border = "1mm solid white";
    document.getElementById(tabID).style.borderRadius = "3mm";
    var fc = document.getElementById(tabID).childNodes;
    var sc = fc[1].childNodes;
    sc[1].style.width = "32mm";
}

function showOnButtonClick() {
    var a = document.getElementById("main_nav");
    if (a.style.display == "block") {
        a.style.display = "none";
    } else {
        a.style.display = "block";
    }
}

function showOnResize() {
    var a = document.getElementById("main_nav");
    var x = window.innerWidth;
    if (x > 924.094488189) {
        a.style.display = "block";
    } else {
        a.style.display = "none";
    }
}

function showAbstract(pub_id, btn_id) {
    document.getElementById(pub_id).style.display = "block";
    document.getElementById(btn_id).style.display = "none";
}