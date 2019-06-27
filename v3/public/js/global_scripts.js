function setActiveLinks(activePageName) {
    $("#navlink-"+activePageName).addClass("active");
}

var MAX_PAGE_SIZE = 10;

function resetFormById(id) {
    document.getElementById(id).reset();
}
function getVal_ByID(id)
{
    return document.getElementById(id).value;
}