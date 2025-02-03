<?php
require "db.php";
require "accessoDB.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

$message = "";

// Eliminazione genere
if (isset($_POST['delete_genre'])) {
    $genreId = intval($_POST['genre_id']);
    $stmt = $conn->prepare("DELETE FROM filmDB_Generi WHERE ID = :id");
    $stmt->bindParam(':id', $genreId);

    if ($stmt->execute()) {
        $message = "Genere eliminato con successo.";
    } else {
        $message = "Errore durante l'eliminazione del genere.";
    }
}

//header("Location: gestione_generi_lingue.php?message=" . urlencode($message));
// Modifica genere
if (isset($_POST['edit_genre'])) {
    $genreId = intval($_POST['genre_id']);
    $genreName = trim($_POST['genre_name']);
    $stmt = $conn->prepare("UPDATE filmDB_Generi SET Nome = :name WHERE ID = :id");
    $stmt->bindParam(':name', $genreName);
    $stmt->bindParam(':id', $genreId);

    if ($stmt->execute()) {
        $message = "Genere modificato con successo.";
    } else {
        $message = "Errore durante la modifica del genere.";
    }
}

// Visualizzazione generi
$genresStmt = $conn->query("SELECT * FROM filmDB_Generi");
$genres = $genresStmt->fetchAll(PDO::FETCH_ASSOC);

// Eliminazione lingua
if (isset($_POST['delete_language'])) {
    $languageId = intval($_POST['language_id']);
    $stmt = $conn->prepare("DELETE FROM filmDB_Lingue WHERE ID = :id");
    $stmt->bindParam(':id', $languageId);

    if ($stmt->execute()) {
        $message = "Lingua eliminata con successo.";
    } else {
        $message = "Errore durante l'eliminazione della lingua.";
    }
}

// Modifica lingua
if (isset($_POST['edit_language'])) {
    $languageId = intval($_POST['language_id']);
    $languageName = trim($_POST['language_name']);
    $stmt = $conn->prepare("UPDATE filmDB_Lingue SET Nome = :name WHERE ID = :id");
    $stmt->bindParam(':name', $languageName);
    $stmt->bindParam(':id', $languageId);

    if ($stmt->execute()) {
        $message = "Lingua modificata con successo.";
    } else {
        $message = "Errore durante la modifica della lingua.";
    }
}

// Modifica film
if (isset($_POST['edit_film'])) {
    $filmId = intval($_POST['film_id']);
    $title = trim($_POST['title']);
    $genreId = intval($_POST['genre_id']);
    $languageId = intval($_POST['language_id']);
    $releaseDate = trim($_POST['release_date']);
    $description = trim($_POST['description']);
    $duration = trim($_POST['duration']); // Ora trattata come stringa

    $stmt = $conn->prepare("UPDATE filmDB_Films SET Titolo = :title, ID_Genere = :genre_id, ID_Lingua = :language_id, Data_Uscita = :release_date, Descrizione = :description, Durata = :duration WHERE ID = :id");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':genre_id', $genreId);
    $stmt->bindParam(':language_id', $languageId);
    $stmt->bindParam(':release_date', $releaseDate);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':duration', $duration);
    $stmt->bindParam(':id', $filmId);

    if ($stmt->execute()) {
        $message = "Film modificato con successo.";
    } else {
        $message = "Errore durante la modifica del film.";
    }
}



// Visualizzazione lingue
$languagesStmt = $conn->query("SELECT * FROM filmDB_Lingue");
$languages = $languagesStmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['delete_film'])) {
    $filmId = intval($_POST['film_id']);

    // Elimina i commenti associati
    $deleteCommentsStmt = $conn->prepare("DELETE FROM filmDB_Commenti WHERE ID_Film = :id");
    $deleteCommentsStmt->bindParam(':id', $filmId);
    $deleteCommentsStmt->execute();

    // Elimina le preferenze associate
    $deletePreferencesStmt = $conn->prepare("DELETE FROM filmDB_Preferenze WHERE ID_Film = :id");
    $deletePreferencesStmt->bindParam(':id', $filmId);
    $deletePreferencesStmt->execute();

    // Elimina il film
    $deleteFilmStmt = $conn->prepare("DELETE FROM filmDB_Films WHERE ID = :id");
    $deleteFilmStmt->bindParam(':id', $filmId);

    if ($deleteFilmStmt->execute()) {
        $message = "Film eliminato con successo.";
    } else {
        $message = "Errore durante l'eliminazione del film.";
    }
}


