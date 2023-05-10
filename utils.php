<?php
if (isset($_GET['functionName']) && $_GET['functionName'] == 'getProducts') {
    echo getProducts();
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$config = file_get_contents('config.json');
$data = json_decode($config, true);

$restaurantId = $data['RESTAURANT']['id'];

function checkToken($token)
{
    // Token von der methode lesen
    if (isset($token)) {
        global $restaurantId;
        global $data;

        $API_KEY = $data['API'][1]['key'];

        $ch = curl_init("api.brightbytetechnologies.de/qrcodes?restaurant_id=" . $restaurantId . "&token=" . $token);
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

function useToken($token)
{
    global $restaurantId;
    global $data;

    $API_KEY = $data['API'][2]['key'];

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

function getProducts()
{
    if (!isset($_SESSION['products'])) {
        $config = file_get_contents('config.json');
        $data = json_decode($config, true);

        $API_KEY = $data['API'][3]['key'];
        $restaurantId = $data['RESTAURANT']['id'];
        $ch = curl_init("api.brightbytetechnologies.de/products?restaurant_id=" . $restaurantId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('api-key: ' . $API_KEY));

        $response = curl_exec($ch);
        curl_close($ch);

        $products = json_decode($response, true);
        foreach ($products as &$product) {
            $product['totalPrice'] = number_format($product['price'] * $product['taxAmount'], 2, ',', '');
            $hashCode = hash('sha256', $product['id']);
            $product['hashCode'] = $hashCode;
        }
        $response = json_encode($products);

        $_SESSION['products'] = $response;
        // JSON-Antwort zurückgeben
        return $response;
    } else {
        return $_SESSION['products'];
    }
}
?>