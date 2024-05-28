<?php
session_start();

// Klassen för autentisering av användaren
class AnvändarAuth {
    private $conn; // Databasanslutningsobjektet

    // Konstruktorn tar emot databasanslutningen och sparar den i klassen
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Metod för att utföra inloggning
    public function login($username, $password) {
        // Escapar användarinput för att förhindra SQL-injektioner
        $username = mysqli_real_escape_string($this->conn, $username);

        // SQL-fråga för att hämta användarinfo från databasen
        $sql = "SELECT * FROM Användare WHERE namn = '$username'";
        $result = mysqli_query($this->conn, $sql);

        // Om resultatet finns och det finns en matchande användare
        if ($result && mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            // Jämför lösenordet med det krypterade lösenordet i databasen
            if (password_verify($password, $row['losen'])) {
                // Sätt sessionvariabler för inloggad användare
                $_SESSION['customer_logged_in'] = true;
                $_SESSION['user_id'] = $row['användar_id'];
                $_SESSION['username'] = $row['namn'];

                // Kontrollera om användaren är administratör
                if ($row['användar_id'] == 1) {
                    $_SESSION['admin_logged_in'] = true;
                    header("Location: admin.php"); // Omdirigera till adminsidan
                } else {
                    header("Location: bibliotek.php"); // Omdirigera till kundsidan
                }
                exit(); // Avsluta skriptet efter omdirigeringen
            } else {
                return "Fel användarnamn eller lösenord!"; // Felmeddelande vid fel lösenord
            }
        } else {
            return "Fel användarnamn eller lösenord!"; // Felmeddelande om användaren inte hittas
        }
    }
}

include_once "dbconn.php"; // Inkludera filen med databasanslutningen
$authenticator = new AnvändarAuth($conn); // Skapa en instans av AnvändarAuth med databasanslutningen

// Om formuläret har skickats med POST-metoden
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error = $authenticator->login($_POST['username'], $_POST['password']); // Utför inloggningen och få eventuellt felmeddelande
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kundinloggning</title>
    <link rel="stylesheet" href="login.css"> <!-- Länk till CSS-filen för styling -->
    <link href="https://fonts.cdnfonts.com/css/playfair-display" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/raleway-5" rel="stylesheet">
</head>
<body>
    <form action="landing.php" method="get" class="hem">
        <button type="submit" class="hemknapp">🏚️</button> <!-- Hemknapp som leder till landing.php -->
    </form>
    <div class="container">
        <h2>Logga in</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="text" class="inputfält" id="username" name="username" placeholder="Användarnamn" required><br>
            <input type="password" class="inputfält" id="password" name="password" placeholder="Lösenord" required><br>
            <input type="submit" class="button" value="Logga in">
        </form>
        <p>Har du inget konto? <a href="register.php">Registrera dig här</a></p>
        <?php if (isset($error)) { echo '<div class="error">' . $error . '</div>'; } ?> <!-- Visa felmeddelande om det finns ett fel -->
    </div>
</body>
</html>
