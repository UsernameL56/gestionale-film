<?php
require "db.php";
require "accessoDB.php";

session_start();

// Imposta la durata della sessione solo durante la vita del browser
ini_set('session.cookie_lifetime', 0);
ini_set('session.gc_maxlifetime', 0);

// Verifica se l'utente è loggato (per altre pagine)
if (isset($_GET['check_session']) && !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Funzione per interrompere la sessione
function logout_user() {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

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
    header("Location: login.php?message=login_error");
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
      header("Location: register.php?message=email_exists");
      exit;
  } else {
      $statement = $conn->prepare("INSERT INTO filmDB_Users (Nome, Cognome, Email, Password, Admin) VALUES (:nome, :cognome, :email, :password, 0)");
      $statement->bindParam(':nome', $nome);
      $statement->bindParam(':cognome', $cognome);
      $statement->bindParam(':email', $email);
      $statement->bindParam(':password', $password);
      $statement->execute();

      header("Location: login.php?message=registered");
      exit;
  }
}
?>
