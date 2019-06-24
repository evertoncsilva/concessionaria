function setActiveLinks(activePageName) {
    $("#navlink-"+activePageName).addClass("active");
}

var MAX_PAGE_SIZE = 10;

function resetFormById(id) {
    document.getElementById(id).reset();
}