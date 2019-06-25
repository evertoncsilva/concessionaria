let original_allAutomoveis = null;
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
let componentes         = null;
let auto_componentes_ids = null;
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
            original_allAutomoveis = result;
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
                $(document).trigger("loadtable");
            } catch (error) {
                alert("Erro ao buscar dados!\nEntre em contato conosco no e-mail: mail@mail.com");
            }
        }

    });
}
function ajax_EditAutomovel(id) 
{

    //TODO: EDITAR AUTOMOVEL
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
    //$('#table-itemcount').append(allAutomoveis.length + 'Itens');
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
    ajax_getAllComponentes(id);
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
    ajax_GetMarcas_ThenPopulateEditorForm();
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
    <td>${item.descricao}</td>
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
    let item                = data && data[0]       || undefined;
    let id                  = item && item.id               || '';
    let descricao           = item && item.descricao        || '';
    let placa               = item && item.placa            || '';
    let renavam             = item && item.renavam          || '';
    let ano_modelo          = item && item.ano_modelo       || '';
    let ano_fabricacao      = item && item.ano_fabricacao   || '';
    let cor                 = item && item.cor              || '';
    let km                  = item && item.km               || '';
    let marca_id            = item && item.marca_id         || '';
    let preco               = item && item.preco            || '';
    let preco_fipe          = item && item.preco_fipe       || '';
    let editar = isEditar || false;

    let method = editar ? "ajax_EditAutomovel("+id+")" : "ajax_CreateAutomovel()" ;
    let verbo = editar ? "Editar" : "Criar";
    let panelTitle = verbo + " automóvel";

    return `<!-- form -->
    <div id="editor-form-wrapper" class="card">
        <div class="card-header">
          <h4 class="panel-title">${verbo} automóvel</h4>
        </div>
        <form id="editor-form" action="">
        <input type="hidden" name="id" value="1">
          <div class="container-fluid">
                <!-- ROW -->
                <div class="row">
                  <div class="col-md-6">
                      <div class="form group">
                          <label for="edito-form-descricao">Descrição do automóvel</label>
                          <input type="text" class="form-control" name="descricao" id="edito-form-descricao" placeholder="Insira uma descrição para seu automóvel" value="${descricao}">
                      </div>
                  </div>
    
                  <div class="col-md-3">
                      <div class="form-group">
                          <label class="control-label" for="editor-form-nome">Placa</label>
                          <input type="text" class="form-control" name="placa" id="editor-form-nome" placeholder="XXX1234" value="${placa}" maxlength="7">
                      </div>  
                  </div>
    
                  <div class="col-md-3">
                      <div class="form-group">
                          <label class="control-label" for="editor-form-renavam">Código RENAVAM</label>
                          <input type="text" class="form-control" name="renavam" id="editor-form-renavam" placeholder="00000000000" value="${renavam}" maxlength="11">
                      </div>  
                  </div>
                </div>
                <!-- ENDROW -->
                <!-- ROW -->
                <div class="row">
                  <div class="col-md-2">
                      <div class="form group" data-group="descricao">
                          <label for="editor-form-ano_modelo">Ano Modelo</label>
                          <select name="ano_modelo" id="editor-form-ano_modelo" class="form-control">
                            <!-- ANO MODELO OPTIONS -->
                            ${template_YearOptions(ano_modelo, 'modelo')}
                          </select>
                      </div>
                  </div>
    
                  <div class="col-md-2">
                      <div class="form-group">
                          <label class="control-label" for="editor-form-ano_fabricacao">Ano Fabricação</label>
                          <select name="ano_fabricacao" id="editor-form-ano_fabricacao" class="form-control">
                            <!-- ANO FABRICACAO OPTIONS -->
                            ${template_YearOptions(ano_fabricacao)}
                          </select>
                      </div>  
                  </div>
    
                  <div class="col-md-2">
                      <div class="form-group">
                          <label class="control-label" for="editor-form-cor">Cor</label>
                          <input type="text" class="form-control" name="cor" id="editor-form-cor" placeholder="Cor" value="${cor}" maxlength="20">
                      </div>  
                  </div>
                  <div class="col-md-2">
                      <div class="form-group">
                          <label class="control-label" for="editor-form-km">KM</label>
                          <input type="text" class="form-control" name="km" id="editor-form-km" placeholder="..." value="${km}" maxlength="7">
                      </div>  
                  </div>
                  <div class="col-md-2">
                      <div class="form-group">
                          <label class="control-label" for="editor-form-marca_id">Marca</label>
                          <select name="marca_id" id="editor-form-marca_id" class="form-control">
                            <!-- MARCA ID OPTIONS -->
                          </select>
                      </div>  
                  </div>
                </div>
                <!-- ENDROW -->
                <!-- ROW -->
                <div class="row">
                  <div class="col-md-2">
                      <div class="form-group">
                          <label for="editor-form-preco">Preço</label>
                          <input type="text" class="form-control" name="preco" id="editor-form-preco" placeholder="Preço" value="${preco}">
                      </div>
                  </div>
    
                  <div class="col-md-2">
                      <div class="form-group">
                          <label class="control-label" for="editor-form-preco_fipe">Preço FIPE</label>
                          <input type="text" class="form-control" name="preco_fipe" id="editor-form-preco_fipe" placeholder="Preço FIPE" value="${preco_fipe}" maxlength="40">
                      </div>  
                  </div>
                </div>
                <!-- ENDROW -->
                <h4>Componentes adicionais</h4>
                <hr>
                <div id="componentes-area">
                <!-- COMPONENTES CHECKBOXES -->
                </div>
                
                <div class="row editor-button-container">
                    <button type="reset" class="btn btn-default">Limpar</button>
                    <button type="button" class="btn btn-warning" onclick="onClick_CreateFormCancelar()">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="${method}">Salvar</button>
                </div>
                  
    
          </div>  
        </form>
                
    </div>
    <!-- end form -->`
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
function update_EditorFormValidation(info) 
{
    //TODO: VALIDAR EDITOR FORM
        //TODO: ano modelo > ano fab?

    // receber os formErrors do servidor e atualizar o form com os erros
    // e as mensagens recebidas de cada campo
    // info[formErrors] 
    //          ['nomecampo' => 'mensagem de erro do campo']
}
function template_EditorFormComponentes(compos) 
{
    var temp = "";

    if(compos.length > 0 )
    {
        CompositionEvent.forEach(comp => {

        temp += `<div class="form-check-inline">
                        <input type="checkbox" value="${comp.id}" class="form-check-input checkbox-componente" name="${comp.id}" id="checkbox-componente-${comp.id}">
                        <label for="checkbox-componente-${comp.id}" class="form-check-label">${comp.nome}</label>
                    </div>
                    `;
        });
    }
    
    return temp;
}
function template_YearOptions(selected, tipo)
{
    let thisYear = new Date().getFullYear();
    let start = 1900;
    let end = thisYear;
    let options = "";
        // anos p/ modelo
        if(tipo == 'modelo')
            end = thisYear +1;
    
        for(let i = end; i >= start; i--)
        {
            options +=   `<option value="${i}" ${i === selected ? 'selected' : ''}>${i}</option>
                        `;
        }
    return options;
}

function ajax_GetMarcas_ThenPopulateEditorForm(auto)
{
    let item = auto && auto[0] || -1;
    let marcas = [];
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
            marcas = result;
            marcas.sort((a, b) => { 
                if (a.nome < b.nome) return -1;
                else if(a.nome > b.nome) return 1;
                else return 0;
            });
            marcas.forEach(marca => {
                let template =  `<option value="${marca.id}" ${marca.id === item.marca_id ? 'selected' : ''}>${marca.nome}</option>
                                `;
                $('#editor-form-marca_id').append(template);
            });
        }
    });
}
function ajax_getAllComponentes(id)
{
    $.ajax({
        type: "GET",
        timeout: 5000,
        contentType: "application/json",
        cache: true,
        url: "/v3/componentes.php?all",
        error: function() {
            alert("Erro ao buscar dados dos componentes, tente novamente mais tarde!");
        },
        success: function(result) {
            componentes = result;
            ajax_getAutomovelComponentesId(id)
        }
    });
}
function ajax_getAutomovelComponentesId(id)
{
    let ids = [];
    $.ajax({
        type: "GET",
        timeout: 5000,
        contentType: "applixation/json",
        cache: false,
        url: "/v3/automoveis.php?get_componentes&id=" + id,
        success: function(result) {
            auto_componentes_ids = result;
            populate_CrossReferenceAutomovelComponente(auto_componentes_ids, componentes);
        }
    });

}
function populate_CrossReferenceAutomovelComponente(auto_componentes, componentes)
{
    let temp = "";

    componentes.forEach(comp => {
        let checked = false;
        
        if(auto_componentes.find(x => x.id === comp.id))
            checked = true;
        
        
        temp = `<div class="form-check-inline">
                    <input type="checkbox" ${checked ? 'checked' : ''} class="form-check-input" name="componente" id="checkbox-componente-10" value="${comp.id}">
                    <label for="checkbox-componente-10" class="form-check-label">${comp.nome}</label>
                </div>`;

        $('#componentes-area').append(temp);
    })

}
function update_FilterTable(value) 
{
    if(value === null || value === "")
        allAutomoveis = original_allAutomoveis;
    else 
    {
        allAutomoveis = original_allAutomoveis.filter(auto => {
            return auto.descricao.contains(value) || auto.nome_marca.contains(value);
        });
    }
    render_Page(0);
}

$(document).on("loadtable", function() {
    $('#filtertext').on('keyup', function() {
        if(this.value.length < 1)
            {
                allAutomoveis = original_allAutomoveis;
                render_Page(0);
            }
        else 
            {
                let value = this.value.toLowerCase();
                allAutomoveis = original_allAutomoveis.filter(auto => {
                    auto.descricao = auto.descricao     || '';
                    auto.nome_marca = auto.nome_marca   || '';

                    return auto.descricao.toLowerCase().includes(value) || auto.nome_marca.toLowerCase().includes(value);
                }); 
                render_Page(0);
            }
            
    });
});

