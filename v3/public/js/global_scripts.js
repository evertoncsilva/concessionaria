function setActiveLinks(activePageName) {
    $("#navlink-"+activePageName).addClass("active");
}

$.urlParam = function(name){
    var results = new RegExp('[\?#]' + name + '=([^&#]*)').exec(window.location.href);
    if (results==null) {
       return null;
    }
    return decodeURI(results[1]) || 0;
}

var MAX_PAGE_SIZE = 10;

function resetFormById(id) {
    document.getElementById(id).reset();
}
function getValByID(id)
{
    return document.getElementById(id).value;
}

function logout()
{
    window.location.href = "http://localhost/v3/login.php?logout";
}