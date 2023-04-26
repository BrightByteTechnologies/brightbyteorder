const form = document.getElementById('generate-form');
const qr = document.getElementById('qr-code');

const onGenerateSubmit = (e) => {
    e.preventDefault();
    clearUI();
    
    const selectValue = document.getElementById('tableSelect').value;

    const token = Math.random().toString(36).substr(2, 8); // 8-stelliger Token
    const url = "http://order.brightbytetechnologies.de/?token=" + token + "&tableNo=" + selectValue;

    generateQRCode(url);
}

const generateQRCode = (url) => {
    const qrcode = new QRCode('qr-code', {
        text: url,
        width: 128,
        height: 128,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
}

const clearUI = () => {
    qr.innerHTML = '';
}

form.addEventListener('submit', onGenerateSubmit);