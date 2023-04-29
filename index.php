<?php
session_start();
$errorMessages = [
    '<h1>An error occurred!</h1> <p1>It seems like the QR-Code provided is invalid!<p1>',
    '<h1>An error occurred!</h1> <p1>The QR-Code has already been used!<p1>',
    '<h1>An error occurred!</h1> <p1>QR-Code is not registered for this table!<p1>',
    '<h1>An error occurred! Error 505</h1> <p1>Internal Server Error!<p1>'
];
if (!isset($_SESSION['valid'])) {
    if (isset($_GET['token']) && isset($_GET['tableNo'])) {
        $token = $_GET['token'];
        $tableNo = $_GET['tableNo'];

        include_once("checkToken.php");
        $response = checkToken($token);

        $data = json_decode($response, true);

        $_SESSION['valid'] = false;
        if ($data !== null) {
            if (count($data) < 1) {
                echo $errorMessages[0];
            } elseif ($data[0]['used'] != 0) {
                echo $errorMessages[1];
            } elseif ($data[0]['tableNo'] != $tableNo) {
                echo $errorMessages[2];
            } else {
                $_SESSION['valid'] = true;
            }
        } else {
            echo $errorMessages[3];
        }
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
                <div id="basket">
                    <div id="basket-header">
                        <button id="basket-close">&#10006;</button>
                        <h2>Warenkorb</h2>
                    </div>
                    <table id="basket-items">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Menge</th>
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
            <div class="item" id="1" onclick="order(event)">
                <div class="content-top">
                    <img src="https://cdn.row-hosting.de/BBT/placeholder.png" class="item-image">
                </div>
                <div class="content-bottom">
                    <p class="item-info"><span class='item-name'>Cola</span> <br> <b class='item-price'>1.23</b> €</p>
                </div>
            </div>
            <div class="item" id="2" onclick="order(event)">
                <div class="content-top">
                    <img src="https://cdn.row-hosting.de/BBT/placeholder.png" class="item-image">
                </div>
                <div class="content-bottom">
                    <p class="item-info"><span class='item-name'>Cola Vanille</span> <br> <b class='item-price'>1.23</b> €
                    </p>
                </div>
            </div>
            <div class="item" id="3" onclick="order(event)">
                <div class="content-top">
                    <img src="https://cdn.row-hosting.de/BBT/placeholder.png" class="item-image">
                </div>
                <div class="content-bottom">
                    <p class="item-info"><span class='item-name'>Fanta</span> <br> <b class='item-price'>1.23</b> €</p>
                </div>
            </div>
            <div class="item" id="4" onclick="order(event)">
                <div class="content-top">
                    <img src="https://cdn.row-hosting.de/BBT/placeholder.png" class="item-image">
                </div>
                <div class="content-bottom">
                    <p class="item-info"><span class='item-name'>Fanta Mango</span> <br> <b class='item-price'>1.23</b> €
                    </p>
                </div>
            </div>
            <div class="item" id="5" onclick="order(event)">
                <div class="content-top">
                    <img src="https://cdn.row-hosting.de/BBT/placeholder.png" class="item-image">
                </div>
                <div class="content-bottom">
                    <p class="item-info"><span class='item-name'>Sprite</span> <br> <b class='item-price'>1.23</b> €</p>
                </div>
            </div>
            <div class="item" id="6" onclick="order(event)">
                <div class="content-top">
                    <img src="https://cdn.row-hosting.de/BBT/placeholder.png" class="item-image">
                </div>
                <div class="content-bottom">
                    <p class="item-info"><span class='item-name'>Energy Drink</span> <br> <b class='item-price'>1.23</b> €
                    </p>
                </div>
            </div>
        </div>
    </body>

    </html>
    <?php
}
?>