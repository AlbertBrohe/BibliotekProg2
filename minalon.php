<?php
session_start();

include 'dbconn.php';

// Funktion för att återlämna en bok
function returnBook($conn, $loan_id, $book_id) {
    // Uppdatera utlåningens status till "återlämnad"
    $today = date('Y-m-d');

    $update_loan_sql = "UPDATE Utlåning SET status='återlämnad', återlämnad='$today' WHERE utlånings_id=$loan_id";
    if ($conn->query($update_loan_sql) === TRUE) {
        // Öka antalet tillgängliga exemplar av boken
        $update_book_sql = "UPDATE Böcker SET tillgängliga_exemplar = tillgängliga_exemplar + 1 WHERE bok_id=$book_id";
        if ($conn->query($update_book_sql) === TRUE) {
            return "";
        } else {
            return "Fel vid uppdatering av bok: " . $conn->error;
        }
    } else {
        return "Fel vid uppdatering av utlåning: " . $conn->error;
    }
}

// Hantera återlämningsbegäran
if (isset($_POST['return'])) {
    $loan_id = $_POST['loan_id'];
    $book_id = $_POST['book_id'];
    $message = returnBook($conn, $loan_id, $book_id);
    echo "<p>$message</p>";

}

// Hämta användarens nuvarande lån
$user_id = $_SESSION['user_id'];
$current_loans_sql = "SELECT Utlåning.*, Böcker.titel, Böcker.författare, Böcker.bild, Böcker.utgivningsår, Böcker.ISBN 
                      FROM Utlåning
                      JOIN Böcker ON Utlåning.bok_id = Böcker.bok_id
                      WHERE Utlåning.användar_id = $user_id AND Utlåning.status = 'utlånad'";
$current_loans_result = $conn->query($current_loans_sql);

// Hämta användarens tidigare lån
$past_loans_sql = "SELECT Utlåning.*, Böcker.titel, Böcker.författare, Böcker.bild, Böcker.utgivningsår, Böcker.ISBN 
                   FROM Utlåning
                   JOIN Böcker ON Utlåning.bok_id = Böcker.bok_id
                   WHERE Utlåning.användar_id = $user_id AND Utlåning.status = 'återlämnad'";
$past_loans_result = $conn->query($past_loans_sql);
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mina Lån</title>
    <link rel="stylesheet" href="minalon.css">
    <link href="https://fonts.cdnfonts.com/css/playfair-display" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/raleway-5" rel="stylesheet">
</head>
<body>
    <div class="navbar">
        <div class="titel" onclick="document.location='bibliotek.php'"><h2>Bibliotek</h2></div>
        <div class="knappar">
            <div class="sok hover" onclick="document.location='search.php'"><i class="fa fa-search"></i></div>
            <div class="knapp hover" onclick="document.location='logout.php'"><h3>Logga Ut</h3></div>
            <div class="knapp hover" onclick="document.location='lona.php'"><h3>Låna</h3></div>
        </div>
    </div>

    <div class="main"> 
        <div class="venster">
            <h2> Dina Nuvarande Lån</h2>
            <div class="current-loans">
            <?php
            if ($current_loans_result->num_rows > 0) { //Visar informationen för nuvarande lån
                while ($row = $current_loans_result->fetch_assoc()) {
                    $id = $row['bok_id'];
                    $today = date('Y-m-d');
                    $return_date = $row['återlämningsdatum'];

                    $overdue_message = ($today > $return_date) ? " (Försenad)" : "";
                    ?>

                    <div class="holder">
                        <img src="<?=$row['bild']?>" alt="<?=$row['titel']?>">
                        <div class="info">
                            <h3><?=$row['titel']?></h3>
                            <p><?=$row['författare']?>, <?=$row['utgivningsår']?></p>
                            <p>Återlämningsdatum: <?=$return_date?><?=$overdue_message?></p>
                            <p>ISBN: <?=$row['ISBN']?></p>
                            <form action="<?=htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
                                <input type="hidden" name="loan_id" value="<?=$row['utlånings_id']?>">
                                <input type="hidden" name="book_id" value="<?=$row['bok_id']?>">
                                <button type="submit" name="return">Återlämna</button>
                            </form>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<h4>Du har inga nuvarande lån.</h4>"; //om personen int ehar nuvarande lån
            }
            ?>
            </div>
        </div>
        <div class="hoger">
            <h2>Dina Tidigare Lån</h2>
            <div class="past-loans">
            <?php
            if ($past_loans_result->num_rows > 0) {
                while ($row = $past_loans_result->fetch_assoc()) { //skriver ut informationen om tidigare lån
                    $date = date("Y-m-d");
                    ?>

                    <div class="holder">
                        <img src="<?=$row['bild']?>" alt="<?=$row['titel']?>">
                        <div class="info">
                            <h3><?=$row['titel']?></h3>
                            <p><?=$row['författare']?>, <?=$row['utgivningsår']?></p>
                            <p>Återlämnad:<?=$row['återlämnad']?></p>
                            <p>ISBN: <?=$row['ISBN']?></p>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<h4>Du har inga tidigare lån.</h4>"; //om personen inte lånat förut
            }
            ?>
            </div>
        </div>
    </div>
    <p><a href="biblotek.php">Tillbaka till start</a></p>
</body>
</html>
