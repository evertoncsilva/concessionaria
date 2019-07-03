<?php
    // INSERIR CONTEÚDO AQUI
?>
<?php include __DIR__.'/../partials/header.php' ?>

<div class="container">
<div id="logreg_forms">
        <form class="form-signin" action="login.php" method="POST">
            <input type="hidden" name="action" value="login">
            <h1 class="h3 mb-3 font-weight-normal" style="text-align: center"> Efetue Login </h1>
            <input type="text" name="login" id="inputEmail" class="form-control" placeholder="Email" required="" autofocus="">
            <input type="password" name="senha" id="inputPassword" class="form-control" placeholder="Senha" required="">
            
            <button class="btn btn-success btn-block" type="submit"><i class="fas fa-sign-in-alt"></i> Entrar </button>
            <hr>
            </form>
            <form class="form-signup" action="login.php" method="POST">
                <p style="text-align:center">OU</p>
                <input type="hidden" name="action" value="registrar">
                <input type="text" name="login" id="user-login" class="form-control" placeholder="login" required autofocus="">
                <input type="password" name="senha" id="user-pass" class="form-control" placeholder="Senha" required autofocus="">
                <input type="password" name="confirma-senha" id="user-repeatpass" class="form-control" placeholder="Confirmar senha" required autofocus="">
                <button class="btn btn-primary btn-block" type="submit"><i class="fas fa-user-plus"></i> Registrar</button>
            </form>
            <br>
            
    </div>
</div>



<?php include __DIR__.'/../partials/footer.php'?>

<?php
// MENSAGEM DE ERRO
if(isset($msg) && $success == false)
{ ?>
<script>

   $("body").append(templateAlertErro("<?= $msg ?>"));
        $("#alert").delay(1500).fadeOut(1500, function(){
            $(this).remove();
        });
function templateAlertErro(message) 
  {
      return `<div id="alert" class="alert alert-warning alert-fixed" role="alert">
      <span class"alert-text">${message}</span>
      <i class="material-icons">error_outline</i></div>
      </div>`;
  }     
</script>
<?php } ?>

<?php
// MENSAGEM DE SUCESSO
if(isset($msg) && $success == true)
{ ?>
<script>

   $("body").append(templateAlertSuccess("<?= $msg ?>"));
        $("#alert").delay(1500).fadeOut(1500, function(){
            $(this).remove();
        });
function templateAlertSuccess(message) 
  {
      
      return `<div id="alert" class="alert alert-success alert-fixed" role="alert">
      <span class"alert-text">${message}</span>
      <i class="material-icons">done</i></div>`;
  }
</script>
<?php } ?>