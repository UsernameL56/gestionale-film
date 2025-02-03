<?php
require "db.php";
require "accessoDB.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $release_date = trim($_POST['release_date']);
    $description = trim($_POST['description']);
    $duration = trim($_POST['duration']);
    $genre_id = intval($_POST['genre_id']);
    $language_id = intval($_POST['language_id']);

    $cover = $_FILES['cover']['name'];
    $target_dir = "img/";
    $target_file = $target_dir . basename($cover);

    if (move_uploaded_file($_FILES['cover']['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO filmDB_Films (Titolo, Data_Uscita, Descrizione, Durata, Copertina, ID_Genere, ID_Lingua) 
                                VALUES (:title, :release_date, :description, :duration, :cover, :genre_id, :language_id)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':release_date', $release_date);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':duration', $duration);
        $stmt->bindParam(':cover', $cover);
        $stmt->bindParam(':genre_id', $genre_id);
        $stmt->bindParam(':language_id', $language_id);

        if ($stmt->execute()) {
            $message = "Film aggiunto con successo!";
        } else {
            $message = "Errore durante l'aggiunta del film.";
        }
    } else {
        $message = "Errore durante il caricamento della copertina.";
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aggiungi Film</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            padding: 20px;
        }

        .btn-back {
            position: absolute;
            top: 20px;
            left: 20px;
        }

        .form-label {
            font-weight: bold;
        }

        .container {
            max-width: 800px;
        }
            .alert {
            transition: opacity 1s ease-in-out;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const alertBox = document.querySelector(".alert");
            if (alertBox) {
                setTimeout(() => {
                    alertBox.style.opacity = "0";
                    setTimeout(() => alertBox.remove(), 1000);
                }, 3000);
            }
        });
    </script>
</head>

<body>
<nav class="navbar navbar-expand-lg" style="background-color: rgb(17, 17, 17);">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="index.php">
            <img src="img/filmDB_Logo.png" alt="Logo" style="height: 80px;">
        </a>

        <!-- Toggler per dispositivi mobili -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Contenuto della navbar -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Dropdown menu utente -->
            <div class="dropdown ms-auto">
                <button class="btn dropdown-toggle text-light" type="button" id="menuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="img/user.png" alt="Menu" style="height: 35px;">
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="menuDropdown">
                    <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container my-4">
    <a href="admin.php" class="btn btn-secondary mb-3">&larr; Torna al Pannello Admin</a>
    <h1 class="text-center mb-4">Aggiungi Film</h1>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"> <?= htmlspecialchars($message) ?> </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Titolo</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="release_date" class="form-label">Data di Uscita</label>
                <input type="date" class="form-control" id="release_date" name="release_date" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Descrizione</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="duration" class="form-label">Durata (minuti)</label>
                <input type="text" class="form-control" id="duration" name="duration" required>
            </div>
            <div class="mb-3">
                <label for="cover" class="form-label">Copertina</label>
                <input type="file" class="form-control" id="cover" name="cover" required>
            </div>
            <div class="mb-3">
                <label for="genre_id" class="form-label">Genere</label>
                <select class="form-select" id="genre_id" name="genre_id" required>
                    <?php
                    $genresStmt = $conn->query("SELECT ID, Nome FROM filmDB_Generi");
                    while ($genre = $genresStmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . $genre['ID'] . "'>" . htmlspecialchars($genre['Nome']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="language_id" class="form-label">Lingua</label>
                <select class="form-select" id="language_id" name="language_id" required>
                    <?php
                    $languagesStmt = $conn->query("SELECT ID, Nome FROM filmDB_Lingue");
                    while ($language = $languagesStmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . $language['ID'] . "'>" . htmlspecialchars($language['Nome']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-warning w-100">Aggiungi Film</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>

