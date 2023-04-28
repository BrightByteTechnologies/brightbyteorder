<?php
function checkToken($token) {
// Token von der methode lesen
if (isset($token)) {
    $restaurantId = "test_restaurant";

    $config = file_get_contents('config.json');
    $data = json_decode($config, true);

    $API_KEY = $data['API'][1]['key'];
    $ch = curl_init("http://localhost:3000/qrcodes?restaurant_id=".$restaurantId. "&token=".$token);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('api-key: ' . $API_KEY));

    $response = curl_exec($ch);

    curl_close($ch);

    // JSON-Antwort zurückgeben
    return $response;
} else {
    // Kein Token angegeben
    return "token is required!";
}
}

?>
