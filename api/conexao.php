<?php
$host = "localhost";
$db_name = "bd_censo";
$username = "root"; 
$password = "";     

try {
    $conn = new PDO("mysql:host={$host};dbname={$db_name}", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $exception) {
    echo json_encode(["erro" => "Erro de conexão: " . $exception->getMessage()]);
    exit;
}
?>