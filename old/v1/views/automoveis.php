<?php
    require 'models/automovel.model.php';

    $auto = new Automovel();
    $autos = $auto->getAll();
?>


<div class="container">

<table class="table">
<thead class="thead-light">
    <tr>
    <th scope="col">#</th>
    <th scope="col">Renavam</th>
    <th scope="col">Placa</th>
    <th scope="col">Ano Modelo</th>
    </tr>
</thead>
<tbody>
<?php
        $rowCount = count($autos);
        for($i = 0; $i < $rowCount; $i++) {
?>
    <tr>
    <th scope="row"><?php echo $autos[$i]['id']?></th>
    <td><?php echo $autos[$i]['renavam']?></td>
    <td><?php echo $autos[$i]['placa']?></td>
    <td><?php echo $autos[$i]['ano_modelo']?></td>
    </tr>
<?php       } ?>
</tbody>
</table>

</div>
