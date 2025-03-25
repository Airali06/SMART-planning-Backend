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



$conn = new mysqli("localhost", "root", "", "z-planning_db");


if ($conn->connect_error) {
    echo json_encode(['errore' => 'nessunrisultato']);
    die();
} else {


    $rawData = file_get_contents("php://input");
    $dati = json_decode($rawData, true);

    if (!$dati) {
        echo json_encode(['errore' => 'Dati non validi']);
        die();
    }

    $id_prenotazione = $dati['id_prenotazione'];
    $id_postazione = $dati['id_postazione'];
    $data = $dati['data'];
    $n_modifiche = (int) $dati['n_modifiche']; //contatore arriva non ancora incrementato
    $n_modifiche++;

    $sql = "UPDATE prenotazioni SET 
            id_postazione = '$id_postazione', 
            data = '$data', 
            n_modifiche = '$n_modifiche'
        WHERE id_prenotazione = '$id_prenotazione'";



    $result = $conn->query($sql);

    echo json_encode(['stato' => 'OK']);
    $conn->close();
}
?>