<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$users = json_decode(file_get_contents('users.json'), true);
$user = $_SESSION['user'];

if (!isset($users[$user]) || $users[$user]['role'] !== 'ADMIN') {
  echo "Dostęp tylko dla administratorów.";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $t = trim($_POST['title']);
  $type = $_POST['type'];
  $category = trim($_POST['category']);
  $data = trim($_POST['url']);
  $thumb = trim($_POST['thumb']);

  $line = "$t | $type | $category | $data | $thumb";

  $file = file('f.txt', FILE_IGNORE_NEW_LINES);
  $inserted = false;

  if ($type === 'film') {
    $start = $end = -1;
    foreach ($file as $i => $fline) {
      if (stripos($fline, '--- MOVIES ---') !== false || stripos($fline, '--- FILMY ---') !== false) $start = $i;
      if ($start !== -1 && $i > $start && strpos($fline, '---') === 0) {$end = $i; break;}
    }
    if ($start !== -1) {
      $pos = ($end !== -1) ? $end : count($file);
      array_splice($file, $pos, 0, $line);
      $inserted = true;
    }
  } elseif ($type === 'serial') {
    $start = $end = -1;
    foreach ($file as $i => $fline) {
      if (stripos($fline, '--- SERIES ---') !== false || stripos($fline, '--- SERIALE ---') !== false) $start = $i;
      if ($start !== -1 && $i > $start && strpos($fline, '---') === 0) {$end = $i; break;}
    }
    if ($start !== -1) {
      $pos = ($end !== -1) ? $end : count($file);
      array_splice($file, $pos, 0, $line);
      $inserted = true;
    }
  } elseif ($type === 'top') {
    $start = $end = -1;
    foreach ($file as $i => $fline) {
      if (stripos($fline, '--- TOP ---') !== false) $start = $i;
      if ($start !== -1 && $i > $start && strpos($fline, '---') === 0) {$end = $i; break;}
    }
    if ($start !== -1) {
      $pos = ($end !== -1) ? $end : count($file);
      array_splice($file, $pos, 0, $line);
      $inserted = true;
    }
  }

  if (!$inserted) $file[] = $line;

  file_put_contents('f.txt', implode("\n", $file) . "\n");
  header("Location: admin.php?added=1");
  exit;
}

