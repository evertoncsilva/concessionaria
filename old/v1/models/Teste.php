<?php
namespace \Automobille\Model\Teste;
/**
 * Apenas um teste
 */
$hostname  = "localhost";
$username = "root";
$password = "";
$dbname = "automobilles";

$conn = new mysqli($hostname, $username, $password, $dbname);
$conn->query("SET NAMES 'utf8'");

$sql = "INSERT INTO componente (nome) VALUES ('componente1')";
$conn->query($sql);

?>