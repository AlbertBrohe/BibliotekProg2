<?php
session_start();

// Klassen för att hantera böcker
class Bokhanterare {
    private $conn; // Databasanslutningsobjektet

    // Konstruktorn tar emot databasanslutningen och sparar den i klassen
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Metod för att söka böcker baserat på titel, författare eller ISBN
    public function searchBooks($search, $sort) {
        $search = mysqli_real_escape_string($this->conn, $search);

        // Konstruera SQL-frågan baserat på söktermen och sorteringen
        $sql = "SELECT * FROM Böcker WHERE (titel LIKE '%$search%' OR författare LIKE '%$search%' OR ISBN LIKE '%$search%') AND tillgängliga_exemplar > 0";

        if ($sort === 'asc') {
            $sql .= " ORDER BY titel ASC";
        } elseif ($sort === 'desc') {
            $sql .= " ORDER BY titel DESC";
        }

        // Kör SQL-frågan
        $result = $this->conn->query($sql);

        return $result;
    }
}

include 'dbconn.php'; // Inkludera filen med databasanslutningen
$Bokhanterare = new Bokhanterare($conn); // Skapa en instans av Bokhanterare med databasanslutningen

// Sökning
$search = isset($_GET['search']) ? $_GET['search'] : ''; // Hämta söktermen från GET-parametern
$sort = isset($_GET['sort']) ? $_GET['sort'] : ''; // Hämta sorteringen från GET-parametern
$result = $Bokhanterare->searchBooks($search, $sort); // Sök efter böcker

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
</head>
<body>

    <div class="navbar">
        <div class="titel" onclick="document.location='landing.php'"><h2>Bibliotek</h2></div>
        <div class="knappar">
            <div class="knapp hover" onclick="document.location='login.php'"><h3>Logga In</h3></div>
        </div>
    </div>

    <div class="topmain">
        <h2>Bläddra Bland Böcker</h2>
        
        <form action="search.php" method="get">
            <input type="text" name="search" placeholder="Sök efter en bok">
            <button type="submit">Sök</button>
        </form>

        <form action="search.php" method="get">
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
            <label for="sort">Sortera:</label>
            <select name="sort" id="sort" onchange="this.form.submit()">
                <option value="asc" <?php if($sort === 'asc') echo 'selected'; ?>>A-Ö</option>
                <option value="desc" <?php if($sort === 'desc') echo 'selected'; ?>>Ö-A</option>
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
                    <div class="barcode"><p><?=$row['ISBN']?></p></div>
                </div>
            <?php
            }
        } else {
            echo "Inga böcker hittades.";
        }
        ?>
    </div>

    <p><a href="biblotek.php">
