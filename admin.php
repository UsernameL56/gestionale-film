<?php
session_start();
require "db.php";
require "accessoDB.php";

// Controllo se l'utente è admin
/*if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit;
}*/

// Recupera i generi, le lingue e i film per il campo "seguito"
$genres = $conn->query("SELECT ID, Nome FROM filmDB_Generi")->fetchAll(PDO::FETCH_ASSOC);
$languages = $conn->query("SELECT ID, Nome FROM filmDB_Lingue")->fetchAll(PDO::FETCH_ASSOC);
$films = $conn->query("SELECT ID, Titolo FROM filmDB_Films")->fetchAll(PDO::FETCH_ASSOC);

// Gestione form per aggiungere un film
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titolo = $_POST['titolo'];
    $data_uscita = $_POST['data_uscita'];
    $descrizione = $_POST['descrizione'];
    $durata = $_POST['durata'];
    $id_genere = $_POST['id_genere'];
    $id_lingua = $_POST['id_lingua'];
    $id_seguito = !empty($_POST['id_seguito']) ? $_POST['id_seguito'] : null;

    // Gestione upload immagine
    $upload_dir = "img/";
    $copertina = $_FILES['copertina']['name'];
    $target_file = $upload_dir . basename($copertina);
    $upload_ok = move_uploaded_file($_FILES['copertina']['tmp_name'], $target_file);

    if ($upload_ok) {
        $stmt = $conn->prepare("INSERT INTO filmDB_Films (Titolo, Data_Uscita, Descrizione, Durata, Copertina, ID_Genere, ID_Lingua, ID_Seguito) 
                                VALUES (:titolo, :data_uscita, :descrizione, :durata, :copertina, :id_genere, :id_lingua, :id_seguito)");
        $stmt->bindParam(':titolo', $titolo);
        $stmt->bindParam(':data_uscita', $data_uscita);
        $stmt->bindParam(':descrizione', $descrizione);
        $stmt->bindParam(':durata', $durata);
        $stmt->bindParam(':copertina', $copertina);
        $stmt->bindParam(':id_genere', $id_genere, PDO::PARAM_INT);
        $stmt->bindParam(':id_lingua', $id_lingua, PDO::PARAM_INT);
        $stmt->bindParam(':id_seguito', $id_seguito, PDO::PARAM_INT | PDO::PARAM_NULL);
        $stmt->execute();

        $success_message = "Film aggiunto con successo!";
    } else {
        $error_message = "Errore durante il caricamento dell'immagine.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Admin Panel - Aggiungi Film</h1>
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="titolo" class="form-label">Titolo</label>
                <input type="text" class="form-control" id="titolo" name="titolo" required>
            </div>
            <div class="mb-3">
                <label for="data_uscita" class="form-label">Data di Uscita</label>
                <input type="date" class="form-control" id="data_uscita" name="data_uscita" required>
            </div>
            <div class="mb-3">
                <label for="descrizione" class="form-label">Descrizione</label>
                <textarea class="form-control" id="descrizione" name="descrizione" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="durata" class="form-label">Durata (minuti)</label>
                <input type="text" class="form-control" id="durata" name="durata" required>
            </div>
            <div class="mb-3">
                <label for="id_genere" class="form-label">Genere</label>
                <select class="form-select" id="id_genere" name="id_genere" required>
                    <?php foreach ($genres as $genre): ?>
                        <option value="<?= $genre['ID'] ?>"><?= $genre['Nome'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="id_lingua" class="form-label">Lingua</label>
                <select class="form-select" id="id_lingua" name="id_lingua" required>
                    <?php foreach ($languages as $language): ?>
                        <option value="<?= $language['ID'] ?>"><?= $language['Nome'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="id_seguito" class="form-label">Seguito (opzionale)</label>
                <select class="form-select" id="id_seguito" name="id_seguito">
                    <option value="">Nessuno</option>
                    <?php foreach ($films as $film): ?>
                        <option value="<?= $film['ID'] ?>"><?= $film['Titolo'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="copertina" class="form-label">Copertina</label>
                <input type="file" class="form-control" id="copertina" name="copertina" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Aggiungi Film</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
