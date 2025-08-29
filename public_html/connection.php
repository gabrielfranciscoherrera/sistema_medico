<?php
$servername = "127.0.0.1";
$username = "root";  
$password = "Theboy%88"; 
$dbname = "sistema_medico_eDoc"; 

$database = new mysqli($servername, $username, $password, $dbname);

if ($database->connect_error) {
    die("Échec de la connexion : " . $database->connect_error);
}
?>

