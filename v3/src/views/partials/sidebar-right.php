<div class="sidebar-right">
  <div class="sidebar-wrapper">
      <div class="item active clickable" id="sidebar-adicionar" onclick="onClickMenuAdicionar()">
          <span  class="sidebar-link">
            <i class="material-icons">add</i>
            Adicionar
        </span>
      </div>
      <div class="item active clickable" id="sidebar-adicionar" onclick="onClickExcluirVarios()">
          <span  class="sidebar-link">
            <i class="material-icons">delete_forever</i>
            Excluir
        </span>
      </div>
      <div class="item active clickable" id="sidebar-adicionar" onclick="toggleSelectAll(false)">
          <span  class="sidebar-link">
            <i class="material-icons">tab_unselected</i>
            Desselecionar
        </span>
      </div>
      <div class="item <?= (isset($_SESSION['login']) && $_SESSION['login'] == true ? 'active' : 'diabled')?> clickable" id="sidebar-logoff" 
      onclick="logout()">
          <span  class="sidebar-link">
            <i class="material-icons">exit_to_app</i>
            Logout
          </span>
      </div>
  </div>
</div>