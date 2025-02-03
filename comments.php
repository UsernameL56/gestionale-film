<?php
require "db.php";
require "accessoDB.php";
session_start();

// Controllo che l'utente sia loggato
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$filmId = $_GET['film_id'] ?? null;

if (!$filmId) {
    echo "Film ID not provided.";
    exit;
}

// Recupera i dettagli del film
$filmStmt = $conn->prepare("SELECT Titolo FROM filmDB_Films WHERE ID = :film_id");
$filmStmt->bindParam(':film_id', $filmId, PDO::PARAM_INT);
$filmStmt->execute();
$film = $filmStmt->fetch(PDO::FETCH_ASSOC);

if (!$film) {
    echo "Film not found.";
    exit;
}

// Gestione inserimento commento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $stars = $_POST['stars'] ?? 0;
    $comment = $_POST['comment'];

    $insertStmt = $conn->prepare(
        "INSERT INTO filmDB_Commenti (Stelle, Commento, ID_User, ID_Film) VALUES (:stars, :comment, :user_id, :film_id)"
    );
    $insertStmt->bindParam(':stars', $stars, PDO::PARAM_INT);
    $insertStmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $insertStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $insertStmt->bindParam(':film_id', $filmId, PDO::PARAM_INT);
    $insertStmt->execute();
}

// Recupera i commenti del film
$commentsStmt = $conn->prepare(
    "SELECT c.Stelle, c.Commento, u.Nome, u.Cognome 
    FROM filmDB_Commenti c 
    JOIN filmDB_Users u ON c.ID_User = u.ID 
    WHERE c.ID_Film = :film_id 
    ORDER BY c.ID DESC"
);
$commentsStmt->bindParam(':film_id', $filmId, PDO::PARAM_INT);
$commentsStmt->execute();
$comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Comments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container my-4">
            <div class="mb-3">
            <a href="index.php" class="btn btn-outline-secondary">&larr; Back to Films</a>
        </div>
        <h1 class="text-center">Comments for <?= htmlspecialchars($film['Titolo']) ?></h1>

        <!-- Form per aggiungere un commento -->
        <div class="card my-4">
            <div class="card-body">
                <h5 class="card-title">Add a Comment</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label for="stars" class="form-label">Rating (1-5)</label>
                        <select class="form-select" id="stars" name="stars" required>
                            <option value="1">1 Star</option>
                            <option value="2">2 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="5">5 Stars</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>

        <!-- Elenco commenti -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Existing Comments</h5>
                <?php if (count($comments) > 0): ?>
                <ul class="list-group">
                    <?php foreach ($comments as $comment): ?>
                    <li class="list-group-item">
                        <strong><?= htmlspecialchars($comment['Nome'] . ' ' . $comment['Cognome']) ?></strong>
                        (<?= $comment['Stelle'] ?> Stars)
                        <p><?= htmlspecialchars($comment['Commento']) ?></p>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p>No comments yet. Be the first to comment!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>
