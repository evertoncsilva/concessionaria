let activePageName = 'marcas';
let pageSize = 10;
let activePageNumber = 0;
let allMarcas = null;
let marcasCount = 0;
let totalPagesCount = 0;
let lastPageNumber = 0;
let lastAlertId = 0;
let nextButtom = $("#paginator-next");
let previousButtom = $("#paginator-previous");
let mainContainer = $("#main-container");
const editorForm = $('#editor-form');
const tablePaginator = $('#tablePaginator');
const tablePanel = $('#table-panel');
const targetTable = $('#tableContent');
const checkboxAll = $('#checkbox-select-all');

window.addEventListener("load", _initialize, true);


function _initialize() 
{
    setActiveLinks(activePageName);
    ajaxGetMarcas();
}
function ajaxGetMarcas(page) 
{
    $.ajax({
        type: "GET",
        timeout: 5000,
        contentType: "application/json",
        cache: false,
        url: "/v3/marcas.php?all",
        error: function() {
            alert("Erro ao buscar dados, tente novamente mais tarde!");
        },
        success: function(result) {
            allMarcas = result;
            marcasCount = allMarcas.length;
            totalPagesCount = Math.ceil(marcasCount / pageSize);
            lastPageNumber = (totalPagesCount > 1) ? (totalPagesCount -1) : 0;
            try {
                if(page === 'last') {
                    renderPage(lastPageNumber);
                }
                else if(page === 'reload') {
                    renderPage(activePageNumber);
                }
                else {
                    renderPage(0);
                }
                
                $('#spinner').remove();
            } catch (error) {
                alert("Erro ao buscar dados!\nEntre em contato conosco no e-mail: mail@mail.com");
            }
        }

    });
}
function ajaxEditMarca(id) 
{
    var data = {
        action: 'update',
        id: id,
        nome: $('#editor-form #nome').val(),
        descricao: $('#editor-form #descricao').val()
    }
    $.post("/v3/marcas.php", data, function (result) {
        renderAlertSuccess("Componente editado com sucesso!");
        removeEditorForm();
        ajaxGetMarcas('reload');
    })
    .fail(function(error) {
        err = error.responseJSON;
        if(err === undefined) {
            err.message = "Erro desconhecido";
            err.code = "666";
        }
         renderAlertError("Não foi possível executar a operação ["+err.message+" | cód: "+err.code+"]");
         if(err.formErrors)
         updateEditorFormFieldErrors(err.formErrors);
    })

}
function renderTable(itens) 
{
    targetTable.empty();
    itens.forEach(function(item) {
        targetTable.append(templateGenerateTableItem(item));
    });
}
function ajaxDeleteManyMarcas(comps)
{
    let actualPage = activePageNumber;
    $.post("/v3/marcas.php", {'action': 'delete_many', 'items': comps}, function (data) {
        renderAlertSuccess(data.message);
    })
    .fail(function(error) {
        renderAlertError("Não foi possível executar a operação ["+error.message+"| cód: "+error.code+"]");
    })
    .always(function() {
        ajaxGetMarcas('reload');
    })
}
function renderPage(pageNumber) 
{
    pageNumber = (pageNumber > 0) ? pageNumber : 0;
    let startItem = pageNumber * pageSize;
    let slice = allMarcas.slice(startItem, (startItem + pageSize));
    toggleTablePanel(true);
    renderTable(slice);
    activePageNumber = pageNumber;
    updatePageButtoms();
    $('#table-itemcount').empty();
    $('#table-itemcount').append(allMarcas.length + 'Itens');
}
function toggleTablePanel(val)
{
    tablePanel.toggleClass('noshow', !val);
    tablePaginator.toggleClass('noshow', !val);
}
function toggleSelectAll(val)
{
    let toggleVal;
    if(val != undefined){
     toggleVal = val;
     checkboxAll.prop('checked', val);
    }
    else {
        toggleVal = checkboxAll.prop('checked');
    }
    $('.item-checkbox').prop('checked', toggleVal);
}
function renderCreateForm()
{
    if($('#editor-form-wrapper').length) return;
    toggleTablePanel(false);
    mainContainer.append(templateGenerateEditorForm());
}
function renderEditForm(id)
{
    toggleTablePanel(false);
    let item = allMarcas.filter(x => x.id === id);
    mainContainer.append(templateGenerateEditorForm(true, item));
}
function onClickNextPage()
{
    if(activePageNumber < lastPageNumber) {
        renderPage(activePageNumber + 1, );
    }
}
function onClickPreviousPage()
{
    if(activePageNumber > 0)
    {
        renderPage(activePageNumber -1);
    }
}
function onClickExcluirVarios()
{
    let selected = [];
    
    $.each($('.item-checkbox:checked'), function() {
        selected.push($(this).val());
    });
    
    if(selected.length === 0)
        {
            renderAlertError("Selecione algum item para deletar")
        }
    else 
        {
            ajaxDeleteManyMarcas(selected);
        }
}
function ajaxCreateMarca() 
{
    var data = {
        action: 'create',
        nome: $('#editor-form #nome').val(),
        descricao: $('#editor-form #descricao').val()
    }

    $.post("/v3/marcas.php", data, function (result) {
        renderAlertSuccess("Marca criada com sucesso!");
        removeEditorForm();
        ajaxGetMarcas('last');
    })
    .fail(function(error) {
        err = error.responseJSON;
        if(err === undefined) {
            err.message = "Erro desconhecido";
            err.code = "666";
        }
         renderAlertError("Não foi possível executar a operação ["+err.message+" | cód: "+err.code+"]");

         if(err.formErrors)
         updateEditorFormFieldErrors(err.formErrors);
    })
}
function onClickMenuAdicionar()
{
    renderCreateForm();
}
function removeEditorForm()
{
    $('#editor-form-wrapper').remove();
}
function updatePageButtoms()
{
    if(activePageNumber < 1) {previousButtom.toggleClass('disabled', true);}
    else {previousButtom.toggleClass('disabled', false);}

    if(activePageNumber < lastPageNumber) {nextButtom.toggleClass('disabled', false);}
    else {nextButtom.toggleClass('disabled', true);}
}
function templateGenerateTableItem(item) 
{
    return `<tr>
    <td class="td-select"><input type="checkbox" value="${item.id}" class="item-checkbox"></td>
    <td>${item.id}</td>
    <td>${item.nome}</td>
    <td>${item.descricao}</td>
    <td>
        <i class="material-icons clickable" onclick="onClickEditMarca(${item.id})">edit</i>
        <i class="material-icons clickable" onclick="onClickDeleteMarca(${item.id})">delete</i>
    </td>
    </tr>`;
}
function templateAlertSuccess(message) 
{
    lastAlertId++;
    return `<div id="alert-${lastAlertId}" class="alert alert-success alert-fixed" role="alert">
    <span class"alert-text">${message}</span>
    <i class="material-icons">done</i></div>`;
}
function templateAlertErro(message) 
{
    lastAlertId++;
    return `<div id="alert-${lastAlertId}" class="alert alert-warning alert-fixed" role="alert">
    <span class"alert-text">${message}</span>
    <i class="material-icons">error_outline</i></div>
    </div>`;
}
function templateGenerateEditorForm(isEditar, data)
{
    let item = (data != undefined) ? data[0] : undefined;
    let nome = (item != undefined) ? item.nome : '';
    let descricao = (item != undefined) ? item.descricao : '';
    let id = (item != undefined) ? item.id : '';
    let editar = (isEditar != undefined) ? isEditar : false;
    let method = editar ? "ajaxEditMarca("+id+")" : "ajaxCreateMarca()" ;
    let verb = editar ? "Editar" : "Criar";
    let panelTitle = verb + " marca";

    return `<div id="editor-form-wrapper" class="card">
                <div class="card-header">
                <h3 class="panel-title">${panelTitle}</h3>
                </div>
                <form id="editor-form" action="">
                    <input type="hidden" name="id" value="${id}">
                    <div class="container-fluid">
                        <div class="row">

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="nome" >Nome da marca</label>
                                <input  type="text" 
                                        class="form-control" 
                                        name="nome" id="nome" 
                                        placeholder="Insira o nome da marca aqui" 
                                        value="${nome}"
                                        maxlength="40">
                            </div>  
                        </div>

                        <div class="col-md-6">
                            <div class="form group" data-group="descricao">
                                <label for="descricao">Descrição da marca</label>
                                <input  type="text" 
                                        class="form-control" 
                                        name="descricao" 
                                        id="descricao" 
                                        placeholder="Insira uma descrição para sua marca" value="${descricao}"
                                        >
                            </div>
                        </div>
                    </div>

                    <div class="row editor-button-container">
                    <button type="reset" class="btn btn-default" >Limpar</button>
                    <button type="button" class="btn btn-warning" onclick="onClickCreateFormCancelar()">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="${method}">Salvar</button>
                </div>
                </form>
            </div>`
}
function renderAlertSuccess(msg) 
{
    $("body").append(templateAlertSuccess(msg));
        $("#alert-"+lastAlertId).delay(1500).fadeOut(1500, function(){
            $(this).remove();
        });
}
function renderAlertError(msg) 
{
    $("body").append(templateAlertErro(msg));
        $("#alert-"+lastAlertId).delay(1500).fadeOut(1500, function(){
            $(this).remove();
        });
}
function onClickEditMarca(id) 
{
    renderEditForm(id);
}
function ajaxDeleteMarca(id) 
{
    $.post("/v3/marcas.php", {'action': 'delete_one', 'id': id}, function (data) {
        removeItemFromList(id);
        renderAlertSuccess(data.message);
    })
    .fail(function(error) {
        renderAlertError("Não foi possível executar a operação ["+error.message+"| cód: "+error.code+"]");
    })
    .always(function() {
        updateReloadTable();
    })
}
function onClickDeleteMarca(id) 
{
    ajaxDeleteMarca(id);
}
function updateReloadTable()
{
    renderPage(activePageNumber);
}
function removeItemFromList(id)
{
    allMarcas = allMarcas.filter(function(item) {
        return item.id != id;
    })
}
function onClickCreateFormCancelar() 
{
    removeEditorForm();
    renderPage(activePageNumber);
}
function updateEditorFormFieldErrors(data)
{
    if(data === undefined || data === null) return;
    if(data.nome)
    {

    }
}