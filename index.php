<?php
require "db.php";
require "accessoDB.php";

session_start();

// Controlla se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Barra di ricerca
$searchQuery = $_GET['search'] ?? '';
$query = "SELECT f.ID, f.Titolo, f.Data_Uscita, f.Descrizione, f.Copertina, g.Nome AS Genere 
          FROM filmDB_Films f 
          JOIN filmDB_Generi g ON f.ID_Genere = g.ID";

if ($searchQuery) {
    $query .= " WHERE f.Titolo LIKE :searchQuery";
    $statement = $conn->prepare($query);
    $statement->bindValue(':searchQuery', '%' . $searchQuery . '%', PDO::PARAM_STR);
} else {
    $statement = $conn->prepare($query);
}

$statement->execute();
$data = $statement->fetchAll();

if ($searchQuery) {
    $genresQuery = "SELECT DISTINCT g.ID, g.Nome 
                    FROM filmDB_Generi g
                    JOIN filmDB_Films f ON g.ID = f.ID_Genere
                    WHERE f.Titolo LIKE :searchQuery";
    $genresStatement = $conn->prepare($genresQuery);
    $genresStatement->bindValue(':searchQuery', '%' . $searchQuery . '%', PDO::PARAM_STR);
} else {
    $genresQuery = "SELECT DISTINCT g.ID, g.Nome 
                    FROM filmDB_Generi g
                    JOIN filmDB_Films f ON g.ID = f.ID_Genere";
    $genresStatement = $conn->prepare($genresQuery);
}
$genresStatement->execute();
$genres = $genresStatement->fetchAll();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Film Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<style>

#suggestions {
    position: absolute;
    z-index: 1000;
    max-height: 200px;
    overflow-y: auto;
    display: none;
    width: 100%;
    margin-top:36px;
}

.card{
	box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
	transition: transform 0.3s;
}

.card:hover{
	transform: scale(1.1);
}

</style>
</head>

<body>
<nav class="navbar navbar-expand-lg" style="background-color: rgb(17, 17, 17);">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="img/filmDB_Logo.png" alt="Logo" style="height: 80px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <form class="d-flex ms-auto me-3 position-relative" method="GET" action="index.php" autocomplete="off">
                <input class="form-control me-2" id="search-bar" type="search" placeholder="Search by title" name="search"
                       value="<?= htmlspecialchars($searchQuery) ?>" style="max-width: 300px;">
                <div id="suggestions" class="list-group"></div>
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                <a href="admin.php" class="btn btn-warning ms-3">Admin Panel</a>
            <?php endif; ?>
            <div class="dropdown">
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
    <h1 class="text-center">Film Collection</h1>
    <?php if (count($data) === 0): ?>
        <p class="text-center">No films found. Try searching for a different title.</p>
    <?php else: ?>
        <?php foreach ($genres as $group): ?>
            <div class="mb-5">
                <h3 class="mb-4 text-warning border-bottom pb-2"><?= htmlspecialchars($group['Nome']) ?></h3>
                <div class="row row-cols-2 row-cols-sm-2 row-cols-md-4 row-cols-lg-6 g-3">
                    <?php foreach ($data as $row): ?>
                        <?php if ($row['Genere'] === $group['Nome']): ?>
                            <div class="col">
                                <div class="card h-100">
                                    <img src="<?= $row['Copertina'] ? 'img/' . $row['Copertina'] : 'img/default.jpg' ?>"
                                         class="card-img-top" alt="<?= htmlspecialchars($row['Titolo']) ?>"
                                         style="aspect-ratio: 9/16; object-fit: cover;">
                                    <div class="card-body text-center">
                                        <h5 class="card-title" style="font-size: 0.9rem;">
                                            <?= htmlspecialchars($row['Titolo']) ?>
                                        </h5>
                                        <button onclick="window.location.href='details.php?id=<?= $row['ID'] ?>'" class="btn btn-warning btn-sm">Details</button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchBar = document.getElementById('search-bar');
    const suggestions = document.getElementById('suggestions');

    searchBar.addEventListener('input', function() {
        const query = searchBar.value.trim();
        if (query.length === 0) {
            suggestions.style.display = 'none';
            return;
        }

        fetch(`search.php?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                suggestions.innerHTML = '';
                if (data.length > 0) {
                    suggestions.style.display = 'block';
                    data.forEach(item => {
                        const suggestion = document.createElement('a');
                        suggestion.classList.add('list-group-item', 'list-group-item-action');
                        suggestion.textContent = item.Titolo;
                        suggestion.href = `index.php?search=${item.Titolo}`;
                        suggestions.appendChild(suggestion);
                    });
                } else {
                    suggestions.style.display = 'none';
                }
            })
            .catch(error => console.error('Error fetching suggestions:', error));
    });

    document.addEventListener('click', function(event) {
        if (!suggestions.contains(event.target) && event.target !== searchBar) {
            suggestions.style.display = 'none';
        }
    });
});
</script>
</body>

</html>
