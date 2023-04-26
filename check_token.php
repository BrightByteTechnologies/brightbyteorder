<?php
// Verbindung zur MySQL-Datenbank herstellen
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tags";
$conn = new mysqli($servername, $username, $password, $dbname);

// Token von der URL lesen
if (isset($_GET['token'])) {
    $tagToken = $_GET['token'];
    
    // Token in der Datenbank überprüfen
    $sql = "SELECT * FROM nfc_tags WHERE token = '$tagToken'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        // Token ist gültig
        $response = array("valid" => true);
    } else {
        // Token ist ungültig
        $response = array("valid" => false);
    }
    
    // JSON-Antwort zurückgeben
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Kein Token in der URL gefunden
    header("HTTP/1.1 400 Bad Request");
}
$conn->close();
?>
