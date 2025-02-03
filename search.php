<?php
require "db.php";
require "accessoDB.php";

if (!isset($_GET['query']) || empty($_GET['query'])) {
    echo json_encode([]);
    exit;
}

$searchQuery = $_GET['query'] . '%';
$stmt = $conn->prepare("SELECT ID, Titolo FROM filmDB_Films WHERE Titolo LIKE :query LIMIT 10");
$stmt->bindParam(':query', $searchQuery, PDO::PARAM_STR);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);
