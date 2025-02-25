<?php
header("Access-Control-Allow-Origin: *"); // Permette richieste da qualsiasi dominio (*), cambialo per sicurezza
header("Access-Control-Allow-Methods:  POST, OPTIONS"); // Metodi consentiti
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With"); // Header consentiti
header("Access-Control-Allow-Credentials: true"); 
header("Content-Type: application/json");


error_reporting(0);//no invio errori del server nella response
ini_set('display_errors', 0);




if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit; // Rispondi subito per le richieste preflight
}



            $rawData = file_get_contents("php://input");
            $data = json_decode($rawData, true);
            $session_id = $data['id_sessione'];
            $controlCode = $data['controlCode'];

            session_id($session_id);//imposto l'id della sessione da riprendere o iniziare
            session_start();//apro la sessione

            if( $_SESSION['controlCode'] != $controlCode){  //esegue il controllo solo se il controlCode è corretto
                $response = ['alive' => 'errore'];
                echo json_encode($response);
                session_unset(); // rimuovo tutte le variabili di sessione
                session_destroy();//RIP sessione
                exit();
            }

            if(!isset($_SESSION['id']) || !isset($_SESSION['scadenza'])){
                $response = ['alive' => 'errore'];
                echo json_encode($response);
                session_unset(); // rimuovo tutte le variabili di sessione
                session_destroy();//RIP sessione
                exit();
            }

            if($_SESSION['scadenza']*1 <= date("YmdHi")*1){
                $response = ['alive' => 'errore3'];
                echo json_encode($response);
                session_unset(); // rimuovo tutte le variabili di sessione
                session_destroy();//RIP sessione
                exit();  
            }
            
            $response = ['alive' => 'OK'];
             echo json_encode($response);


?>