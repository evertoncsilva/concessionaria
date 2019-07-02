function setActiveLinks(activePageName) {
    $("#navlink-"+activePageName).addClass("active");
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