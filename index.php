<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$errorMessages = [
    '<h1>An error occurred!</h1> <p1>It seems like the QR-Code provided is invalid!<p1>',
    '<h1>An error occurred!</h1> <p1>The QR-Code has already been used!<p1>',
    '<h1>An error occurred!</h1> <p1>QR-Code is not registered for this table!<p1>',
    '<h1>An error occurred! Error 505</h1> <p1>Internal Server Error!<p1>',
    '<h1>An error occurred! Error 404</h1> <p1>No Token found!<p1>'
];
if (!isset($_SESSION['valid'])) {
    if (isset($_GET['token']) && isset($_GET['tableNo'])) {
        $token = $_GET['token'];
        $tableNo = $_GET['tableNo'];

        require_once("checkToken.php");
        $tokenResponse = checkToken($token);

        $tokenData = json_decode($tokenResponse, true);
        $_SESSION['valid'] = false;
        if ($tokenData !== null) {
            if (count($tokenData) < 1) {
                echo $errorMessages[0];
            } elseif ($tokenData[0]['used'] != 0) {
                echo $errorMessages[1];
            } elseif ($tokenData[0]['tableNo'] != $tableNo) {
                echo $errorMessages[2];
            } else {
                $_SESSION['valid'] = true;
                require_once("getProducts.php");
                $productResponse = getProducts();
                
                $productData = json_decode($productResponse, true);

                $itemList = "";
                foreach ($productData as $item) {
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
                $_SESSION['itemList'] = $itemList;
            }
        } else {
            echo $errorMessages[3];
        }
    } else {
        echo $errorMessages[4];
    }
}

?>
<?php
if ($_SESSION['valid']) {
    ?>
    <!Doctype HTML>
    <html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut Icon" href="https://cdn.row-hosting.de/BBT/Website/bb-logo.png">

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
                    Warenkorb
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
                        <button id="pay-button">Bezahlen</button>
                        <button id="reset-button">Entfernen</button>
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