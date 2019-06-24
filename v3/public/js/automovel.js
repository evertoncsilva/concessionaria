let activePageName      = 'automoveis';
let allAutomoveis       = null;
let pageSize            = 10;
let activePageNumber    = 0;
let automoveisCount     = 0;
let totalPagesCount     = 0;
let lastPageNumber      = 0;
let lastAlertId         = 0;
let nextButtom          = $("#paginator-next");
let previousButtom      = $("#paginator-previous");
let mainContainer       = $("#main-container");
const editorForm        = $('#editor-form');
const tablePaginator    = $('#tablePaginator');
const tablePanel        = $('#table-panel');
const targetTable       = $('#tableContent');
const checkboxAll       = $('#checkbox-select-all');

window.addEventListener("load", _initialize, true);


function _initialize() 
{
    setActiveLinks(activePageName);
    ajax_GetAutomoveis();
}
function ajax_GetAutomoveis(page) 
{
    $.ajax({
        type: "GET",
        timeout: 5000,
        contentType: "application/json",
        cache: false,
        url: "/v3/automoveis.php?all",
        error: function() {
            alert("Erro ao buscar dados, tente novamente mais tarde!");
        },
        success: function(result) {
            allAutomoveis = result;
            automoveisCount = allAutomoveis.length;
            totalPagesCount = Math.ceil(automoveisCount / pageSize);
            lastPageNumber = (totalPagesCount > 1) ? (totalPagesCount -1) : 0;
            try {
                if(page === 'last') {
                    render_Page(lastPageNumber);
                }
                else if(page === 'reload') {
                    render_Page(activePageNumber);
                }
                else {
                    render_Page(0);
                }
                
                $('#spinner').remove();
            } catch (error) {
                alert("Erro ao buscar dados!\nEntre em contato conosco no e-mail: mail@mail.com");
            }
        }

    });
}
function ajax_EditAutomovel(id) 
{
    var data = {
        action: 'update',
        id: id,
        nome: $('#editor-form #nome').val(),
        descricao: $('#editor-form #descricao').val()
    }
    $.post("/v3/automoveis.php", data, function (result) {
        render_AlertSuccess("Automóvel editado com sucesso!");
        remove_EditorForm();
        ajax_GetAutomoveis('reload');
    })
    .fail(function(error) {
        err = error.responseJSON;
        if(err === undefined) {
            err.message = "Erro desconhecido";
            err.code = "666";
        }
         render_AlertError("Não foi possível executar a operação ["+err.message+" | cód: "+err.code+"]");
         if(err.formErrors)
         update_EditorFormFieldErrors(err.formErrors);
    })

}
function render_Table(itens) 
{
    targetTable.empty();
    itens.forEach(function(item) {
        targetTable.append(template_GenerateTableItem(item));
    });
}
function ajax_DeleteManyAutomoveis(comps)
{
    let actualPage = activePageNumber;
    $.post("/v3/automoveis.php", {'action': 'delete_many', 'items': comps}, function (data) {
        render_AlertSuccess(data.message);
    })
    .fail(function(error) {
        renderAlertError("Não foi possível executar a operação ["+error.message+"| cód: "+error.code+"]");
    })
    .always(function() {
        ajax_GetAutomoveis('reload');
    })
}
function render_Page(pageNumber) 
{
    pageNumber = (pageNumber > 0) ? pageNumber : 0;
    let startItem = pageNumber * pageSize;
    let slice = allAutomoveis.slice(startItem, (startItem + pageSize));
    toggle_TablePanel(true);
    render_Table(slice);
    activePageNumber = pageNumber;
    update_PageButtoms();
    $('#table-itemcount').empty();
    $('#table-itemcount').append(allAutomoveis.length + 'Itens');
}
function toggle_TablePanel(val)
{
    tablePanel.toggleClass('noshow', !val);
    tablePaginator.toggleClass('noshow', !val);
}
function toggle_SelectAll(val)
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
function render_CreateForm()
{
    if($('#editor-form-wrapper').length) return;
    toggle_TablePanel(false);
    mainContainer.append(template_GenerateEditorForm());
}
function render_EditForm(id)
{
    toggle_TablePanel(false);
    let item = allAutomoveis.filter(x => x.id === id);
    mainContainer.append(template_GenerateEditorForm(true, item));
}
function onClick_NextPage()
{
    if(activePageNumber < lastPageNumber) {
        render_Page(activePageNumber + 1, );
    }
}
function onClick_PreviousPage()
{
    if(activePageNumber > 0)
    {
        render_Page(activePageNumber -1);
    }
}
function onClick_ExcluirVarios()
{
    let selected = [];
    
    $.each($('.item-checkbox:checked'), function() {
        selected.push($(this).val());
    });
    
    if(selected.length === 0)
        {
            render_AlertError("Selecione algum item para deletar")
        }
    else 
        {
            ajax_DeleteManyAutomoveis(selected);
        }
}
function ajax_CreateAutomovel() 
{
    var data = {
        action: 'create',
        nome: $('#editor-form #nome').val(),
        descricao: $('#editor-form #descricao').val()
    }

    $.post("/v3/automoveis.php", data, function (result) {
        render_AlertSuccess("Automóvel criado com sucesso!");
        remove_EditorForm();
        ajax_GetAutomoveis('last');
    })
    .fail(function(error) {
        err = error.responseJSON;
        if(err === undefined) {
            err.message = "Erro desconhecido";
            err.code = "666";
        }
         render_AlertError("Não foi possível executar a operação ["+err.message+" | cód: "+err.code+"]");

         if(err.formErrors)
         update_EditorFormFieldErrors(err.formErrors);
    })
}
function onClick_MenuAdicionar()
{
    render_CreateForm();
}
function remove_EditorForm()
{
    $('#editor-form-wrapper').remove();
}
function update_PageButtoms()
{
    if(activePageNumber < 1) {previousButtom.toggleClass('disabled', true);}
    else {previousButtom.toggleClass('disabled', false);}

    if(activePageNumber < lastPageNumber) {nextButtom.toggleClass('disabled', false);}
    else {nextButtom.toggleClass('disabled', true);}
}
function template_GenerateTableItem(item) 
{
    return `<tr>
    <td class="td-select"><input type="checkbox" value="${item.id}" class="item-checkbox"></td>
    <td>${item.id}</td>
    <td>${item.placa}</td>
    <td>${item.preco}</td>
    <td>${item.ano_fabricacao}/${item.ano_modelo}</td>
    <td>${item.km}</td>
    <td>${item.nome_marca}</td>
    <td>
        <i class="material-icons clickable" onclick="onClick_EditAutomovel(${item.id})">edit</i>
        <i class="material-icons clickable" onclick="onClick_DeleteAutomovel(${item.id})">delete</i>
    </td>
    </tr>`;
}
function template_AlertSuccess(message) 
{
    lastAlertId++;
    return `<div id="alert-${lastAlertId}" class="alert alert-success alert-fixed" role="alert">
    <span class"alert-text">${message}</span>
    <i class="material-icons">done</i></div>`;
}
function template_AlertErro(message) 
{
    lastAlertId++;
    return `<div id="alert-${lastAlertId}" class="alert alert-warning alert-fixed" role="alert">
    <span class"alert-text">${message}</span>
    <i class="material-icons">error_outline</i></div>
    </div>`;
}
function template_GenerateEditorForm(isEditar, data)
{
    let item = (data != undefined) ? data[0] : undefined;
    let nome = (item != undefined) ? item.nome : '';
    let descricao = (item != undefined) ? item.descricao : '';
    let id = (item != undefined) ? item.id : '';
    let editar = (isEditar != undefined) ? isEditar : false;
    let method = editar ? "ajax_EditAutomovel("+id+")" : "ajax_CreateAutomovel()" ;
    let verb = editar ? "Editar" : "Criar";
    let panelTitle = verb + " automóvel";

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
                    <button type="button" class="btn btn-warning" onclick="onClick_CreateFormCancelar()">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="${method}">Salvar</button>
                </div>
                </form>
            </div>`
}
function render_AlertSuccess(msg) 
{
    $("body").append(template_AlertSuccess(msg));
        $("#alert-"+lastAlertId).delay(1500).fadeOut(1500, function(){
            $(this).remove();
        });
}
function render_AlertError(msg) 
{
    $("body").append(template_AlertErro(msg));
        $("#alert-"+lastAlertId).delay(1500).fadeOut(1500, function(){
            $(this).remove();
        });
}
function onClick_EditAutomovel(id) 
{
    render_EditForm(id);
}
function ajax_DeleteMarca(id) 
{
    $.post("/v3/automoveis.php", {'action': 'delete_one', 'id': id}, function (data) {
        remove_ItemFromList(id);
        render_AlertSuccess(data.message);
    })
    .fail(function(error) {
        renderAlertError("Não foi possível executar a operação ["+error.message+"| cód: "+error.code+"]");
    })
    .always(function() {
        update_reloadTable();
    })
}
function onClick_DeleteAutomovel(id) 
{
    ajax_DeleteMarca(id);
}
function update_reloadTable()
{
    render_Page(activePageNumber);
}
function remove_ItemFromList(id)
{
    allAutomoveis = allAutomoveis.filter(function(item) {
        return item.id != id;
    })
}
function onClick_CreateFormCancelar() 
{
    remove_EditorForm();
    render_Page(activePageNumber);
}
function update_EditorFormFieldErrors(data)
{
    if(data === undefined || data === null) return;
    if(data.nome)
    {

    }
}