<?php
if (session_status() == PHP_SESSION_NONE) {
    // 3600 Seconds is equal to one hour
    $sessionTimeout = 3600 * 3;

    session_set_cookie_params($sessionTimeout); // Set the session cookie parameters
    session_start(); // Start the session if it's not already started
}

if (isset($_POST['functionName'])) {
    switch ($_POST['functionName']) {
        case 'getProducts':
            print_r(getProducts());
            break;
        case 'placeOrder':
            print_r(placeOrder());
            break;

        default:
            $data = array(
                'status' => 400,
                'message' => 'Unknown Function called'
            );

            echo json_encode($data);
            break;
    }
}

function checkToken($token)
{
    // Token von der methode lesen
    if (isset($token)) {
        $config = file_get_contents('config.json');
        $data = json_decode($config, true);

        $restaurantId = $data['RESTAURANT']['id'];

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
    $config = file_get_contents('config.json');
    $data = json_decode($config, true);

    $restaurantId = $data['RESTAURANT']['id'];

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

        $restaurantId = $data['RESTAURANT']['id'];

        $API_KEY = $data['API'][3]['key'];
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

function placeOrder()
{
    if (isset($_SESSION['valid']) && $_SESSION['valid']) {
        // Check the time elapsed since the last order
        $currentTime = time();
        $lastOrderTime = isset($_SESSION['lastOrderTime']) ? $_SESSION['lastOrderTime'] : 0;
        $elapsedTime = $currentTime - $lastOrderTime;
        $minTimeBetweenOrders = 1 * 60; // 10 minutes in seconds

        $init = $minTimeBetweenOrders - $elapsedTime;
        $minutes = floor(($init / 60) % 60);
        if ($elapsedTime < $minTimeBetweenOrders) {
            $data = array(
                'status' => 400,
                'message' => 'Please wait for ' . ($minutes) . ' minutes before placing another order.'
            );
            return json_encode($data);
        }

        $config = file_get_contents('config.json');
        $data = json_decode($config, true);

        $restaurantId = $data['RESTAURANT']['id'];

        $basketItems = json_decode($_POST['basketItems'], true);

        $totalAmount = 0;

        for ($i = 0; $i < count($basketItems); $i++) {
            if (isInProducts($basketItems[$i + 1]['name'], $basketItems[$i + 1]['description'])) {
                $totalAmount += $basketItems[$i + 1]['totalPrice'];
            } else {
                $data = array(
                    'status' => 400,
                    'message' => 'Can\'t fulfill order!'
                );

                return json_encode($data);
            }
        }

        // Create a new array with basket items and total amount
        $orderData = array(
            'basketItems' => $basketItems,
            'totalAmount' => $totalAmount
        );

        // Encode the order data to JSON format
        $jsonOrderData = json_encode($orderData);

        $jsonData = array(
            'orderData' => $jsonOrderData,
            'restaurant_id' => $restaurantId,
            'token' => $_SESSION['token']
        );

        $API_KEY = $data['API'][7]['key'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'api.brightbytetechnologies.de/orders/place');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jsonData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('api-key: ' . $API_KEY, 'Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === 'OK') {
            // Update the last order time in the session
            $_SESSION['lastOrderTime'] = $currentTime;
        }

        return $response;
    } else {
        $data = array(
            'status' => 400,
            'message' => 'Your session has expired'
        );

        return json_encode($data);
    }
}

function isInProducts($name, $desc)
{
    $products = getProducts();
    $productsArray = json_decode($products, true);
    foreach ($productsArray as $product) {
        if ($product['name'] === $name && $product['description'] === $desc) {
            return true;
        }
    }
    return false;
}
?>