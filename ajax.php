<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'ADMIN') {
  http_response_code(403);
  echo json_encode(['status' => 'error', 'message' => 'Brak dostępu']);
  exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['action'])) {
  echo json_encode(['status' => 'error', 'message' => 'Nieznana akcja']);
  exit;
}

$file = 'f.txt';
$lines = file($file, FILE_IGNORE_NEW_LINES);
$action = $input['action'];

switch ($action) {
  case 'edit':
    $index = intval($input['index']);
    $title = trim($input['title']);
    $category = trim($input['category']);
    $data = trim($input['data']);
    $thumb = trim($input['thumb']);
    if (isset($lines[$index])) {
      $parts = explode('|', $lines[$index]);
      if (count($parts) >= 5) {
        $type = trim($parts[1]);
        $lines[$index] = "$title | $type | $category | $data | $thumb";
        file_put_contents($file, implode("\n", $lines) . "\n");
        echo json_encode(['status' => 'ok', 'message' => 'Zapisano zmiany.']);
      } else {
        echo json_encode(['status' => 'error', 'message' => 'Niepoprawna linia.']);
      }
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Nie znaleziono wpisu.']);
    }
    break;

  case 'sort':
    $order = $input['order'] ?? [];
    if (!is_array($order)) $order = explode(',', $order);

    // Zbierz oryginalne linie danych bez separatorów
    $dataLines = [];
    foreach ($lines as $i => $line) {
      if (trim($line) !== '' && strpos($line, '---') !== 0) {
        $dataLines[$i] = $line;
      }
    }

    // Buduj nowy plik z zachowaniem separatorów na ich miejscach
    $newLines = [];
    $dataIterator = 0;
    foreach ($lines as $i => $line) {
      if (strpos($line, '---') === 0) {
        // Separator – przepisz bez zmian
        $newLines[] = $line;
      } else {
        // Element danych – wstaw w kolejności podanej przez JS
        $idx = intval($order[$dataIterator] ?? -1);
        if (isset($dataLines[$idx])) {
          $newLines[] = $dataLines[$idx];
        }
        $dataIterator++;
      }
    }

    file_put_contents($file, implode("\n", $newLines) . "\n");
    echo json_encode(['status' => 'ok', 'message' => 'Zapisano kolejność.']);
    break;

  case 'delete':
    $index = intval($input['index']);
    if (isset($lines[$index])) unset($lines[$index]);
    file_put_contents($file, implode("\n", $lines) . "\n");
    echo json_encode(['status' => 'ok', 'message' => 'Usunięto wpis.']);
    break;

  case 'notify':
    echo json_encode(['status' => 'ok', 'message' => 'Powiadomienie wysłane!']);
    break;

  default:
    echo json_encode(['status' => 'error', 'message' => 'Nieznana akcja']);
    break;
}
