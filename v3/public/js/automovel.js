var originalAllAutomoveis = null;
var activePageName = 'automoveis';
var allAutomoveis = [];
var pageSize = 10;
var activePageNumber = 0;
var automoveisCount = 0;
var totalPagesCount = 0;
var lastPageNumber = 0;
var lastAlertId = 0;
var mainContainer = $("#main_container");
var componentes = null;
var autoComponentesIds = null;
var filtertext = '';
var editorForm = $('#editor_form');
var table_paginator = $('#table_paginator');
var tablePanel = $('#table_panel');
var targetTable = $('#tableContent');
var checkboxAll = $('#checkbox-select-all');

window.addEventListener("load", _initialize, true);

function executeRouteCalls(verb) {
    switch(verb) {
        case 'editar':
                routeCallEditar(id);
            break;
    }
}
function locationHashChanged() {
    removeEditorForm();
    toggleTablePanel(false);
    var hash = window.location.hash.split('/');
    // processar caminhos por hash

    if(hash[1] === 'editar' && !isNaN(hash[2])) {
        var id = parseInt(hash[2]);
        renderEditForm(id);
    }
    else if(hash[1] === 'listar' && !isNaN(hash[2])) {
        var pageNum = parseInt(hash[2]) -1;
        ajaxGetPage(pageNum);
    }
    else if(hash[1] === 'adicionar') {
        renderFormAdicionar();
    }
    else {
        location.hash = '/listar/1';
    }
}
function onClickClearFilter() {
    filtertext = '';
    $('#filtertext').val('');
    $('#btn-clearfilter').addClass("noshow");
    ajaxGetPage();
}
function renderPagination() {
    var maxLinksbefore = 99999;
    var maxLinksafter = 99999;
    
    var linkActive = activePageNumber;
    var template_linksbefore = "";
    var template_linksafter = "";
    var renderedBefore  = 0;
    var renderedAfter   = 0;
    //calcular quantidade de links antes da página atual ativa:
        for (var i = linkActive -1; i >= 0 && renderedBefore < maxLinksbefore ; i--) {
            template_linksbefore = `<li class="page-item"><a class="page-link" onclick="gotoPage(${i})">${i+1}</a></li>` + template_linksbefore;
            renderedBefore++;
        }
    //calcular quantidade de links depois da página atual ativa:
        for (var i = linkActive +1; i < totalPagesCount && renderedAfter < maxLinksafter ; i++) {
            template_linksafter += `<li class="page-item"><a class="page-link" onclick="gotoPage(${i})">${i+1}</a></li>`;
            renderedAfter++;
        }
    
    var template = `    <nav aria-label="Page navigation example">
                            <ul class="pagination">
                            <li class="page-item ${linkActive -1 >= 0 ? '' : 'disabled'}"><a class="page-link" onclick="gotoPage(${linkActive -1 >= 0 ? (linkActive -1) : 0})">Anterior</a></li>
                                ${template_linksbefore}
                            <li class="page-item active"><a class="page-link">${linkActive +1}</a></li>
                                ${template_linksafter}
                            <li class="page-item ${linkActive +2 <= totalPagesCount ? '' : 'disabled'}"><a class="page-link" onclick="gotoPage(${linkActive + 1 <= totalPagesCount ? (linkActive + 1) : 0})">Próximo</a></li>
                            </ul>
                        </nav>`
    table_paginator.html(template);
}
function gotoPage(num) {
    var pageNum = parseInt(num) +1;
    checkboxAll.prop('checked', false);
    location.hash = '/listar/'+ pageNum;
}
function ajaxGetPage(pageNum = 0, filter = filtertext) {
    $.ajax({
        type: 'GET',
        timeout: 1000,
        contentType: 'application/json',
        cache: false,
        url: '/v3/automoveis.php?getpage',
        data: {page: pageNum, filter: filter},
        error: function() {
            alert('Erro ao buscar dados, tente novamente mais tarde!');
        },
        success: function(result) {
            activePageNumber = parseInt(result.currentpage);
            totalPagesCount = parseInt(result.totalpages);
            automoveisCount = parseInt(result.totalitems);
            allAutomoveis = result.data;
            $('#spinner').remove();
            if(result === null) {
                    renderAlertError('Nenhum automóvel encontrado!');
            }
            automoveisCount = result.totalitems ? result.totalitems : 0;
            totalPagesCount = result.totalpages ? result.totalpages : 0;
            renderPage(result.data);
        }
    });
}
function _initialize() {
    locationHashChanged();
    setActiveLinks(activePageName);
    

    $('#filtertext').on('keyup', function() {
        filtertext = this.value;
        if(this.value != '') {
            $('#btn-clearfilter').removeClass("noshow");
        }
        else {
            $('#btn-clearfilter').addClass("noshow");
            ajaxGetPage();
        }
    });
    $('#filtertext').on('keypress', function(e) {
        if(e.which == 13) {
            if($('#filtertext').val != '') {
                ajaxGetPage();
            }
        }
    });

   
    window.addEventListener("hashchange", locationHashChanged, true);
}
function getCheckedComponentes() {
    var elements = document.getElementsByClassName('form-check-componente');
    var componentes = [];

    for (i in elements) {
        if(elements[i].checked)
            componentes.push(elements[i].value)
    }

    return componentes;
}
function ajaxCreateAutomovel() {
    resetEditorFormValidation();
    var componentes = getCheckedComponentes();
    //TODO: EDITAR AUTOMOVEL
    var data = {
        action: 'create',
        descricao: getValByID('editor_form-descricao'),
        placa: getValByID('editor_form-placa'),
        renavam: getValByID('editor_form-renavam'),
        ano_modelo: getValByID('editor_form-ano_modelo'),
        ano_fabricacao: getValByID('editor_form-ano_fabricacao'),
        cor: getValByID('editor_form-cor'),
        km: getValByID('editor_form-km'),
        marca_id: getValByID('editor_form-marca_id'),
        preco: getValByID('editor_form-preco'),
        preco_fipe: getValByID('editor_form-preco_fipe'),
        componentes_ids: componentes

    }
    $.post('/v3/automoveis.php', data, function (result) {
        renderAlertSuccess('Automóvel criado com sucesso!');
        removeEditorForm();
        ajaxGetPage();
    })
    .fail(function(error) {
        err = error.responseJSON;
        if(err === undefined) {
            err.message = 'Erro desconhecido';
            err.code = '666';
        }
         renderAlertError('Não foi possível executar a operação ['+err.message+' | cód: '+err.code+']');

         if (err.formErrors) {
             updateEditorFormFieldErrors(err.formErrors);
         }
    })

}
function ajaxEditAutomovel(id) {
    resetEditorFormValidation();
    var componentes = getCheckedComponentes();
    //TODO: EDITAR AUTOMOVEL
    var data = {
        action: 'update',
        id: id,
        descricao: getValByID('editor_form-descricao'),
        placa: getValByID('editor_form-placa'),
        renavam: getValByID('editor_form-renavam'),
        ano_modelo: getValByID('editor_form-ano_modelo'),
        ano_fabricacao: getValByID('editor_form-ano_fabricacao'),
        cor: getValByID('editor_form-cor'),
        km: getValByID('editor_form-km'),
        marca_id: getValByID('editor_form-marca_id'),
        preco: getValByID('editor_form-preco'),
        preco_fipe: getValByID('editor_form-preco_fipe'),
        componentes_ids: componentes
    }
    $.post('/v3/automoveis.php', data, function (result) {
        renderAlertSuccess('Automóvel editado com sucesso!');
        removeEditorForm();
        location.hash = '/listar/1';
    })
    .fail(function(error) {
        err = error.responseJSON != undefined ? error.responseJSON : new Object();
        if(err === undefined) {
            err.message = 'Erro desconhecido';
            err.code = '666';
        }
         renderAlertError('Não foi possível executar a operação ['+err.message+' | cód: '+err.code+']');
         if(err.formErrors)
         updateEditorFormFieldErrors(err.formErrors);
    })

}
function renderTable(itens) {
    targetTable.empty();
    if (automoveisCount === 0) {
        renderAlertError('Nenhum automóvel encontrado!');
    }
    itens.forEach(function(item) {
        targetTable.append(templateGenerateTableItem(item));
    });
}
function ajaxDeleteManyAutomoveis(comps) {
    var actualPage = activePageNumber;
    $.post("/v3/automoveis.php", {'action': 'delete_many', 'items': comps}, function (data) {
        renderAlertSuccess(data.message);
    })
    .fail(function(error) {
        renderAlertError('Não foi possível executar a operação ['+error.message+'| cód: '+error.code+']');
    })
    .always(function() {
        ajaxGetPage();
    })
}
function renderPage(data) {
    toggleTablePanel(true);
    renderTable(data);
    renderPagination();
    $('#table-itemcount').empty();
    //$('#table-itemcount').append(allAutomoveis.length + 'Itens');
}
function toggleTablePanel(val) {
    tablePanel.toggleClass('noshow', !val);
    table_paginator.toggleClass('noshow', !val);
}
function toggleSelectAll(val) {
    var toggleVal;
    if (val != undefined) {
     toggleVal = val;
     checkboxAll.prop('checked', val);
    }
    else {
        toggleVal = checkboxAll.prop('checked');
    }
    $('.item-checkbox').prop('checked', toggleVal);
}
function renderCreateForm() {
    if($('#editor_form-wrapper').length) return;
    toggleTablePanel(false);
    mainContainer.append(templateGenerateEditorForm());
    setFormMasks();
    
}
function renderEditForm(id) {
    removeEditorForm();
    ajaxGetAutomovel(id, function(automovel) {
        ajaxGetThenPopulateAllComponentes(id);
        var editorformtemplate = templateGenerateEditorForm(true, automovel)
        mainContainer.append(editorformtemplate);
        
        toggleTablePanel(false);
        ajaxGetMarcasThenPopulateEditorForm(automovel);
        setFormMasks();
    });
}
function ajaxGetAutomovel(id, callback) {
    var automovel;
    $.get("/v3/automoveis.php?getAutomovel", {'id': id}, function (data) {
        automovel = data;
        return callback(automovel);
    })
    .fail(function(error) {
        let err = error.responseJSON;
        renderAlertError(err.message);
        ajaxGetPage();
    })
    
}
function onClickNextPage() {
    if(activePageNumber < lastPageNumber) {
        renderPage(activePageNumber + 1, );
    }
}
function onClickPreviousPage() {
    if(activePageNumber > 0) {
        renderPage(activePageNumber -1);
    }
}
function onClickExcluirVarios() {
    $('#checkbox-select-all').prop("checked", false);
    var selected = [];
    $.each($('.item-checkbox:checked'), function() {
        selected.push($(this).val());
    });
    if (selected.length === 0) {
        renderAlertError("Selecione algum item para deletar")
    }
    else {
        var confirma = confirm("Tem certeza que deseja excluir estes itens?");
        if (confirma) {
            ajaxDeleteManyAutomoveis(selected);
        } 
        else {
            renderAlertSuccess("Exclusão cancelada");
            $.each($('.item-checkbox:checked'), function() {
                $(this).prop('checked', false);
            });
        }
    }
}
function onClickMenuAdicionar() {
    var hash = window.location.hash.split('/');
    if (hash[1] === 'adicionar') {
        return
    }
    else {
        location.hash = '/adicionar';
    }
}
function renderFormAdicionar() {
        renderCreateForm();
        ajaxGetMarcasThenPopulateEditorForm();
        ajaxGetAllComponentesAndDisplay();
}
function removeEditorForm() {
    $('#editor_form-wrapper').remove();
}
function templateGenerateTableItem(item) {
    var precofloat = parseFloat(item.preco);
    var kmFormatado = item.km.toLocaleString('br');
    var precoFormatado =  precofloat.toLocaleString('br', {minimumFractionDigits: 2, maximumFractionDigits: 2})
    var placaFormatada = item.placa.toUpperCase();
    placaFormatada = placaFormatada.slice(0,3) + "-" + placaFormatada.slice(3);
    return `<tr>
        <td class="td-select"><input type="checkbox" value="${item.id}" class="item-checkbox"></td>
        <a href="#edit/25">
            <td class="clickable" onclick="onClickEditAutomovel(${item.id})">${item.id}</td>
            <td class="clickable" onclick="onClickEditAutomovel(${item.id})">${item.descricao}</td>
            <td class="clickable" onclick="onClickEditAutomovel(${item.id})">R$ ${precoFormatado}</td>
            <td class="clickable" onclick="onClickEditAutomovel(${item.id})">${item.ano_fabricacao}/${item.ano_modelo}</td>
            <td class="clickable" onclick="onClickEditAutomovel(${item.id})">${kmFormatado}</td>
            <td class="clickable" onclick="onClickEditAutomovel(${item.id})">${placaFormatada}</td>
            <td class="clickable" onclick="onClickEditAutomovel(${item.id})">${item.nome_marca}</td>
            <td>
                <i class="material-icons clickable" onclick="onClickEditAutomovel(${item.id})">edit</i>
        </a>
                <i class="material-icons clickable" onclick="onClickDeleteAutomovel(${item.id})">delete</i>
            </td>
    </tr>`;
}
function templateAlertSuccess(message) {
    lastAlertId++;
    return `<div id="alert-${lastAlertId}" class="alert alert-success alert-fixed" role="alert">
    <span class"alert-text">${message}</span>
    <i class="material-icons">done</i></div>`;
}
function templateAlertErro(message) {
    lastAlertId++;
    return `<div id="alert-${lastAlertId}" class="alert alert-warning alert-fixed" role="alert">
    <span class"alert-text">${message}</span>
    <i class="material-icons">error_outline</i></div>
    </div>`;
}
function templateGenerateEditorForm(isEditar, data) {
    var editar = isEditar || false;
    var item = data || undefined;
    var ano_fabricacao = item && item.ano_fabricacao || '';
    var ano_modelo = item && item.ano_modelo || '';
    var preco_fipe = item && item.preco_fipe || '';
    var descricao = item && item.descricao || '';
    var marca_id = item && item.marca_id || '';
    var renavam = item && item.renavam || '';
    var placa = item && item.placa || '';
    var preco = item && item.preco || '';
    var cor = item && item.cor || '';
    var id = item && item.id || '';
    var km = item && item.km || '';

    var method = editar ? "ajaxEditAutomovel("+id+")" : "ajaxCreateAutomovel()" ;
    var verbo = editar ? "Editar" : "Criar";
    var panelTitle = verbo + " automóvel";

    return `<!-- form -->
    <div id="editor_form-wrapper" class="card">
        <div class="card-header">
          <h4 class="panel-title">${verbo} automóvel</h4>
        </div>
        <form id="editor_form" action="">
        <input type="hidden" name="id" value="1">
          <div class="container-fluid">
                <!-- ROW -->
                <div class="row">
                  <div class="col-md-6">
                      <div class="form group">
                          <label for="editor_form-descricao">Descrição do automóvel</label>
                          <input type="text" class="form-control" name="descricao" id="editor_form-descricao" placeholder="Insira uma descrição para seu automóvel" value="${descricao}">
                      </div>
                  </div>
    
                  <div class="col-md-3">
                      <div class="form-group">
                          <label class="control-label" for="editor_form-placa">Placa</label>
                          <input type="text" class="form-control" name="placa" id="editor_form-placa" placeholder="XXX1234" value="${placa}" maxlength="8">
                      </div>  
                  </div>
    
                  <div class="col-md-3">
                      <div class="form-group">
                          <label class="control-label" for="editor_form-renavam">Código RENAVAM</label>
                          <input type="text" class="form-control" name="renavam" id="editor_form-renavam" placeholder="00000000000" value="${renavam}" maxlength="11">
                      </div>  
                  </div>
                </div>
                <!-- ENDROW -->
                <!-- ROW -->
                <div class="row">
                  <div class="col-md-2">
                      <div class="form group" data-group="descricao">
                          <label for="editor_form-ano_modelo">Ano Modelo</label>
                          <select name="ano_modelo" id="editor_form-ano_modelo" class="form-control">
                            <!-- ANO MODELO OPTIONS -->
                            ${templateYearOptions(ano_modelo, 'modelo')}
                          </select>
                      </div>
                  </div>
    
                  <div class="col-md-2">
                      <div class="form-group">
                          <label class="control-label" for="editor_form-ano_fabricacao">Ano Fabricação</label>
                          <select name="ano_fabricacao" id="editor_form-ano_fabricacao" class="form-control">
                            <!-- ANO FABRICACAO OPTIONS -->
                            ${templateYearOptions(ano_fabricacao)}
                          </select>
                      </div>  
                  </div>
    
                  <div class="col-md-2">
                      <div class="form-group">
                          <label class="control-label" for="editor_form-cor">Cor</label>
                          <input type="text" class="form-control" name="cor" id="editor_form-cor" placeholder="Cor" value="${cor}" maxlength="20">
                      </div>  
                  </div>
                  <div class="col-md-2">
                      <div class="form-group">
                          <label class="control-label" for="editor_form-km">KM</label>
                          <input type="text" class="form-control" name="km" id="editor_form-km" placeholder="..." value="${km}" maxlength="7">
                      </div>  
                  </div>
                  <div class="col-md-2">
                      <div class="form-group">
                          <label class="control-label" for="editor_form-marca_id">Marca</label>
                          <select name="marca_id" id="editor_form-marca_id" class="form-control">
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
                          <label for="editor_form-preco">Preço</label>
                          <input type="text" class="form-control" name="preco" id="editor_form-preco" placeholder="Preço" value="${preco}">
                      </div>
                  </div>
    
                  <div class="col-md-2">
                      <div class="form-group">
                          <label class="control-label" for="editor_form-preco_fipe">Preço FIPE</label>
                          <input type="text" class="form-control" name="preco_fipe" id="editor_form-preco_fipe" placeholder="Preço FIPE" value="${preco_fipe}" maxlength="40">
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
                    ${!isEditar ? '<button type="reset" class="btn btn-default">Limpar</button>' : ''}
                    <button type="button" class="btn btn-warning" onclick="onClickCreateFormCancelar()">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="${method}">Salvar</button>
                </div>
                  
    
          </div>  
        </form>
                
    </div>
    <!-- end form -->`
}
function renderAlertSuccess(msg) {
    $("body").append(templateAlertSuccess(msg));
        $("#alert-"+lastAlertId).delay(1500).fadeOut(1500, function(){
            $(this).remove();
        });
}
function renderAlertError(msg) {
    $("body").append(templateAlertErro(msg));
        $("#alert-"+lastAlertId).delay(1500).fadeOut(1500, function(){
            $(this).remove();
        });
}
function onClickEditAutomovel(id) {
    location.hash = '/editar/'+id;
    //renderEditForm(id);
}
function ajaxDeleteMarca(id) {
    $.post("/v3/automoveis.php", {'action': 'delete_one', 'id': id}, function (data) {
        removeItemFromList(id);
        renderAlertSuccess(data.message);
    })
    .fail(function(error) {
        var err = error.responseJSON;
        renderAlertError("Não foi possível executar a operação ["+err.message+"| cód: "+err.code+"]");
    })
    .always(function() {
        updateReloadTable();
    })
}
function onClickDeleteAutomovel(id) {
    var confirma = confirm("Tem certeza que deseja excluir este item?");
    if(confirma) ajaxDeleteMarca(id);
}
function updateReloadTable() {
    ajaxGetPage(activePageNumber);
}
function removeItemFromList(id) {
    allAutomoveis = allAutomoveis.filter(function(item) {
        return item.id != id;
    })
}
function onClickCreateFormCancelar() {
    var pageNum = parseInt(activePageNumber) +1;
    location.hash = '/listar/'+pageNum;
    // removeEditorForm();
    // ajaxGetPage(activePageNumber);
}
function updateEditorFormFieldErrors(data) {
    console.log(data);
    data.forEach(data => renderInvalidFormFeedback(data));
}
function templateEditorFormComponentes(compos) {
    var temp = "";

    if (compos.length > 0 ) {
        compos.forEach(comp => {

        temp += `<div class="form-check-inline">
                        <input type="checkbox" value="${comp.id}" class="form-check-componente" name="${comp.id}" id="checkbox-componente-${comp.id}">
                        <label for="checkbox-componente-${comp.id}" class="form-check-label">${comp.nome}</label>
                </div>`;
        });
    }
    return temp;
}
function templateYearOptions(selected, tipo) {
    var thisYear = new Date().getFullYear();
    var start = 1900;
    var end = thisYear;
    var options = "";
        // anos p/ modelo
        if(tipo == 'modelo') {
            end = thisYear +1;
        }
        for(var i = end; i >= start; i--) {
            options +=   `<option value="${i}" ${i === selected ? 'selected' : ''}>${i}</option>`;
        }
    return options;
}
function ajaxGetMarcasThenPopulateEditorForm(auto) {
    var item = auto && auto[0] || -1;
    var marcas = [];
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
                var template =  `<option value="${marca.id}" ${marca.id === item.marca_id ? 'selected' : ''}>${marca.nome}</option>
                                `;
                $('#editor_form-marca_id').append(template);
            });
        }
    });
}
function ajaxGetAllComponentesAndDisplay() {
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
            template = templateEditorFormComponentes(componentes);
            $('#componentes-area').append(template);
            
        }
    });
}
function ajaxGetThenPopulateAllComponentes(id) {
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
            ajaxGetThenPopulateAutomovelComponentesId(id)
        }
    });
}
function ajaxGetThenPopulateAutomovelComponentesId(id) {
    var ids = [];
    $.ajax({
        type: "GET",
        timeout: 5000,
        contentType: "applixation/json",
        cache: false,
        url: "/v3/automoveis.php?get_componentes&id=" + id,
        success: function(result) {
            autoComponentesIds = result;
            populateCrossReferenceAutomovelComponente(autoComponentesIds, componentes);
        }
    });
}
function populateCrossReferenceAutomovelComponente(auto_componentes, componentes) {
    var temp = "";
    componentes.forEach(comp => {
        var checked = false;
        if (auto_componentes.find(x => x.id === comp.id)) {
            checked = true;
        }        
        temp = `<div class="form-check-inline">
                    <input type="checkbox" ${checked ? 'checked' : ''} class="form-check-componente" name="componente" id="checkbox-componente-${comp.id}" value="${comp.id}">
                    <label for="checkbox-componente-${comp.id}" class="form-check-label">${comp.nome}</label>
                </div>`;
        $('#componentes-area').append(temp);
    });
}
function resetEditorFormValidation(formErrors) {   
    $('.invalid-feedback .valid-feedback').each(function (item) {
        item.empty();
    });
    $('.is-valid').each(function (item) {$(this).toggleClass('is-valid', false)});
    $('.is-invalid').each(function (item) {$(this).toggleClass('is-invalid', false)});
    $('.invalid-feedback, .valid-feedback').remove();
}
function renderInvalidFormFeedback(item) {
    var nomecampo = item.campo;
    var feedback_div = `<div class="invalid-feedback">${item.msg}</div>`;
    $('#editor_form-'+nomecampo).toggleClass('is-invalid', true);
    $(feedback_div).insertAfter('#editor_form-'+nomecampo);
}
function setFormMasks() {
    $('#editor_form-placa').mask('AAA-0000');
    $('#editor_form-renavam').mask('00000000000');
    $('#editor_form-preco').mask('000.000,00', {reverse: true});
    $('#editor_form-preco_fipe').mask('000.000,00', {reverse: true});
    $('#editor_form-km').mask('000.000', {reverse: true});
}