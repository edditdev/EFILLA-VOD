<?php session_start();
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = trim($_POST['user'] ?? '');
  $p = trim($_POST['pass'] ?? '');
  $users = json_decode(file_get_contents('users.json') ?: '{}', true);
  if (!isset($users[$u])) $err = 'Nieprawidłowy login.';
  else if (base64_decode($users[$u]['pass']) !== $p) $err = 'Nieprawidłowe hasło.';
  else {
    $_SESSION['user']=$u;
    $_SESSION['role']=$users[$u]['role'];
    header('Location: index.php');
    exit;
  }
}
?><!DOCTYPE html>
<html lang="pl"><head><meta charset="UTF-8"><title>Logowanie</title>
<link rel="stylesheet" href="assets/css/style.css"></head><body class="login-page">
<div class="glass-panel">
  <h2>Logowanie</h2>
  <?php if($_GET['registered']):?><div class="success">Zarejestrowano. Zaloguj się.</div><?php endif;?>
  <?php if($err):?><div class="error"><?=htmlspecialchars($err)?></div><?php endif;?>
  <form method="post">
    <input name="user" placeholder="Login">
    <input name="pass" type="password" placeholder="Hasło">
    <button type="submit">Zaloguj</button>
  </form>
  <p>Nie masz konta? <a href="register.php">Zarejestruj się</a></p>
</div></body></html>
