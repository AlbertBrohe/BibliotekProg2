<?php
session_start();

require_once "dbconn.php";

// Tar lånade böcker och binder dem till användaren
$lonade = "
    SELECT utlåning.*, användare.namn AS användarnamn, böcker.titel, böcker.författare, böcker.bild 
    FROM utlåning 
    JOIN användare ON utlåning.användar_id = användare.användar_id
    JOIN böcker ON utlåning.bok_id = böcker.bok_id
    WHERE utlåning.status = 'utlånad'
";
$totlonade = $conn->query($lonade);

// skapar array för att hålla böcker till specifik användare
$booksByUser = [];

while ($row = mysqli_fetch_assoc($totlonade)) { //delar ut böcker till den användare som lånat
    $användarnamn = $row['användarnamn'];
    if (!isset($booksByUser[$användarnamn])) {
        $booksByUser[$användarnamn] = [];
    }
    $booksByUser[$användarnamn][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="lonade.css">
    <link href="https://fonts.cdnfonts.com/css/playfair-display" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/raleway-5" rel="stylesheet">
</head>
<body>
    <div class="navbar">
        <div class="knappar">
            <div class="knapp hover" onclick="document.location='logout.php'"><h3>Logga Ut</h3></div>
            <div class="knapp hover" onclick="document.location='admin.php'"><h3>Lägg Till Bok</h3></div>
        </div>
    </div>

    <div class="main">
        <div class="storloda">
        <div class="title"><h2>Utlånade böcker</h2></div>
            <div class="lonadebokar">
                <?php foreach ($booksByUser as $användarnamn => $books): //printar ut informationen i diven?>
                    <div class="loda">
                        <div class="personer">
                            <div class="namn"><h4><?= $användarnamn ?>s lånade böcker</h4></div>
                            <?php foreach ($books as $book): ?>
                                <div class="bokar">
                                    <img src="<?= $book['bild'] ?>" alt="">
                                    <div class="info">
                                        <p><b>Skribent:</b><br>
                                        <?= $book['författare'] ?><br>
                                        <b>Bok:</b><br>
                                        <?= $book['titel'] ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
