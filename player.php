<?php
session_start();
if (!isset($_SESSION['user'])) header('Location: login.php');

$serial = $_GET['serial'] ?? null;
$src = $_GET['src'] ?? null;
$title = htmlspecialchars($_GET['title'] ?? 'Odtwarzanie');
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <title>EFILLA | <?= $title ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://vjs.zencdn.net/7.20.3/video-js.css" rel="stylesheet" />
  <script src="https://vjs.zencdn.net/7.20.3/video.min.js"></script>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: radial-gradient(circle at top left, #0a1416, #10272a, #134143);
      color: #ccc;
      height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      backdrop-filter: blur(10px);
      background: rgba(18, 165, 166, 0.15);
      padding: 12px 24px;
      border-bottom: 1px solid rgba(18, 165, 166, 0.3);
      color: #12A5A6;
      font-weight: 600;
      font-size: 18px;
    }
    .navbar .brand {
      text-decoration: none;
      color: #12A5A6;
    }
    .navbar .links a {
      margin-left: 20px;
      color: #12A5A6;
      text-decoration: none;
      font-weight: 500;
      font-size: 14px;
      transition: color 0.2s ease-in-out;
    }
    .navbar .links a:hover {
      color: #0ef;
    }
    .content {
      flex: 1;
      display: flex;
      background: transparent;
      overflow: hidden;
    }
    .video-container {
      flex: 2;
      background: #000;
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .video-js {
      width: 100% !important;
      height: auto !important;
      max-height: 100vh;
      max-width: 100vw;
      aspect-ratio: 16 / 9;
      background: black;
      object-fit: contain;
    }
    .video-title-overlay {
      position: absolute;
      top: 16px;
      left: 16px;
      background: rgba(18, 165, 166, 0.25);
      backdrop-filter: blur(6px);
      padding: 8px 14px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 20px;
      color: #12A5A6;
      user-select: none;
      pointer-events: none;
      max-width: 70vw;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .custom-controls {
      position: absolute;
      bottom: 18px;
      left: 0;
      right: 0;
      margin: 0 24px;
      display: flex;
      gap: 14px;
      background: rgba(18, 165, 166, 0.15);
      backdrop-filter: blur(8px);
      padding: 10px 20px;
      border-radius: 20px;
      align-items: center;
      user-select: none;
    }
    .custom-controls button {
      background: transparent;
      border: none;
      color: #12A5A6;
      font-size: 20px;
      cursor: pointer;
      transition: color 0.15s ease;
    }
    .custom-controls button:hover {
      color: #0ef;
    }
    #progressBar {
      flex: 1;
      -webkit-appearance: none;
      width: auto;
      height: 6px;
      border-radius: 4px;
      background: rgba(18, 165, 166, 0.3);
      cursor: pointer;
    }
    #progressBar::-webkit-slider-thumb {
      -webkit-appearance: none;
      width: 14px;
      height: 14px;
      border-radius: 50%;
      background: #0ef;
      cursor: pointer;
      border: none;
      margin-top: -4px;
      box-shadow: 0 0 6px #0ef;
    }
    #progressBar:focus {
      outline: none;
      box-shadow: 0 0 6px #0ef;
    }
    #currentTime, #durationTime {
      color: #12A5A6;
      font-size: 14px;
      user-select: none;
      width: 50px;
      text-align: center;
      font-family: monospace;
    }
    .custom-controls input[type=range]:not(#progressBar) {
      -webkit-appearance: none;
      width: 80px;
      height: 6px;
      border-radius: 4px;
      background: rgba(18, 165, 166, 0.3);
      cursor: pointer;
    }
    .custom-controls input[type=range]:not(#progressBar)::-webkit-slider-thumb {
      -webkit-appearance: none;
      width: 14px;
      height: 14px;
      border-radius: 50%;
      background: #0ef;
      cursor: pointer;
      border: none;
      margin-top: -4px;
      box-shadow: 0 0 6px #0ef;
    }
    .custom-controls input[type=range]:not(#progressBar):focus {
      outline: none;
      box-shadow: 0 0 6px #0ef;
    }
    .episodes-panel {
      flex: 1;
      background: rgba(18, 165, 166, 0.1);
      backdrop-filter: blur(8px);
      overflow-y: auto;
      padding: 22px 18px;
      display: flex;
      flex-direction: column;
      scrollbar-width: thin;
      scrollbar-color: #12A5A6 transparent;
    }
    .episodes-panel::-webkit-scrollbar {
      width: 8px;
    }
    .episodes-panel::-webkit-scrollbar-thumb {
      background-color: #12A5A6;
      border-radius: 4px;
    }
    .season-label {
      font-weight: 700;
      color: #a0dede;
      margin-bottom: 14px;
      font-size: 16px;
    }
    .episode {
      background: rgba(18, 165, 166, 0.07);
      padding: 12px 14px;
      margin: 6px 0;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 600;
      color: #cdf6f7;
      user-select: none;
      transition: background 0.2s ease;
    }
    .episode:hover {
      background: rgba(18, 165, 166, 0.25);
    }
    .footer {
      text-align: center;
      padding: 14px;
      font-size: 13px;
      color: #0b3b3d;
      border-top: 1px solid rgba(18, 165, 166, 0.3);
      backdrop-filter: blur(10px);
      background: rgba(18, 165, 166, 0.07);
      user-select: none;
    }
    .fullscreen-exit-btn {
      position: fixed;
      top: 18px;
      right: 18px;
      background: rgba(18, 165, 166, 0.4);
      backdrop-filter: blur(8px);
      padding: 12px 16px;
      border-radius: 14px;
      display: none;
      cursor: pointer;
      color: #12A5A6;
      font-weight: 700;
      font-size: 18px;
      z-index: 999;
      user-select: none;
      transition: background-color 0.25s ease;
    }
    .fullscreen-exit-btn:hover {
      background: rgba(18, 165, 166, 0.65);
    }
  </style>