$lines = file('f.txt', FILE_IGNORE_NEW_LINES);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <title>Panel Admina</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    /* Twój styl */
    body {
      margin: 0;
      background: radial-gradient(circle at top left, #0f2027, #203a43, #2c5364);
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 40px 20px;
    }
    .glass {
      background: rgba(255, 255, 255, 0.08);
      border-radius: 16px;
      padding: 30px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.15);
      width: 100%;
      max-width: 800px;
      box-shadow: 0 0 30px rgba(0,0,0,0.4);
    }
    h1 { text-align: center; margin-bottom: 30px; font-weight: 500; }
    form { display: grid; gap: 15px; }
    input, select {
      padding: 10px; font-size: 16px; border-radius: 8px; border: none; outline: none;
      background: rgba(255,255,255,0.1); color: #fff;
    }
    input::placeholder { color: rgba(255,255,255,0.7); }
    button {
      padding: 12px; background: linear-gradient(to right, #00c6ff, #0072ff);
      border: none; border-radius: 8px; color: #fff; font-size: 16px; cursor: pointer;
      transition: background 0.3s;
    }
    button:hover { background: linear-gradient(to right, #0072ff, #00c6ff); }
    .list { margin-top: 40px; }
    .item, .header {
      display: flex; align-items: center; justify-content: space-between;
      padding: 8px; margin-bottom: 4px;
      background: rgba(255,255,255,0.05); border-radius: 6px;
    }
    .header { font-weight: bold; background: rgba(255,255,255,0.1); cursor: default; }
    .drag { cursor: grab; margin-right: 10px; user-select:none; }
    .title { flex: 1; }
    .editBtn, .deleteBtn {
      margin-left: 10px; background: none; border: none; color: #fff;
      font-size: 16px; cursor: pointer;
    }
    .editBtn:hover, .deleteBtn:hover { color: #0ef; }
    .toast {
      position: fixed; top: 20px; right: 20px; background: #12A5A6;
      color: #fff; padding: 12px 20px; border-radius: 6px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3); transition: opacity 0.3s ease; z-index: 9999;
    }
    .toast.hidden { opacity: 0; pointer-events: none; }
    footer { margin-top: 50px; text-align: center; font-size: 14px; color: #bbb; }
  </style>
</head>
<body>
  <div class="glass">
    <h1>🎬 Panel administratora</h1>
    <form method="post">
      <input type="text" name="title" placeholder="Tytuł" required />
      <select name="type">
        <option value="film">Film</option>
        <option value="serial">Serial</option>
        <option value="top">Top</option>
      </select>
      <input type="text" name="category" placeholder="Kategoria (np. Akcja, Komedia)" required />
      <input type="text" name="url" placeholder="URL do źródła (lub .txt dla seriali)" required />
      <input type="text" name="thumb" placeholder="Miniaturka (URL)" required />
      <button type="submit">➕ Dodaj</button>
    </form>

    <div class="list">
      <h2>📂 Istniejące pozycje</h2>
      <button id="saveOrderBtn">💾 Zapisz kolejność</button>
      <ul id="itemList">
  <?php 
    $currentSection = '';
    $itemIndex = 0; // indeks tylko dla pozycji .item
    foreach ($lines as $i => $line): 
      $line = trim($line);
      if ($line === '') continue;
      if (strpos($line, '---') === 0) {
        $currentSection = htmlspecialchars($line);
        echo "<li class='header'><span class='drag'>⣿</span>$currentSection</li>";
        continue;
      }
      $p = explode('|', $line);
      if (count($p) < 5) continue;
      $title = trim($p[0]);
      $cat = trim($p[2]);
  ?>
    <li class="item" data-index="<?= $itemIndex++ ?>">
      <span class="drag">⣿</span>
      <span class="title"><?= htmlspecialchars($title) ?> (<?= htmlspecialchars($cat) ?>)</span>
      <button class="editBtn">✏️</button>
      <button class="deleteBtn">🗑</button>
    </li>
  <?php endforeach; ?>
</ul>
       
      </ul>
    </div>
  </div>

  <footer>by eddit</footer>
  <div id="toast" class="toast hidden"></div>

  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
  <script>
    function showToast(msg, color = '#12A5A6') {
      const t = document.getElementById('toast');
      t.textContent = msg;
      t.style.background = color;
      t.classList.remove('hidden');
      setTimeout(() => t.classList.add('hidden'), 3000);
    }
    function ajax(data, cb) {
      fetch('ajax.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
      }).then(r => r.json())
        .then(res => {
          showToast(res.message, res.status === 'ok' ? '#12A5A6' : '#f55');
          if (cb) cb(res);
        });
    }
    document.querySelectorAll('.deleteBtn').forEach(btn => {
      btn.onclick = e => {
        if (!confirm('Usunąć?')) return;
        const li = e.target.closest('li'), idx = li.dataset.index;
        ajax({action: 'delete', index: parseInt(idx)}, r => {
          if (r.status === 'ok') li.remove();
        });
        e.stopPropagation();
      };
    });
    document.querySelectorAll('.editBtn').forEach(btn => {
      btn.onclick = e => {
        const li = e.target.closest('li'), idx = li.dataset.index;
        const oldTitle = li.querySelector('.title').textContent.split(' (')[0];
        const oldCat = li.querySelector('.title').textContent.split(' (')[1].slice(0, -1);
        const t = prompt('Tytuł?', oldTitle);
        if (!t) return;
        const c = prompt('Kategoria?', oldCat);
        if (!c) return;
        ajax({action: 'edit', index: parseInt(idx), title: t, category: c}, r => {
          if (r.status === 'ok') {
            li.querySelector('.title').textContent = t + ' (' + c + ')';
          }
        });
        e.stopPropagation();
      };
    });

    const sortable = Sortable.create(document.getElementById('itemList'), {
      handle: '.drag',
      filter: '.header',
      preventOnFilter: false
    });

    document.getElementById('saveOrderBtn').onclick = () => {
      const order = [...document.querySelectorAll('#itemList li.item')]
        .map(li => parseInt(li.dataset.index));
      ajax({action: 'sort', order}, r => {
        if (r.status === 'ok') showToast('Kolejność zapisana');
      });
    };

    <?php if (isset($_GET['added'])): ?>
      showToast('Dodano nową pozycję');
    <?php endif; ?>
  </script>
</body>
</html>
