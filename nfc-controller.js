if ('NDEFReader' in window) {
    const reader = new NDEFReader();
  
    reader.scan().then(() => {
      console.log("NFC scan started successfully.");
      reader.onreadingerror = () => {
        console.log("Cannot read data from the NFC tag. Try again.");
      };
      reader.onreading = event => {
        console.log("NDEF message read.");
        const urlRecord = event.message.records.find(r => r.recordType === "url");
        if (urlRecord) {
          // Token von der URL extrahieren
          const tagToken = urlRecord.data.substr(7);
          
          // Token in der Datenbank überprüfen
          fetch(`check_token.php?token=${tagToken}`).then(response => {
            if (response.ok) {
              return response.json();
            } else {
              throw new Error("Network response was not ok.");
            }
          }).then(data => {
            if (data.valid) {
              // Neuen Token generieren
              const token = Math.random().toString(36).substr(2, 8); // 8-stelliger Token
  
              // Tag mit neuer URL beschreiben
              urlRecord.data = `order.brightbytetechnologies.de/?token=${token}`;
  
              // Tag schreiben
              const message = new NDEFMessage([urlRecord]);
              return reader.write(message);
            } else {
              throw new Error("Invalid token.");
            }
          }).then(() => {
            console.log("NDEF message written successfully.");
            // Wenn alles erfolgreich: Seite laden
            location.reload();
          }).catch(error => {
            console.error(error);
            alert("Fehler beim Schreiben des NFC-Tags.");
          });
        } else {
          console.log("NDEF message does not contain a URL record.");
        }
      };
    }).catch(error => {
      console.error(error);
      alert("Fehler beim Starten des NFC-Scans.");
    });
  } else {
    console.log("NDEFReader not supported.");
  }
  