</head>
<body>
<nav class="navbar">
  <a href="index.php" class="brand">EFILLA</a>
  <div class="links">
    <a href="index.php">Biblioteka</a>
    <?php if ($_SESSION['role'] === 'ADMIN'): ?>
      <a href="admin.php">Admin</a>
    <?php endif; ?>
    <a href="logout.php">Wyloguj</a>
  </div>
</nav>

<div class="content">
  <div class="video-container">
    <video
      id="vjs"
      class="video-js vjs-default-skin"
      preload="auto"
      playsinline
      controls="false"
      webkit-playsinline
      x5-playsinline
    ></video>
    <div class="video-title-overlay"><?= $title ?></div>
    <div class="custom-controls">
      <button onclick="togglePlay()" aria-label="Play/Pause">⏯️</button>
      <button onclick="stopVideo()" aria-label="Stop">⏹️</button>
      <button onclick="skip(-10)" aria-label="Cofnij 10 sekund">⏪10s</button>
      <button onclick="skip(10)" aria-label="Przewiń 10 sekund">⏩10s</button>

      <div id="currentTime">00:00</div>
      <input
        id="progressBar"
        type="range"
        min="0"
        max="100"
        step="0.1"
        value="0"
        oninput="seek(this.value)"
        aria-label="Progress bar"
      />
      <div id="durationTime">00:00</div>

      <input
        type="range"
        min="0"
        max="1"
        step="0.05"
        value="1"
        onchange="setVolume(this.value)"
        aria-label="Głośność"
      />
      <button onclick="toggleFullscreen()" aria-label="Fullscreen">⛶</button>
    </div>
  </div>
  <?php if ($serial): ?>
  <div class="episodes-panel" id="episodeList"></div>
  <?php endif; ?>
</div>

<div class="fullscreen-exit-btn" id="exitFullscreenBtn" onclick="exitFullscreen()" title="Wyjdź z pełnego ekranu">⤢</div>

<footer class="footer">© <?= date('Y') ?> EFILLA by eddit</footer>

<script>
  const player = videojs('vjs', { controls: false, fluid: true });
  const progressBar = document.getElementById('progressBar');
  const exitBtn = document.getElementById('exitFullscreenBtn');
  const currentTimeElem = document.getElementById('currentTime');
  const durationTimeElem = document.getElementById('durationTime');

  function play(u) {
    player.src({ src: u, type: 'application/x-mpegURL' });
    player.play();
  }
  function togglePlay() {
    player.paused() ? player.play() : player.pause();
  }
  function stopVideo() {
    player.pause();
    player.currentTime(0);
  }
  function skip(s) {
    player.currentTime(player.currentTime() + s);
  }
  function setVolume(v) {
    player.volume(v);
  }
  function toggleFullscreen() {
    if (player.isFullscreen()) {
      player.exitFullscreen();
    } else {
      player.requestFullscreen();
    }
  }
  function exitFullscreen() {
    player.exitFullscreen();
  }

  function formatTime(seconds) {
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    const s = Math.floor(seconds % 60);
    return (h > 0 ? h.toString().padStart(2, '0') + ':' : '') +
           m.toString().padStart(2, '0') + ':' +
           s.toString().padStart(2, '0');
  }

  player.on('timeupdate', () => {
    if (!player.duration()) return;
    const current = player.currentTime();
    const duration = player.duration();

    progressBar.value = (current / duration) * 100;
    currentTimeElem.textContent = formatTime(current);
    durationTimeElem.textContent = formatTime(duration);
  });

  function seek(val) {
    if (!player.duration()) return;
    player.currentTime(player.duration() * (val / 100));
  }

  document.addEventListener('mousemove', () => {
    if (player.isFullscreen()) {
      exitBtn.style.display = 'block';
      clearTimeout(exitBtn._hideTimer);
      exitBtn._hideTimer = setTimeout(() => {
        exitBtn.style.display = 'none';
      }, 2500);
    }
  });

  <?php if ($src): ?>play("<?= addslashes($src) ?>");<?php endif; ?>
  <?php if ($serial): ?>
  fetch("<?= addslashes($serial) ?>")
    .then(r => r.text())
    .then(txt => {
      const sb = document.getElementById('episodeList');
      txt.split("\n")
        .map(l => l.trim())
        .filter(Boolean)
        .forEach(line => {
          if (line.startsWith("---")) {
            const d = document.createElement('div');
            d.className = 'season-label';
            d.textContent = line.replace(/[-|]/g, '').trim();
            sb.appendChild(d);
          } else {
            const [t, u] = line.split("|").map(s => s.trim());
            const e = document.createElement('div');
            e.className = 'episode';
            e.textContent = t;
            e.onclick = () => play(u);
            sb.appendChild(e);
          }
        });
    });
  <?php endif; ?>
</script>
</body>
</html>
