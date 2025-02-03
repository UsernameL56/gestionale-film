<?php
require "db.php";
require "accessoDB.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Messaggi di conferma o errore
$message = "";

// Gestione dell'aggiunta di un genere
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_genre'])) {
    $genreName = trim($_POST['genre_name']);

    if (!empty($genreName)) {
        $stmt = $conn->prepare("INSERT INTO filmDB_Generi (Nome) VALUES (:genre_name)");
        $stmt->bindParam(':genre_name', $genreName);

        if ($stmt->execute()) {
            $message = "Genere aggiunto con successo.";
        } else {
            $message = "Errore durante l'aggiunta del genere.";
        }
    } else {
        $message = "Il nome del genere non può essere vuoto.";
    }
}

// Gestione dell'aggiunta di una lingua
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_language'])) {
    $languageName = trim($_POST['language_name']);

    if (!empty($languageName)) {
        $stmt = $conn->prepare("INSERT INTO filmDB_Lingue (Nome) VALUES (:language_name)");
        $stmt->bindParam(':language_name', $languageName);

        if ($stmt->execute()) {
            $message = "Lingua aggiunta con successo.";
        } else {
            $message = "Errore durante l'aggiunta della lingua.";
        }
    } else {
        $message = "Il nome della lingua non può essere vuoto.";
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aggiungi Generi e Lingue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	    <style>
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            padding: 20px;
        }

        .btn-back {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: bold;
        }

        .container {
            max-width: 1000px;
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
        <a href="admin.php" class="btn btn-secondary btn-back">&larr; Torna al Pannello Admin</a>
        <h1 class="text-center">Aggiungi Generi e Lingue</h1>

        <?php if (!empty($message)): ?>
            <div class="alert alert-info"> <?= htmlspecialchars($message) ?> </div>
        <?php endif; ?>

        <div class="row">
            <!-- Aggiungi Genere -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Aggiungi Genere</h5>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="genre_name" class="form-label">Nome del Genere</label>
                                <input type="text" class="form-control" id="genre_name" name="genre_name" required>
                            </div>
                            <button type="submit" name="add_genre" class="btn btn-warning">Aggiungi</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Aggiungi Lingua -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Aggiungi Lingua</h5>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="language_name" class="form-label">Nome della Lingua</label>
                                <input type="text" class="form-control" id="language_name" name="language_name" required>
                            </div>
                            <button type="submit" name="add_language" class="btn btn-warning">Aggiungi</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
