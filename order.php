<?php

if (isset($_GET['token']) && isset($_GET['tableNo'])) {
    $token = $_GET['token'];
    $tableNo = $_GET['tableNo'];

    include_once("checkToken.php");
    $response = checkToken($token);

    $data = json_decode($response, true);

    $codeValid = false;
    if (count($data) < 1) {
        echo ("<h1>An error occurred!</h2><p1>It sems like the QR-Code provided is invalid!</p1>");
    } elseif ($data[0]['used'] != 0) {
        echo ("<h1>An error occurred!</h2><p1>The QR-Code has already been used!</p1>");
    } else {
        $codeValid = true;
    }
}

?>
<?php
if ($codeValid) {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Page Title</title>
    <!-- Your head content goes here -->
</head>
<body>
    <h1>QR-Code valid</h1>
</body>
</html>
<?php
}
?>
