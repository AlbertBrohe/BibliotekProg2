<?php
session_start();
include 'dbconn.php';

// Kontrollera om användaren är inloggad som administratör
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Meddelande variabel för att visa resultatet av bokinläggningen
$message = '';

// Hantera bokinläggningsformuläret
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titel = mysqli_real_escape_string($conn, $_POST['titel']);
    $författare = mysqli_real_escape_string($conn, $_POST['författare']);
    $ISBN = mysqli_real_escape_string($conn, $_POST['ISBN']);
    $utgivningsår = mysqli_real_escape_string($conn, $_POST['utgivningsår']);
    $tillgängliga_exemplar = mysqli_real_escape_string($conn, $_POST['tillgängliga_exemplar']);

    // Bilduppladdning
    $target_directory = "uploads/";
    $target_file = $target_directory . basename($_FILES["bild"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Kontrollera om filen är en riktig bild eller en falsk bild
    $check = getimagesize($_FILES["bild"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $message = "Filen är inte en bild.";
        $uploadOk = 0;
    }

    // Kontrollera om filen redan existerar
    if (file_exists($target_file)) {
        $message = "Filen finns redan.";
        $uploadOk = 0;
    }

    // Kontrollera filstorlek
    if ($_FILES["bild"]["size"] > 10000000) {
        $message = "Filen är för stor.";
        $uploadOk = 0;
    }

    // Tillåt vissa filformat
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $message = "Endast JPG, JPEG, PNG och GIF filer är tillåtna.";
        $uploadOk = 0;
    }

    // Kontrollera om $uploadOk är satt till 0 av ett fel
    if ($uploadOk == 0) {
        $message = "Din fil laddades inte upp.";
    } else {
        if (move_uploaded_file($_FILES["bild"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO Böcker (titel, författare, ISBN, utgivningsår, tillgängliga_exemplar, bild) 
                    VALUES ('$titel', '$författare', '$ISBN', $utgivningsår, $tillgängliga_exemplar, '$target_file')";

            if ($conn->query($sql) === TRUE) {
                $message = "Boken har lagts till i databasen.";
                header("Location: admin.php?message=success");
                exit();
                
            } else {
                $message = "Fel vid inläggning av boken: " . $conn->error;
            }
        } else {
            $message = "Det uppstod ett fel vid uppladdning av din fil.";
        }
    }
}

// Kontrollera om ett meddelande har skickats som en GET-parameter
if (isset($_GET['message']) && $_GET['message'] == 'success') {
    $message = "Boken har lagts till i databasen.";
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Lägg till bok</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.cdnfonts.com/css/playfair-display" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/raleway-5" rel="stylesheet">
</head>
<body>
    <div class="navbar">
        <div class="knappar">
            <div class="knapp hover" onclick="document.location='logout.php'"><h3>Logga Ut</h3></div>
            <div class="knapp hover" onclick="document.location='lonade.php'"><h3>Utlånat</h3></div>
        </div>
    </div>

    <div class="main">
        <div class="container">
            <h2>Lägg till ny bok</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <label for="titel">Titel:</label>
                <input type="text" id="titel" name="titel" required>
                
                <label for="författare">Författare:</label>
                <input type="text" id="författare" name="författare" required>
                
                <label for="ISBN">ISBN:</label>
                <input type="text" id="ISBN" name="ISBN" required>
                
                <label for="utgivningsår">Utgivningsår:</label>
                <input type="number" id="utgivningsår" name="utgivningsår" required>
                
                <label for="tillgängliga_exemplar">Tillgängliga Exemplar:</label>
                <input type="number" id="tillgängliga_exemplar" name="tillgängliga_exemplar" required>

                <label for="bild">Bild:</label>
                <input type="file" id="bild" name="bild" required>

                <button type="submit">Lägg till bok</button>
            </form>

            <?php
            if ($message) {
                echo '<div class="message">' . $message . '</div>';
            }
            ?>
        </div>
    </div>
</body>
</html>
