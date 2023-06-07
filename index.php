<?php
$errorMessages = [
    // Error message for invalid QR-Code
    '<h1>An error occurred!</h1> <p1>It seems like the QR-Code provided is invalid!<p1>',

    // Error message for already used QR-Code
    '<h1>An error occurred!</h1> <p1>The QR-Code has already been used!<p1>',

    // Error message for QR-Code not registered for the table
    '<h1>An error occurred!</h1> <p1>QR-Code is not registered for this table!<p1>',

    // Error message for internal server error
    '<h1>An error occurred! Error 505</h1> <p1>Internal Server Error!<p1>',

    // Error message for no token found
    '<h1>An error occurred! Error 404</h1> <p1>No Token found!<p1>'
];

if (session_status() == PHP_SESSION_NONE) {
    // 3600 Seconds is equal to one hour
    $sessionTimeout = 3600 * 3;

    session_set_cookie_params($sessionTimeout); // Set the session cookie parameters
    session_start(); // Start the session if it's not already started
}

if (!isset($_SESSION['valid'])) {
    if (isset($_GET['token']) && isset($_GET['tableNo'])) {
        $token = $_GET['token'];
        $tableNo = $_SESSION['tableNo'] = $_GET['tableNo'];

        require_once("utils.php"); // Include the necessary utility file
        $tokenResponse = checkToken($token); // Check the validity of the token

        $tokenData = json_decode($tokenResponse, true); // Decode the token response
        
        $_SESSION['valid'] = false; // Initialize the session variable for validity check
        if ($tokenData !== null) {
            if (count($tokenData) < 1) {
                echo $_SESSION['error'] = $errorMessages[0]; // Display error message for invalid QR-Code
            } elseif ($tokenData[0]['used'] != 0) {
                echo($$tokenData[0]);
                echo $_SESSION['error'] = $errorMessages[1]; // Display error message for already used QR-Code
            } elseif ($tokenData[0]['tableNo'] != $tableNo) {
                echo $_SESSION['error'] = $errorMessages[2]; // Display error message for QR-Code not registered for the table
            } else {
                $_SESSION['valid'] = true; // Set the session variable to indicate valid token
                $productResponse = getProducts(); // Get the product data
                $useTokenResponse = useToken($token); // Mark the token as used
                $_SESSION['token'] = $token;
                $productData = json_decode($productResponse, true); // Decode the product data

                $itemList = "";
                foreach ($productData as $item) {
                    // Iterate through each product and build the item list HTML
                    $id = $item['id'];
                    $name = $item['name'];
                    $description = $item['description'];
                    $basePrice = $item['price'];
                    $taxAmount = $item['taxAmount'];
                    $totalPrice = $item['totalPrice'];
                    $url = $item['url'];

                    $itemList .= "<div class='item' onclick='order(event)'>";
                    $itemList .= "<div class='content-top'> <img src='$url' class='item-image'> </div>";
                    $itemList .= "<div class='content-bottom'>";
                    $itemList .= "<p class='item-info'> <span class='item-name'>$name</span> <br> <span class='item-description'>$description</span> <br>";
                    $itemList .= "<b class='item-price'>$totalPrice</b> €</p> </div> </div>";
                }
                $_SESSION['itemList'] = $itemList; // Store the item list in the session
            }
        } else {
            echo $_SESSION['error'] = $errorMessages[3]; // Display error message for internal server error
        }
    } else {
        echo $_SESSION['error'] = $errorMessages[4]; // Display error message for no token found
    }
} elseif (isset($_SESSION['error'])) {
    echo $_SESSION['error']; // Display any existing error message in the session
}

if ($_SESSION['valid']) {
    ?>
    <!Doctype HTML>
    <html>

    <head>
        <title>Bestellung - BrightByte Technologies</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut Icon" href="https://cdn.row-hosting.de/BBT/Website/bb-logo.png">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bree+Serif&display=swap" rel="stylesheet">
        <style>
            @import url("css/root.css");
            @import url("css/order-style.css");
        </style>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/dompurify@2.3.2/dist/purify.min.js"></script>
        <script src="./scripts/order-script.js" defer></script>
    </head>

    <body>
        <div class="order-nav">
            <div class="order-basket">
                <button id="basketBtn">
                    <span id="basketCount">0</span>
                </button>
                <div id="basket-overlay"></div>
                <div class="basket">
                    <div id="basket-header">
                        <button id="basket-close">&#10006;</button>
                        <h2>Warenkorb</h2>
                    </div>
                    <table id="basket-items">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Beschreibung</th>
                                <th>Menge</th>
                                <th>Preis</th>
                                <th>Gesamt</th>
                                <th>Entfernen</th>
                            </tr>
                        </thead>
                        <tbody id="items-in-basket">
                        </tbody>
                    </table>
                    <div id="basket-footer">
                        <button id="pay-button">Bestellen</button>
                        <button id="reset-button">Leeren</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="menu-items">
            <?php
            echo $_SESSION['itemList'];
            ?>
        </div>
    </body>

    </html>
    <?php
}
?>