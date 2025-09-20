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

    $sql = "SELECT id_postazione, nome, id_categoria, descrizione, stato  FROM postazioni";
    $result = $conn->query($sql);

    $records = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }
    } else {
        // Se non ci sono risultati
        echo json_encode(['errore' => 'Nessuna postazione trovata']);
    }
    echo json_encode($records);
    $conn->close();

}