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



$session_timetolive = 900; //15 min

function getFutureTimestamp($minutidopo)
{
    // Calcola la data e l'ora nel futuro
    /*
        restituisce un numero in formato YYMMDDHHmm esempio 202506200934 
        è settato in modo che rappresenti il momento della scadenza
        verrà poi confrotato con la corrispondente attuale per evitare problemi di conversioni di giorni mesi o anni
        se il la stringa attuale è un numero maggiore della stringa di scadenza, la sessione è scaduta
        sono ammesse solo cifre per convertirlo in intero e facilitare il confronto
    */


    // Crea la stringa numerica
    return date("YmdHi", time() + $minutidopo * 60) * 1;
}


$conn = new mysqli("localhost", "root", "", "SMART-planning_db");




if ($conn->error) {
    echo json_encode(['errore' => 'nessunrisultato']);
    die();
} else {


    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);
    $id_utente = $data['id_utente'];
    $password = $data['password'];
    $session_id = $data['id_sessione'];

    $sql = "SELECT password FROM utenti WHERE id_utente='$id_utente'";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        //username non presente
        echo json_encode(['errore' => 'nessunrisultato']);
        //header("Location :registra.php");
    } else {
        //leggo la password del db
        $row = $result->fetch_assoc();
        $pwddb = $row["password"];

        if ($password == $pwddb) {

            if ($session_id) {
                session_id($session_id); //specifica ceh id assegnare alla futura sessione o riprendere una già esistente
            }
            session_start();  //inizia una nuova sessione con id dato o riprende sessione già esistente rinnovandone la scadenza
            $controlCode = rand(10000, 99999);
            $scadenza = getFutureTimestamp($session_timetolive);
            $_SESSION['scadenza'] = $scadenza; // Imposta l'orario di scadenza
            $_SESSION['id'] = $session_id;
            $_SESSION['controlCode'] = $controlCode;


            $sql = "SELECT password, nome, cognome, genere, username, livello, id_coordinatore FROM utenti WHERE id_utente='$id_utente'";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();

            $response = [
                'id_sessione' => $session_id,
                'scadenza' => $scadenza,
                'id_utente' => $id_utente,
                'nome' => $row["nome"],
                'cognome' => $row["cognome"],
                'genere' => $row["genere"],
                'username' => $row["username"],
                'livello' => $row["livello"],
                'id_coordinatore' => $row["id_coordinatore"],
                'controlCode' => $controlCode,
                'errore' => '',
            ];

            echo json_encode($response);


        } else {
            echo json_encode(['errore' => 'passworderrata']);
        }

    }
}




?>