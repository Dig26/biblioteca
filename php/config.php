<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "biblioteca";

// Creazione della connessione MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);
// Controllo della connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}
?>
