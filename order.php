<?php
$errorMessages = [
    '<h1>An error occurred!</h1> <p1>It seems like the QR-Code provided is invalid!<p1>',
    '<h1>An error occurred!</h1> <p1>The QR-Code has already been used!<p1>',
    '<h1>An error occurred!</h1> <p1>QR-Code is not registered for this table!<p1>'
];
if (isset($_GET['token']) && isset($_GET['tableNo'])) {
    $token = $_GET['token'];
    $tableNo = $_GET['tableNo'];

    include_once("checkToken.php");
    $response = checkToken($token);

    $data = json_decode($response, true);

    $codeValid = false;
    if (count($data) < 1) {
        echo $errorMessages[0];
    } elseif ($data[0]['used'] != 0) {
        echo $errorMessages[1];
    } elseif ($data[0]['tableNo'] != $tableNo) {
        echo $errorMessages[2];
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
