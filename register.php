<?php
session_start();

// Klassen för användarregistrering
class AnvändarReg {
    private $conn; // Databasanslutningsobjektet

    // Konstruktorn tar emot databasanslutningen och sparar den i klassen
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Metod för att utföra användarregistrering
    public function registerUser($username, $phone, $email, $password) {
        // Escapar användarinput för att förhindra SQL-injektioner
        $username = mysqli_real_escape_string($this->conn, $username);
        $phone = mysqli_real_escape_string($this->conn, $phone);
        $email = mysqli_real_escape_string($this->conn, $email);
        $password = mysqli_real_escape_string($this->conn, $password);

        // Kryptera lösenordet
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Lägg till användaren i databasen
        $sql = "INSERT INTO Användare (namn, telefon, epost, losen) VALUES ('$username', '$phone', '$email', '$hashed_password')";
        if (mysqli_query($this->conn, $sql)) {
            return true; // Returnera true om registreringen lyckas
        } else {
            return "Det uppstod ett fel vid registreringen."; // Returnera felmeddelande om det uppstår ett fel
        }
    }
}

include_once "dbconn.php"; // Inkludera filen med databasanslutningen
$registrera = new AnvändarReg($conn); // Skapa en instans av AnvändarReg med databasanslutningen

// Om formuläret har skickats med POST-metoden
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Utför användarregistrering och få eventuellt felmeddelande
    $registration_result = $registrera->registerUser($_POST['username'], $_POST['phone'], $_POST['email'], $_POST['password']);

    // Om registreringen lyckades, sätt sessionvariabel och omdirigera till inloggningssidan
    if ($registration_result === true) {
        $_SESSION['user_registered'] = true;
        header("Location: login.php");
        exit();
    } else {
        $error = $registration_result; // Annars, sätt felmeddelandet för att visa på sidan
    }
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrering</title>
    <link rel="stylesheet" href="register.css"> <!-- Länk till CSS-filen för styling -->
    <link href="https://fonts.cdnfonts.com/css/playfair-display" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/raleway-5" rel="stylesheet">
</head>
<body>

    <!-- Formulär för hemknapp -->
    <form action="landing.php" method="get" class="hem">
        <button type="submit" class="hemknapp">🏚️</button>
    </form>

    <div class="container">
        <h2>Registrera dig</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
            <input type="text" class="inputfält" name="username" placeholder="Användarnamn" required><br>
            <input type="text" class="inputfält" name="phone" placeholder="Telefon" required><br>
            <input type="email" class="inputfält" name="email" placeholder="E-post" required><br>
            <input type="password" class="inputfält" name="password" placeholder="Lösenord" required><br>
            <input type="submit" class="button" value="Registrera"> 
        </form>
        <p>Har du inget konto? <a href="login.php">Logga in istället 😁</a></p>
        <!-- Visar eventuellt felmeddelande -->

        <?php if (isset($error)) { echo $error; } ?>
    </div>
</body>
</html>