// Visualizzazione film
$filmsStmt = $conn->query("SELECT f.ID, f.Titolo, g.Nome AS Genere, l.Nome AS Lingua, f.Data_Uscita, f.Descrizione, f.Durata
                           FROM filmDB_Films f 
                           JOIN filmDB_Generi g ON f.ID_Genere = g.ID 
                           JOIN filmDB_Lingue l ON f.ID_Lingua = l.ID");
$films = $filmsStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestione Generi e Lingue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .table-container {
            margin-top: 20px;
        }

        .btn-small {
            padding: 0.4rem 0.8rem;
            font-size: 0.9rem;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .navbar {
            margin-bottom: 20px;
        }

        h2 {
            margin-top: 30px;
            color: #007bff;
        }

        .table {
            border: 1px solid #dee2e6;
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

<div class="container my-5">
    <a href="admin.php" class="btn btn-secondary mb-4">&larr; Torna al Pannello Admin</a>
    <h1 class="text-center">Gestione Generi e Lingue</h1>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"> <?= htmlspecialchars($message) ?> </div>
    <?php endif; ?>

<div class="table-container">
    <h2>Generi</h2>
    <table class="table table-hover table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($genres as $genre): ?>
                <tr>
                    <form method="POST">
                        <td><?= htmlspecialchars($genre['ID']) ?></td>
                        <td>
                            <input type="text" class="form-control" name="genre_name" value="<?= htmlspecialchars($genre['Nome']) ?>" required>
                        </td>
                        <td class="action-buttons">
                            <input type="hidden" name="genre_id" value="<?= $genre['ID'] ?>">
                            <button type="submit" name="edit_genre" class="btn btn-primary btn-small">Modifica</button>
                            <button type="submit" name="delete_genre" class="btn btn-danger btn-small">Elimina</button>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="table-container">
    <h2>Lingue</h2>
    <table class="table table-hover table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($languages as $language): ?>
                <tr>
                    <form method="POST">
                        <td><?= htmlspecialchars($language['ID']) ?></td>
                        <td>
                            <input type="text" class="form-control" name="language_name" value="<?= htmlspecialchars($language['Nome']) ?>" required>
                        </td>
                        <td class="action-buttons">
                            <input type="hidden" name="language_id" value="<?= $language['ID'] ?>">
                            <button type="submit" name="edit_language" class="btn btn-primary btn-small">Modifica</button>
                            <button type="submit" name="delete_language" class="btn btn-danger btn-small">Elimina</button>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
    <div class="table-container">
    <h2>Film</h2>
    <table class="table table-hover table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Titolo</th>
                <th>Genere</th>
                <th>Lingua</th>
                <th>Data Uscita</th>
                <th>Descrizione</th>
                <th>Durata</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($films as $film): ?>
                <tr>
                    <form method="POST" class="d-inline">
                        <td><?= htmlspecialchars($film['ID']) ?></td>
                        <td>
                            <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($film['Titolo']) ?>" required>
                        </td>
                        <td>
                            <select class="form-select" name="genre_id" required>
                                <?php foreach ($genres as $genre): ?>
                                    <option value="<?= $genre['ID'] ?>" <?= $film['Genere'] === $genre['Nome'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($genre['Nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select class="form-select" name="language_id" required>
                                <?php foreach ($languages as $language): ?>
                                    <option value="<?= $language['ID'] ?>" <?= $film['Lingua'] === $language['Nome'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($language['Nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <input type="date" class="form-control" name="release_date" value="<?= htmlspecialchars($film['Data_Uscita']) ?>" required>
                        </td>
                        <td>
                            <textarea class="form-control" name="description" rows="2"><?= htmlspecialchars($film['Descrizione']) ?></textarea>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="duration" value="<?= htmlspecialchars($film['Durata']) ?>" required>
                        </td>
                        <td class="action-buttons">
                            <input type="hidden" name="film_id" value="<?= $film['ID'] ?>">
                            <button type="submit" name="edit_film" class="btn btn-primary btn-small">Modifica</button>
                            <button type="submit" name="delete_film" class="btn btn-danger btn-small">Elimina</button>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>



</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>
</body>

</html>
