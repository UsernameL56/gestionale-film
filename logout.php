<?php
session_start();

// Distrugge tutte le variabili di sessione
session_unset();

// Distrugge la sessione
session_destroy();

// Reindirizza alla pagina di login (o homepage)
header("Location: login.php?message=logged_out");

exit;
?>
