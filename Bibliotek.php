<?php
session_start();
include 'dbconn.php'; // Inkludera databasanslutningsfilen

// Hämta användarens ID från sessionen
$user_id = $_SESSION['user_id'];

// Räkna antalet lånade böcker för användaren
$loan_count_sql = "SELECT COUNT(*) as loan_count FROM Utlåning WHERE användar_id = $user_id AND status = 'utlånad'";
$loan_count_result = $conn->query($loan_count_sql);

// Kontrollera om resultatet finns och hämta antalet
if ($loan_count_result->num_rows > 0) {
    $loan_count_row = $loan_count_result->fetch_assoc();
    $loan_count = $loan_count_row['loan_count'];
} else {
    $loan_count = 0;
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Användarsida</title>
    <link rel="stylesheet" href="Bibliotek.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.cdnfonts.com/css/playfair-display" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/raleway-5" rel="stylesheet">
</head>
<body>
    <div class="navbar">
        <div class="titel"><h2>Bibliotek</h2></div>
        <div class="knappar">
            <div class="knapp hover" onclick="document.location='logout.php'"><h3>Logga Ut</h3></div>
            <div class="knapp hover" onclick="document.location='minalon.php'"><h3>Mina Lån</h3></div>
        </div>
    </div>

    <div class="main">
        <h2>Hej, <?=$_SESSION['username']?>!<br>Antal lånade böcker: <?=$loan_count?></h2>
        
        <div class="alter">
            <div class="alt" onclick="document.location='lona.php'">Låna Bok</div> 
            <div class="alt" onclick="document.location='minalon.php'">Återlämna Bok</div>
        </div>
    </div>
</body>
</html>
