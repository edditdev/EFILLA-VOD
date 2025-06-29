<?php session_start();
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = trim($_POST['user'] ?? '');
  $p = trim($_POST['pass'] ?? '');
  $e = trim($_POST['email'] ?? '');
  if (!$u || !$p || !$e) $err = 'Wszystkie pola są wymagane.';
  else {
    $file = 'users.json';
    $users = json_decode(file_get_contents($file) ?: '{}', true);
    if (isset($users[$u])) $err = 'Login już istnieje.';
    else {
      $users[$u] = [
        'pass' => base64_encode($p),
        'email' => $e,
        'role' => 'USER'
      ];
      file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));
      header('Location: login.php?registered=1');
      exit;
    }
  }
}
?><!DOCTYPE html>
<html lang="pl"><head><meta charset="UTF-8"><title>Rejestracja</title>
<link rel="stylesheet" href="assets/css/style.css"></head><body class="login-page">
<div class="glass-panel">
  <h2>Zarejestruj się</h2>
  <?php if($err):?><div class="error"><?=htmlspecialchars($err)?></div><?php endif; ?>
  <form method="post">
    <input name="user" placeholder="Login">
    <input name="email" type="email" placeholder="Email">
    <input name="pass" type="password" placeholder="Hasło">
    <button type="submit">Zarejestruj</button>
  </form>
  <p>Masz konto? <a href="login.php">Zaloguj się</a></p>
</div></body></html>
