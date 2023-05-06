<?php
function checkToken($token) {
// Token von der methode lesen
if (isset($token)) {
    $config = file_get_contents('config.json');
    $data = json_decode($config, true);

    $API_KEY = $data['API'][1]['key'];
    $restaurantId = $data['RESTAURANT']['id'];

    $ch = curl_init("api.brightbytetechnologies.de/qrcodes?restaurant_id=".$restaurantId. "&token=".$token);
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
function useToken($token) {
    $config = file_get_contents('config.json');
    $data = json_decode($config, true);

    $API_KEY = $data['API'][2]['key'];
    $restaurantId = $data['RESTAURANT']['id'];

    $data = array(
        'restaurant_id' => $restaurantId,
        'token' => $token
    );

    $jsonData = json_encode($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'api.brightbytetechnologies.de/qrcodes/use');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('api-key: ' . $API_KEY, 'Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}
?>
