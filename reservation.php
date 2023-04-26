<!DOCTYPE html>
<html>

<head>
    <title>One-Time-Use QR Code Generator</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"
        integrity="sha512-CNgIRecGo7nphbeZ04Sc13ka07paqdeTu0WR1IM4kNcpmBAUSHSQX0FslNhTDadL4O5SAGapGt4FodqL8My0mA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
</head>

<body>
    <h1>One-Time-Use QR Code Generator</h1>
    <form id="generate-form">
        <select name="tableNo" id="tableSelect">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
        </select>
        <button id="generate" type="submit">Generate QR Code</button>
    </form>
    <div id="qr-code"></div>
    <script src="js/qr-generator.js"></script>
</body>

</html>