<?php
session_start();

include 'dbconn.php';

// Klassen för utlåningsprocessen
class UtlåningsHanterare {
    private $anslutning; // Databasanslutningsobjektet

    // Konstruktorn tar emot databasanslutningen och sparar den i klassen
    public function __construct($anslutning) {
        $this->anslutning = $anslutning;
    }

    // Metod för att låna en bok
    public function lånaBok($book_id, $user_id) {
        // Hämta bokens tillgängliga exemplar
        $book_check_sql = "SELECT tillgängliga_exemplar FROM Böcker WHERE bok_id = $book_id";
        $book_check_result = $this->anslutning->query($book_check_sql);

        if ($book_check_result->num_rows > 0) {
            $book = $book_check_result->fetch_assoc();
            if ($book['tillgängliga_exemplar'] > 0) {
                // Minska antalet tillgängliga exemplar
                $update_book_sql = "UPDATE Böcker SET tillgängliga_exemplar = tillgängliga_exemplar - 1 WHERE bok_id = $book_id";
                if ($this->anslutning->query($update_book_sql) === TRUE) {
                    // Lägg till utlåning i databasen
                    $loan_date = date('Y-m-d');
                    $return_date = date('Y-m-d', strtotime('+14 days')); // 2 veckor senare
                    $loan_sql = "INSERT INTO Utlåning (bok_id, användar_id, utlåningsdatum, återlämningsdatum, status) VALUES ($book_id, $user_id, '$loan_date', '$return_date', 'utlånad')";
                    if ($this->anslutning->query($loan_sql) === TRUE) {
                        return "Boken har lånats ut!";
                    } else {
                        return "Fel vid utlåning: " . $this->anslutning->error;
                    }
                } else {
                    return "Fel vid uppdatering av bok: " . $this->anslutning->error;
                }
            } else {
                return "Boken är inte tillgänglig för utlåning.";
            }
        } else {
            return "Boken finns inte.";
        }
    }
}

$utlåningsHanterare = new UtlåningsHanterare($conn); // Skapa en instans av UtlåningsHanterare med databasanslutningen

// Hantera lånebegäran
if (isset($_POST['loan'])) {
    $book_id = $_POST['book_id'];
    $user_id = $_SESSION['user_id'];
    $message = $utlåningsHanterare->lånaBok($book_id, $user_id);
    echo "<p>$message</p>";
}

// Sökning
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $sql = "SELECT * FROM Böcker WHERE (titel LIKE '%$search%' OR författare LIKE '%$search%' OR ISBN LIKE '%$search%') AND tillgängliga_exemplar > 0";
} else {
    $sql = "SELECT * FROM Böcker WHERE tillgängliga_exemplar > 0";
}

// Sortering
if (isset($_GET['sort'])) {
    $sort = $_GET['sort'];
    if ($sort === 'asc') {
        $sql .= " ORDER BY titel ASC";
    } elseif ($sort === 'desc') {
        $sql .= " ORDER BY titel DESC";
    }
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bläddra Bland Böcker</title>
    <link rel="stylesheet" href="lona.css">
    <link href="https://fonts.cdnfonts.com/css/playfair-display" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/raleway-5" rel="stylesheet">
    <style>
        /* Lägg till CSS-stilar för presentation */
    </style>
</head>
<body>

    <div class="navbar">
        <div class="titel" onclick="document.location='bibliotek.php'"><h2>Bibliotek</h2></div>
        <div class="knappar">
            <div class="sok hover" onclick="document.location='search.php'"><i class="fa fa-search"></i></div>
            <div class="knapp hover" onclick="document.location='logout.php'"><h3>Logga Ut</h3></div>
            <div class="knapp hover" onclick="document.location='minalon.php'"><h3>Mina Lån</h3></div>
        </div>
    </div>

    <div class="topmain">
        <h2>Bläddra Bland Böcker</h2>
        
        <form action="lona.php" method="get">
            <input type="text" name="search" placeholder="Sök efter en bok">
            <button type="submit">Sök</button>
        </form>

        <form action="lona.php" method="get">
            <input type="hidden" name="search" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <label for="sort">Sortera:</label>
            <select name="sort" id="sort" onchange="this.form.submit()">
                <option value="asc" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'asc') echo 'selected'; ?>>A-Ö</option>
                <option value="desc" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'desc') echo 'selected'; ?>>Ö-A</option>
            </select>

        </form> 
    </div>
    <div class="botmain">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { ?>

                <div class="holder">
                    <img src="<?=$row['bild']?>" alt="">
                    <div class="info">
                        <div class="namn"><h3><?=$row['titel']?></h3></div>
                        <div class="skribent"><p><?=$row['författare']?>, <?=$row['utgivningsår']?></p></div>
                    </div>
                    <form action="lona.php" method="post">
                        <input type="hidden" name="book_id" value="<?=$row['bok_id']?>">
                        <input type="submit" name="loan" id="loan" value="Låna">
                    </form>
                    <div class="barcode"><p><?=$row['ISBN']?></p></div>
                </div>
                <?php
            }
        } else {
            echo "Inga böcker hittades.";
        }
        ?>
    </div>

    <p><a href="biblotek.php">Tillbaka till startsidan</a></p>
</body>
</html>
