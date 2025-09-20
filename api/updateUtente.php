<?php
header("Access-Control-Allow-Origin: *"); // Permette richieste da qualsiasi dominio (*), cambialo per sicurezza
header("Access-Control-Allow-Methods:  POST, OPTIONS"); // Metodi consentiti
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With"); // Header consentiti
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");
// Pulisce il buffer senza inviarlo
ob_start();
ob_end_clean();
error_reporting(0);
ini_set('display_errors', 0);
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit; // Rispondi subito per le richieste preflight
}



$conn = new mysqli("localhost", "root", "", "SMART-planning_db");


if ($conn->error) {
    echo json_encode(['errore' => 'nessunrisultato']);
    die();
} else {


    $rawData = file_get_contents("php://input");
    $dati = json_decode($rawData, true);

    $id_utente = $dati['id_utente'];
    $nome = $dati['nome'];
    $cognome = $dati['cognome'];
    $genere = $dati['genere'];
    $username = $dati['username'];
    $password = $dati['password'];
    $livello = $dati['livello'];
    $id_coordinatore = $dati['id_coordinatore'];
    $sql = "UPDATE utenti SET nome =  '$nome', cognome =  '$cognome', genere =  '$genere', username = '$username', password = '$password', livello = '$livello', id_coordinatore =  '$id_coordinatore' where id_utente = '$id_utente'";
    $result = $conn->query($sql);

    echo json_encode($records);
    $conn->close();

}
?>