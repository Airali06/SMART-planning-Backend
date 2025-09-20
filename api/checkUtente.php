<?php
header("Access-Control-Allow-Origin: *"); // Permette richieste da qualsiasi dominio (*), cambialo per sicurezza
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Metodi consentiti
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With"); // Header consentiti
header("Access-Control-Allow-Credentials: true");
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
    $data = json_decode($rawData, true);
    $id_utente = $data['id_utente'];

    $sql = "SELECT * FROM utenti WHERE id_utente='$id_utente'";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        $response = ['response' => "0"]; //utente non  trovato

    } else {
        $response = ['response' => "1"]; //utente trovato
    }
    echo json_encode($response);
}

?>