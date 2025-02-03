<?php
session_start();
require "db.php";
require "accessoDB.php";

// Controllo se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Recupera le informazioni dell'utente dal database
$stmt = $conn->prepare("SELECT Nome, Cognome, Email FROM filmDB_Users WHERE ID = :id");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Errore: utente non trovato.";
    exit;
}

// Gestione aggiornamento profilo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];

    // Aggiorna le informazioni dell'utente nel database
    $updateStmt = $conn->prepare("UPDATE filmDB_Users SET Nome = :nome, Cognome = :cognome, Email = :email WHERE ID = :id");
    $updateStmt->bindParam(':nome', $nome);
    $updateStmt->bindParam(':cognome', $cognome);
    $updateStmt->bindParam(':email', $email);
    $updateStmt->bindParam(':id', $user_id, PDO::PARAM_INT);

    if ($updateStmt->execute()) {
        $success_message = "Profilo aggiornato con successo.";
        // Aggiorna i dati nella variabile $user
        $user['Nome'] = $nome;
        $user['Cognome'] = $cognome;
        $user['Email'] = $email;
    } else {
        $error_message = "Errore durante l'aggiornamento del profilo.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilo Utente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Profilo Utente</h1>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($user['Nome']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="cognome" class="form-label">Cognome</label>
                <input type="text" class="form-control" id="cognome" name="cognome" value="<?= htmlspecialchars($user['Cognome']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['Email']) ?>" required>
            </div>
        </form>

        <a href="index.php" class="btn btn-secondary mt-3">Torna alla Home</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
