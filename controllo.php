<?php
require "db.php";
require "accessoDB.php";

session_start();



// Funzione per il login
if (isset($_POST['login'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];

  var_dump($email);
  $statement = $conn->prepare("SELECT * FROM filmDB_Users WHERE Email = :email");
  $statement->bindParam(':email', $email);
  $statement->execute();

  $user = $statement->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($password, $user['Password'])) {
    $_SESSION['user_id'] = $user['ID'];
    $_SESSION['is_admin'] = $user['Admin'];
    header("Location: index.php");
    exit;
  } else {
    header("Location: login.php");
  }
} else if (isset($_POST['register'])) {
  $nome = $_POST['nome'];
  $cognome = $_POST['cognome'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

  // Controlla se l'email esiste già
  $check = $conn->prepare("SELECT ID FROM filmDB_Users WHERE Email = :email");
  $check->bindParam(':email', $email);
  $check->execute();

  if ($check->rowCount() > 0) {
    $register_error = "Email già registrata.";
  } else {
    $statement = $conn->prepare("INSERT INTO filmDB_Users (Nome, Cognome, Email, Password, Admin) VALUES (:nome, :cognome, :email, :password, 0)");
    $statement->bindParam(':nome', $nome);
    $statement->bindParam(':cognome', $cognome);
    $statement->bindParam(':email', $email);
    $statement->bindParam(':password', $password);
    $statement->execute();
    header("Location: login.php");
  }
}
?>