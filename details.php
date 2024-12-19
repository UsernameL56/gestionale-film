<?php
require "db.php";
require "accessoDB.php";
session_start();

if (isset($_GET['id'])) {
    $filmID = intval($_GET['id']); // Sanitizza il valore
} else {
    echo "Nessun ID ricevuto.";
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

if ($filmID) {
    // Query per ottenere i dettagli del film
    $query = "SELECT f.ID, f.Titolo, f.Data_Uscita, f.Descrizione, f.Copertina, g.Nome AS Genere, l.Nome AS Lingua
              FROM filmDB_Films f
              JOIN filmDB_Generi g ON f.ID_Genere = g.ID
              JOIN filmDB_Lingue l ON f.ID_Lingua = l.ID
              WHERE f.ID = :filmID";

    $statement = $conn->prepare($query);
    $statement->bindParam(':filmID', $filmID, PDO::PARAM_INT);
    $statement->execute();

    // Recupera i dettagli del film
    $film = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$film) {
        echo "Film non trovato.";
        exit;
    }

    // Gestione inserimento commento
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
        $stars = $_POST['stars'] ?? 0;
        $comment = $_POST['comment'];

        $insertStmt = $conn->prepare(
            "INSERT INTO filmDB_Commenti (Stelle, Commento, ID_User, ID_Film) VALUES (:stars, :comment, :user_id, :film_id)"
        );
        $insertStmt->bindParam(':stars', $stars, PDO::PARAM_STR);
        $insertStmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $insertStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $insertStmt->bindParam(':film_id', $filmID, PDO::PARAM_INT);
        $insertStmt->execute();

        // Reindirizza per prevenire il reinvio del form
        header("Location: details.php?id=$filmID");
        exit;
    }

    // Query per ottenere i commenti del film
    $commentsQuery = "SELECT c.Stelle, c.Commento, u.Nome, u.Cognome 
                      FROM filmDB_Commenti c 
                      JOIN filmDB_Users u ON c.ID_User = u.ID 
                      WHERE c.ID_Film = :filmID 
                      ORDER BY c.ID DESC";

    $commentsStmt = $conn->prepare($commentsQuery);
    $commentsStmt->bindParam(':filmID', $filmID, PDO::PARAM_INT);
    $commentsStmt->execute();

    // Recupera i commenti del film
    $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dettagli Film</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .rating {
            direction: rtl;
            unicode-bidi: bidi-override;
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }

        .rating > label {
            font-size: 1.5rem;
            color: #ddd;
            cursor: pointer;
            margin-right: 5px;
        }

        .rating > input {
            display: none;
        }

        .rating > input:checked ~ label,
        .rating > input:checked ~ label ~ label {
            color: #ffca28;
        }

        .compact-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .compact-form textarea {
            flex: 1;
            min-width: 200px;
        }

        .display-rating {
            color: #ffca28;
            font-size: 1.2rem;
        }
    </style>
</head>

<body>
    <div class="container my-4">
      <div class="mb-3">
        <a href="index.php" class="btn btn-outline-secondary">&larr; Back to Films</a>
      </div>
        <!-- Dettagli del film -->
        <div class="card mb-4">
            <div class="row g-0">
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <img src="<?= $film['Copertina'] ? 'img/' . $film['Copertina'] : 'img/default.jpg' ?>" class="img-fluid rounded-start" alt="<?= htmlspecialchars($film['Titolo']) ?>">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($film['Titolo']) ?></h5>
                        <p class="card-text"><strong>Data di uscita:</strong> <?= htmlspecialchars($film['Data_Uscita']) ?></p>
                        <p class="card-text"><strong>Genere:</strong> <?= htmlspecialchars($film['Genere']) ?></p>
                        <p class="card-text"><strong>Lingua:</strong> <?= htmlspecialchars($film['Lingua']) ?></p>
                        <p class="card-text"><strong>Descrizione:</strong> <?= htmlspecialchars($film['Descrizione']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form per aggiungere un commento -->
        <div class="card my-4">
            <div class="card-body">
                <h5 class="card-title">Aggiungi un commento</h5>
                <form method="POST" class="compact-form">
                    <div class="rating">
                        <input type="radio" name="stars" id="star5" value="5" required><label for="star5">&#9733;</label>
                        <input type="radio" name="stars" id="star4" value="4"><label for="star4">&#9733;</label>
                        <input type="radio" name="stars" id="star3" value="3"><label for="star3">&#9733;</label>
                        <input type="radio" name="stars" id="star2" value="2"><label for="star2">&#9733;</label>
                        <input type="radio" name="stars" id="star1" value="1"><label for="star1">&#9733;</label>
                    </div>
                    <textarea class="form-control" id="comment" name="comment" rows="1" placeholder="Scrivi il tuo commento qui..." required></textarea>
                    <button type="submit" class="btn btn-primary">Invia</button>
                </form>
            </div>
        </div>

        <!-- Commenti del film -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Commenti</h5>
                <?php if (count($comments) > 0): ?>
                    <ul class="list-group">
                        <?php foreach ($comments as $comment): ?>
                            <li class="list-group-item">
                                <strong><?= htmlspecialchars($comment['Nome'] . ' ' . $comment['Cognome']) ?></strong>
                                <div class="display-rating">
                                    <?php for ($i = 0; $i < $comment['Stelle']; $i++): ?>
                                        &#9733;
                                    <?php endfor; ?>
                                </div>
                                <p><?= htmlspecialchars($comment['Commento']) ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">Nessun commento disponibile. Sii il primo a commentare!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
