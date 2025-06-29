<?php
session_start();
if (!isset($_SESSION['user'])) header('Location: login.php');
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8" />
<title>EFILLA | Biblioteka</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
  @keyframes bgAnimation {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }
  @keyframes reelSpin {
    from { transform: rotate(0deg);}
    to { transform: rotate(360deg);}
  }
  @keyframes playPulse {
    0%, 100% { filter: drop-shadow(0 0 6px #0ef); transform: scale(1); }
    50% { filter: drop-shadow(0 0 15px #0ef); transform: scale(1.1); }
  }
  body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(270deg, #0a1416, #10272a, #134143, #0a1416);
    background-size: 800% 800%;
    animation: bgAnimation 30s ease infinite;
    color: #ccc;
    min-height: 100vh;
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
    user-select: none;
  }
  .navbar .brand {
    font-weight: 700;
    color: #12A5A6;
    display: flex;
    align-items: center;
    gap: 8px;
    user-select: none;
  }
  .navbar .brand svg {
    width: 28px;
    height: 28px;
    fill: #12A5A6;
    filter: drop-shadow(0 0 1px #0ef);
    animation: reelSpin 5s linear infinite;
  }
  .navbar .user {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    color: #12A5A6;
  }
  .navbar .user svg {
    width: 22px;
    height: 22px;
    fill: #12A5A6;
    filter: drop-shadow(0 0 2px #0ef);
  }
  .navbar .user span {
    user-select: none;
  }
  .navbar .user a {
    color: #12A5A6;
    text-decoration: none;
    margin-left: 16px;
    font-weight: 600;
    transition: color 0.2s ease-in-out;
  }
  .navbar .user a:hover {
    color: #0ef;
  }
  .library {
    flex: 1;
    overflow-y: auto;
    padding: 18px 24px;
    scrollbar-width: thin;
    scrollbar-color: #12A5A6 transparent;
  }
  .library::-webkit-scrollbar {
    width: 8px;
  }
  .library::-webkit-scrollbar-thumb {
    background-color: #12A5A6;
    border-radius: 4px;
  }
  h2 {
    position: relative;
    color: #a0dede;
    margin-top: 40px;
    margin-bottom: 20px;
    font-weight: 700;
    font-size: 24px;
    user-select: none;
    padding-left: 28px;
    text-shadow: 0 0 5px #12A5A6;
  }
  h2::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -6px;
    width: 20px;
    height: 4px;
    background: #12A5A6;
    box-shadow: 0 0 8px #0ef;
    border-radius: 2px;
  }
  .grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 14px;
  }
.tile {
  position: relative; /* kluczowe */
  background: rgba(18, 165, 166, 0.07);
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  box-shadow: 0 0 6px rgba(18, 165, 166, 0.3);
  transition: background 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
  user-select: none;
  display: flex;
  flex-direction: column;
  align-items: center;
}
  .tile:hover {
    background: rgba(18, 165, 166, 0.25);
    box-shadow: 0 0 14px #0ef;
    transform: scale(1.05);
    z-index: 1;
  }
.thumb {
  width: 100%;
  height: 220px;
  object-fit: cover;
  border-bottom: 1px solid rgba(18, 165, 166, 0.3);
  background: #0b3b3d;
  flex-shrink: 0;
  transition: transform 0.3s ease, filter 0.3s ease;
  display: block; /* dodane */
}
  .tile:hover .thumb {
    transform: scale(1.08);
    filter: brightness(0.7);
  }
.play-icon {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 48px;
  height: 48px;
  fill: #12A5A6;
  filter: drop-shadow(0 0 4px #0ef);
  opacity: 0;
  pointer-events: none;
  animation: playPulse 2s infinite;
  transition: opacity 0.3s ease, transform 0.3s ease;
  transform-origin: center center;
  transform: translate(-50%, -50%) scale(1);
  z-index: 2;
}
.tile:hover .play-icon {
  opacity: 1;
  transform: translate(-50%, -50%) scale(1.1);
}
  .title {
    padding: 8px 12px;
    color: #cdf6f7;
    font-weight: 600;
    font-size: 16px;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 100%;
  }
  footer.footer {
    text-align: center;
    padding: 14px;
    font-size: 13px;
    color: #0b3b3d;
    border-top: 1px solid rgba(18, 165, 166, 0.3);
    backdrop-filter: blur(10px);
    background: rgba(18, 165, 166, 0.07);
    user-select: none;
    flex-shrink: 0;
  }
</style>
</head>
<body>
<nav class="navbar">
  <div class="brand" title="EFILLA">
    <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" >
      <circle cx="32" cy="32" r="30" stroke="#12A5A6" stroke-width="3" fill="none"/>
      <circle cx="20" cy="20" r="5" fill="#12A5A6"/>
      <circle cx="44" cy="20" r="5" fill="#12A5A6"/>
      <circle cx="20" cy="44" r="5" fill="#12A5A6"/>
      <circle cx="44" cy="44" r="5" fill="#12A5A6"/>
      <circle cx="32" cy="32" r="8" stroke="#12A5A6" stroke-width="3" fill="none"/>
    </svg>
    EFILLA
  </div>
  <div class="user" title="Konto użytkownika">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#12A5A6" aria-hidden="true" focusable="false" >
      <path d="M12 12c2.7 0 4.9-2.2 4.9-4.9S14.7 2.2 12 2.2 7.1 4.4 7.1 7.1 9.3 12 12 12zm0 2.2c-3.2 0-9.6 1.6-9.6 4.9v2.2h19.2v-2.2c0-3.3-6.4-4.9-9.6-4.9z"/>
    </svg>
    <span><?=htmlspecialchars($_SESSION['user'])?></span>
    <a href="logout.php">Wyloguj</a>
    <?php if ($_SESSION['role'] === 'ADMIN'): ?>
      | <a href="admin.php">Panel Admina</a>
    <?php endif; ?>
  </div>
</nav>

<div class="library" id="content"></div>

<footer class="footer">by eddit © <?=date('Y')?></footer>

<script>
fetch('f.txt').then(r => r.text()).then(text => {
  const lines = text.split("\n").map(l => l.trim()).filter(Boolean);
  const container = document.getElementById('content');
  let section;
  lines.forEach(line => {
    if (line.startsWith("---")) {
      const h = document.createElement('h2');
      h.textContent = line.replace(/[-|]/g, '').trim();
      container.appendChild(h);
      section = document.createElement('div');
      section.className = 'grid';
      container.appendChild(section);
    } else {
      const parts = line.split("|").map(s => s.trim());
      if (parts.length < 4) return;
      let t, type, data, thumb;
      if (parts.length === 4) {
        [t, type, data, thumb] = parts;
      } else {
        [t, type, , data, thumb] = parts;
      }
      if (!t || !type || !data) return;
      const tile = document.createElement('div');
      tile.className = 'tile';
      tile.onclick = () => {
        location.href = 'player.php?' + (type === 'film' ? 'src=' + encodeURIComponent(data) : 'serial=' + encodeURIComponent(data)) + '&title=' + encodeURIComponent(t);
      };
      const img = document.createElement('img');
      img.src = thumb || 'assets/images/default.jpg';
      img.className = 'thumb';
      const playIcon = document.createElementNS("http://www.w3.org/2000/svg", "svg");
      playIcon.setAttribute("viewBox", "0 0 24 24");
      playIcon.setAttribute("class", "play-icon");
      playIcon.innerHTML = `<path fill="#12A5A6" d="M8 5v14l11-7z"/>`;
      const name = document.createElement('div');
      name.className = 'title';
      name.textContent = t;
      tile.appendChild(img);
      tile.appendChild(playIcon);
      tile.appendChild(name);
      section.appendChild(tile);
    }
  });
});
</script>
</body>
</html>
