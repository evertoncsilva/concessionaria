let original_allAutomoveis = null;
let activePageName      = 'automoveis';
let allAutomoveis       = [];
let pageSize            = 10;
let activePageNumber    = 0;
let automoveisCount     = 0;
let totalPagesCount     = 0;
let lastPageNumber      = 0;
let lastAlertId         = 0;
let mainContainer       = $("#main-container");
let componentes         = null;
let auto_componentes_ids = null;
let filtertext          = '';
const editorForm        = $('#editor-form');
const tablePaginator    = $('#tablePaginator');
const tablePanel        = $('#table-panel');
const targetTable       = $('#tableContent');
const checkboxAll       = $('#checkbox-select-all');

window.addEventListener("load", _initialize, true);

function onClick_clearFilter()
{
    filtertext = '';
    $('#filtertext').val('');
    $('#btn-clearfilter').addClass("noshow");
    ajax_getPage();
}
function render_Pagination()
{
    let maxLinksbefore = 3;
    let maxLinksafter = 3;
    
    let linkActive = activePageNumber;
    let template_linksbefore = "";
    let template_linksafter = "";
    let renderedBefore  = 0;
    let renderedAfter   = 0;
    //calcular quantidade de links antes da página atual ativa:
        for(let i = linkActive -1; i >= 0 && renderedBefore < maxLinksbefore ; i--)
        {
            template_linksbefore = `<li class="page-item"><a class="page-link" onclick="gotoPage(${i})">${i+1}</a></li>` + template_linksbefore;
            renderedBefore++;
        }
    //calcular quantidade de links depois da página atual ativa:
        for(let i = linkActive +1; i < totalPagesCount && renderedAfter < maxLinksafter ; i++)
        {
            template_linksafter += `<li class="page-item"><a class="page-link" onclick="gotoPage(${i})">${i+1}</a></li>`;
            renderedAfter++;
        }
    
    let template = `    <nav aria-label="Page navigation example">
                            <ul class="pagination">
                            <li class="page-item ${linkActive -1 >= 0 ? '' : 'disabled'}"><a class="page-link" onclick="gotoPage(${linkActive -1 >= 0 ? (linkActive -1) : 0})">Anterior</a></li>
                                ${template_linksbefore}
                            <li class="page-item active"><a class="page-link">${linkActive +1}</a></li>
                                ${template_linksafter}
                            <li class="page-item ${linkActive +2 <= totalPagesCount ? '' : 'disabled'}"><a class="page-link" onclick="gotoPage(${linkActive + 1 <= totalPagesCount ? (linkActive + 1) : 0})">Próximo</a></li>
                            </ul>
                        </nav>`
    tablePaginator.html(template);
}
function gotoPage(num)
{
    ajax_getPage(num);
}
function ajax_getPage(pageNum = 0, filter = filtertext)
{
    $.ajax({
        type: "GET",
        timeout: 1000,
        contentType: "application/json",
        cache: false,
        url: "/v3/automoveis.php?getpage",
        data: {page: pageNum, filter: filter},
        error: function() {
            alert("Erro ao buscar dados, tente novamente mais tarde!");
        },
        success: function(result) {
            activePageNumber = parseInt(result.currentpage);
            totalPagesCount = parseInt(result.totalpages);
            automoveisCount = parseInt(result.totalitems);
            allAutomoveis = result.data;
            $('#spinner').remove();
            if(result === null)
                {
                    render_AlertError("Nenhum automóvel encontrado!");
                }
            automoveisCount = result.totalitems ? result.totalitems : 0;
            totalPagesCount = result.totalpages ? result.totalpages : 0;
            render_Page(result.data);
        }
    });
}
function _initialize() 
{
    setActiveLinks(activePageName);
    ajax_getPage();

    $('#filtertext').keyup(function(){
        filtertext = this.value;
        if(this.value != '') {
            $('#btn-clearfilter').removeClass("noshow");
        }
        else {
            $('#btn-clearfilter').addClass("noshow");
            ajax_getPage();
        }
    });
    $('#filtertext').keypress(function(e) {
        if(e.which == 13) {
            if($('#filtertext').val != '') {
                ajax_getPage();
            }
        }
    });
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
            $('#spinner').remove();
            if(result === null)
            {
                render_AlertError("Nenhum automóvel encontrado!");
            }
            allAutomoveis = result || [];
            original_allAutomoveis = result;
            automoveisCount = allAutomoveis.length;
            totalPagesCount = Math.ceil(automoveisCount / pageSize);
            lastPageNumber = (totalPagesCount > 1) ? (totalPagesCount -1) : 0;
            render_Pagination();
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
            } catch (error) {
                alert("Erro ao buscar dados!\nEntre em contato conosco no e-mail: mail@mail.com");
            }
        }

    });
}
function getCheckedComponentes()
{
    let elements = document.getElementsByClassName('form-check-componente');
    let componentes = [];

    for (i in elements) {
        if(elements[i].checked)
            componentes.push(elements[i].value)
    }

    return componentes;
}
function ajax_CreateAutomovel() 
{
    reset_EditorFormValidation();
    var componentes = getCheckedComponentes();
    //TODO: EDITAR AUTOMOVEL
    var data = {
        action: 'create',
        descricao:          getVal_ByID('editor-form-descricao'),
        placa:              getVal_ByID('editor-form-placa'),
        renavam:            getVal_ByID('editor-form-renavam'),
        ano_modelo:         getVal_ByID('editor-form-ano_modelo'),
        ano_fabricacao:     getVal_ByID('editor-form-ano_fabricacao'),
        cor:                getVal_ByID('editor-form-cor'),
        km:                 getVal_ByID('editor-form-km'),
        marca_id:           getVal_ByID('editor-form-marca_id'),
        preco:              getVal_ByID('editor-form-preco'),
        preco_fipe:         getVal_ByID('editor-form-preco_fipe'),
        componentes_ids:    componentes

    }
    $.post("/v3/automoveis.php", data, function (result) {
        render_AlertSuccess("Automóvel criado com sucesso!");
        remove_EditorForm();
        ajax_getPage();
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
function ajax_EditAutomovel(id) 
{
    reset_EditorFormValidation();
    var componentes = getCheckedComponentes();
    //TODO: EDITAR AUTOMOVEL
    var data = {
        action: 'update',
        id:             id,
        descricao:          getVal_ByID('editor-form-descricao'),
        placa:              getVal_ByID('editor-form-placa'),
        renavam:            getVal_ByID('editor-form-renavam'),
        ano_modelo:         getVal_ByID('editor-form-ano_modelo'),
        ano_fabricacao:     getVal_ByID('editor-form-ano_fabricacao'),
        cor:                getVal_ByID('editor-form-cor'),
        km:                 getVal_ByID('editor-form-km'),
        marca_id:           getVal_ByID('editor-form-marca_id'),
        preco:              getVal_ByID('editor-form-preco'),
        preco_fipe:         getVal_ByID('editor-form-preco_fipe'),
        componentes_ids:    componentes

    }
    $.post("/v3/automoveis.php", data, function (result) {
        render_AlertSuccess("Automóvel editado com sucesso!");
        remove_EditorForm();
        ajax_getPage();
    })
    .fail(function(error) {
        err = error.responseJSON != undefined ? error.responseJSON : new Object();
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
    if(automoveisCount === 0)
        render_AlertError("Nenhum automóvel encontrado!");
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
        render_AlertError("Não foi possível executar a operação ["+error.message+"| cód: "+error.code+"]");
    })
    .always(function() {
        ajax_getPage();
    })
}
function render_Page(data) 
{
    toggle_TablePanel(true);
    render_Table(data);
    render_Pagination();
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
    set_FormMasks();
    
}
function render_EditForm(id)
{
    ajax_getThen_Populate_AllComponentes(id);
    toggle_TablePanel(false);
    let item = allAutomoveis.filter(x => x.id == id);
    mainContainer.append(template_GenerateEditorForm(true, item));
    ajax_GetMarcas_ThenPopulateEditorForm(item);
    set_FormMasks();
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
    $('#checkbox-select-all').prop("checked", false);
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
            let confirma = confirm("Tem certeza que deseja excluir estes itens?");
            if(confirma) ajax_DeleteManyAutomoveis(selected);
            else {
                render_AlertSuccess("Exclusão cancelada");
                $.each($('.item-checkbox:checked'), function() {
                    $(this).prop('checked', false);
                });
            }
        }
}
function onClick_MenuAdicionar()
{
    render_CreateForm();
    ajax_GetMarcas_ThenPopulateEditorForm();
    ajax_getAllComponentes_andDisplay();
}
function remove_EditorForm()
{
    $('#editor-form-wrapper').remove();
}
function template_GenerateTableItem(item) 
{
    let precofloat = parseFloat(item.preco);
    let kmFormatado = item.km.toLocaleString('br');
    let precoFormatado =  precofloat.toLocaleString('br', {minimumFractionDigits: 2, maximumFractionDigits: 2})
    let placaFormatada = item.placa.toUpperCase();
    placaFormatada = placaFormatada.slice(0,3) + "-" + placaFormatada.slice(3);
    return `<tr>
    <td class="td-select"><input type="checkbox" value="${item.id}" class="item-checkbox"></td>
    <td class="clickable" onclick="onClick_EditAutomovel(${item.id})">${item.id}</td>
    <td class="clickable" onclick="onClick_EditAutomovel(${item.id})">${item.descricao}</td>
    <td class="clickable" onclick="onClick_EditAutomovel(${item.id})">R$ ${precoFormatado}</td>
    <td class="clickable" onclick="onClick_EditAutomovel(${item.id})">${item.ano_fabricacao}/${item.ano_modelo}</td>
    <td class="clickable" onclick="onClick_EditAutomovel(${item.id})">${kmFormatado}</td>
    <td class="clickable" onclick="onClick_EditAutomovel(${item.id})">${placaFormatada}</td>
    <td class="clickable" onclick="onClick_EditAutomovel(${item.id})">${item.nome_marca}</td>
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
    let item                = data && data[0]               || undefined;
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
                          <label for="editor-form-descricao">Descrição do automóvel</label>
                          <input type="text" class="form-control" name="descricao" id="editor-form-descricao" placeholder="Insira uma descrição para seu automóvel" value="${descricao}">
                      </div>
                  </div>
    
                  <div class="col-md-3">
                      <div class="form-group">
                          <label class="control-label" for="editor-form-placa">Placa</label>
                          <input type="text" class="form-control" name="placa" id="editor-form-placa" placeholder="XXX1234" value="${placa}" maxlength="8">
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
                <hr>
                <h4>Componentes adicionais:</h4>
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
        let err = error.responseJSON;
        render_AlertError("Não foi possível executar a operação ["+err.message+"| cód: "+err.code+"]");
    })
    .always(function() {
        update_reloadTable();
    })
}
function onClick_DeleteAutomovel(id) 
{
    let confirma = confirm("Tem certeza que deseja excluir este item?");
    if(confirma) ajax_DeleteMarca(id);
}
function update_reloadTable()
{
    ajax_getPage(activePageNumber);
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
    ajax_getPage(activePageNumber);
}
function update_EditorFormFieldErrors(data)
{
    console.log(data);
    data.forEach(data => render_InvalidFormFeedback(data));
}
function template_EditorFormComponentes(compos) 
{
    var temp = "";

    if(compos.length > 0 )
    {
        compos.forEach(comp => {

        temp += `<div class="form-check-inline">
                        <input type="checkbox" value="${comp.id}" class="form-check-componente" name="${comp.id}" id="checkbox-componente-${comp.id}">
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
function ajax_getAllComponentes_andDisplay()
{
    componentes = [];
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
            template = template_EditorFormComponentes(componentes);
            $('#componentes-area').append(template);
            
        }
    });
}
function ajax_getThen_Populate_AllComponentes(id)
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
            ajax_getThen_Populate_AutomovelComponentesId(id)
        }
    });
}
function ajax_getThen_Populate_AutomovelComponentesId(id)
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
                    <input type="checkbox" ${checked ? 'checked' : ''} class="form-check-componente" name="componente" id="checkbox-componente-${comp.id}" value="${comp.id}">
                    <label for="checkbox-componente-${comp.id}" class="form-check-label">${comp.nome}</label>
                </div>`;

        $('#componentes-area').append(temp);
    })

}
function reset_EditorFormValidation(formErrors)
{   
    $('.invalid-feedback .valid-feedback').each(function (item) {
        item.empty();
    });
    $('.is-valid').each(function (item) {$(this).toggleClass('is-valid', false)});
    $('.is-invalid').each(function (item) {$(this).toggleClass('is-invalid', false)});
    $('.invalid-feedback, .valid-feedback').remove();
}
function render_InvalidFormFeedback(item) 
{
    let nomecampo = item.campo;
    let feedback_div = `<div class="invalid-feedback">${item.msg}</div>`;
    $('#editor-form-'+nomecampo).toggleClass('is-invalid', true);
    $(feedback_div).insertAfter('#editor-form-'+nomecampo);
}
function set_FormMasks()
{
    $('#editor-form-placa').mask('AAA-0000');
    $('#editor-form-renavam').mask('00000000000');
    $('#editor-form-preco').mask('000.000,00', {reverse: true});
    $('#editor-form-preco_fipe').mask('000.000,00', {reverse: true});
    $('#editor-form-km').mask('000.000', {reverse: true});
}