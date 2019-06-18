var activePage = 'componentes';
var pageSize = 10;
var activePage = 0;
var allComponentes = null;
var componentesCount = 0;
var totalPagesCount = 0;
var nextButtom = $("#paginator-next");
var previousButtom = $("#paginator-previous");
var _tableContainer = $("#table-container");
const targetTable = $('#tableContent');

window.addEventListener("load", initialize, true);


function initialize() {
    setActiveLinks(activePage);
    //$('#tableContent').append(generateTableItem(item));
    //$('#spinner').remove();
    getComponentes();
}
function getComponentes() {
    $.ajax({
        type: "GET",
        timeout: 5000,
        contentType: "application/json",
        cache: false,
        url: "/v3/componentes.php?all",
        error: function() {
            alert("Erro ao buscar dados, tente novamente mais tarde!");
        },
        success: function(result) {
            console.log(Object.entries(result));
            allComponentes = result;
            componentesCount = allComponentes.length;
            totalPagesCount = Math.ceil(componentesCount / pageSize);
            try {
                renderPage(0);
                $('#spinner').remove();
            } catch (error) {
                alert("Erro ao buscar dados!\nEntre em contato conosco no e-mail: mail@mail.com");
            }
        }

    });
}
function renderTable(itens) {
    targetTable.empty();

    itens.forEach(function(item) {
        targetTable.append(generateTableItem(item));
    });
}
function renderPage(pageNumber) {
    pageNumber = (pageNumber >= 0) ? pageNumber : 0;
    let startItem = pageNumber * pageSize;
    let slice = allComponentes.slice(startItem, (startItem + pageSize));

    renderTable(slice);
    activePage = pageNumber;
    updatePageButtoms();
}
function onNextPage()
{
    renderPage(activePage + 1, );
}
function onPreviousPage()
{
    if(activePage > 0)
    {
        renderPage(activePage -1);
    }
}
function updatePageButtoms()
{
    if(activePage < 1) {previousButtom.toggleClass('disabled', true);}
    else {previousButtom.toggleClass('disabled', false);}

    var teste = activePage < totalPagesCount;
    var testeButtom = nextButtom;
    if(activePage < totalPagesCount) {nextButtom.toggleClass('disabled', false);}
    else {nextButtom.toggleClass('disabled', true);}
}
function generateTableItem(item) {
    return `<tr>
    <td class="td-select"><input type="checkbox"></td>
    <td>${item.id}</td>
    <td>${item.nome}</td>
    <td>${item.descricao}</td>
    <td>
      <span class="glyphicon glyphicon-trash table-option pull-right" aria-hidden="true" onclick="onEditComponente()"></span>
      <span class="glyphicon glyphicon-pencil table-option pull-right " aria-hidden="true" onclick="ondeDeleteComonente()"></span>
    </td>
    </tr>`;
}
function onEditComponente() {
    _tableContainer.hide();
}
function onDeleteComponente() {

}