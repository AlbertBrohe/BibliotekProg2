<?php
// Starta sessionen om den inte redan är startad
session_start();

// Avsluta sessionen genom att tömma alla sessionvariabler
$_SESSION = array();

// Förstör sessionen
session_destroy();

// Omdirigera användaren tillbaka till startsidan eller inloggningssidan
header("Location: landing.php"); 
exit();
?>
