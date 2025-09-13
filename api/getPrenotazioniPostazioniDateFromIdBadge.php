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


if ($conn->error) {
    echo json_encode(['errore' => 'errore']);
    die();
} else {


    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);
    $id_badge = $data['id_badge'];
    $data = $dati['data'];
    $username = "";

    $sql = "SELECT utenti.nome FROM badge JOIN utenti on utenti.id_utente = badge.id_utente  WHERE id_badge='$id_badge'";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $username = $row['username'];
    } else {
        echo json_encode(['errore' => 'errore']);
    }



    $sql = "SELECT postazioni.nome FROM badge JOIN prenotazioni on prenotazioni.id_utente = badge.id_utente join postazioni on postazioni.id_postazione = prenotazioni.id_prenotazione WHERE id_badge='$id_badge' AND data = '$data' AND postazioni.catecoria !='C'";

    $result = $conn->query($sql);

    $records = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }
    }

    echo json_encode($records);
    $conn->close();
}
?>