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
    echo json_encode(['errore' => 'errore']);
    die();
} else {


    $rawData = file_get_contents("php://input");
    $dati = json_decode($rawData, true);
    $data = $dati['data'];



    $sql = "SELECT nome 
    FROM postazioni 
    WHERE id_postazione NOT IN (
        SELECT id_prenotazione 
        FROM prenotazioni 
        WHERE data = '$data'
    )
    AND id_categoria NOT IN ('C', 'B')";

    $result = $conn->query($sql);

    $records = [];
    $postazioni = "";

    if (!$result) {
        // La query ha fallito
        echo json_encode(['errore' => 'errore']);
        exit; // interrompe l'esecuzione dello script
    }

    $records = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
            $postazioni .= $row['nome'] . ";";
        }

        echo json_encode(["postazioni" => $postazioni, "errore" => "NONE"]);
    } else {
        echo json_encode(["msg" => "NONE", "errore" => "errore"]);
    }


    $conn->close();
}
?>