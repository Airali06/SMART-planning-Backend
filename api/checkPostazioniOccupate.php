<?php
header("Access-Control-Allow-Origin: *"); // Permette richieste da qualsiasi dominio (*), cambialo per sicurezza
header("Access-Control-Allow-Methods:  POST, OPTIONS"); // Metodi consentiti
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With"); // Header consentiti
header("Access-Control-Allow-Credentials: true"); 
header("Content-Type: application/json");

// Pulisce il buffer senza inviarlo
ob_start();
ob_end_clean();

// Disabilita la visualizzazione degli errori
error_reporting(0);
ini_set('display_errors', 0);

// Gestione della richiesta preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit; // Rispondi subito per le richieste preflight
}

// Connessione al database
$conn = new mysqli("localhost", "root", "", "z-planning_db");

// Ricezione dei dati JSON inviati nella richiesta
$rawData = file_get_contents("php://input");
$dati = json_decode($rawData, true);
$data = $dati['data']; 

// Verifica se ci sono errori nella connessione
if ($conn->error) {
    echo json_encode(['errore' => 'nessunrisultato']);
    die();
} else {
    // Query per ottenere tutte le postazioni occupate per la data specificata
    $sql = "SELECT id_postazione FROM prenotazioni WHERE data = '$data'";
    $result = $conn->query($sql);

    // Array per memorizzare le postazioni occupate
    $occupate = [];

    if ($result->num_rows > 0) {
        // Aggiungi tutte le postazioni occupate nell'array
        while ($row = $result->fetch_assoc()) {
            $occupate[] = $row['id_postazione'];
        }
        // Ritorna l'elenco delle postazioni occupate
        echo json_encode(['stato' => 'occupate', 'occupate' => $occupate]);
    } else {
        // Se non ci sono postazioni occupate, ritorna "libera"
        echo json_encode(['stato' => 'libera']);
    }

    // Chiudi la connessione al database
    $conn->close();
}
?>
