# EFILLA - Simple M3U8 Streaming Platform

## Overview
EFILLA is a lightweight, PHP-based web application for managing and streaming media using M3U8 playlists. It’s designed as a minimal streaming service backend with user authentication, admin controls, and a clean UI for browsing and playing movies or series. Think of it as a simple, customizable foundation for a private streaming platform.

## Features
- **User Authentication**: Login and registration system with role-based access (ADMIN/USER).
- **Media Management**: Admins can add, edit, delete, and reorder movies/series stored in a flat file (`f.txt`).
- **M3U8 Streaming**: Supports streaming M3U8 playlists for movies and series using Video.js.
- **Responsive UI**: Glassmorphism-inspired design with a grid-based library and a player with custom controls.
- **Drag-and-Drop Sorting**: Admins can reorder media entries via a drag-and-drop interface.
- **Series Support**: Handles multi-episode series with episode lists loaded from text files.

## Screenshots
Below are screenshots showcasing the EFILLA platform's interface:

- **Login Page**: Clean and modern login interface for user authentication.
  ![Login Page](https://i.imgur.com/09ljdmb.png)

- **Library View**: Grid-based library displaying movies and series with hover effects.
  ![Library View](https://i.imgur.com/LTutIhp.png)

- **Player Page**: Video player with custom controls and series episode panel (when applicable).
  ![Player Page](https://i.imgur.com/ufUFiWI.jpeg)

- **Admin Panel**: Interface for admins to manage media entries with drag-and-drop sorting.
  ![Admin Panel](https://i.imgur.com/owBaMW3.png)

**Live Preview**: Check out the live demo at [https://eddit.me//](https://eddit.me/streaming/).

## Tech Stack
- **Backend**: PHP (session-based auth, file-based storage).
- **Frontend**: HTML, CSS (glassmorphism), JavaScript with Video.js for streaming and SortableJS for drag-and-drop.
- **Storage**: Flat file (`f.txt`) for media data, JSON (`users.json`) for user data.
- **External Libraries**:
  - Video.js for M3U8 playback.
  - SortableJS for drag-and-drop functionality.

## How It Works
1. **User Flow**:
   - Users register (`register.php`) or log in (`login.php`) to access the library (`index.php`).
   - The library displays media entries (movies, series, or "top" items) from `f.txt`, grouped by sections (e.g., `--- MOVIES ---`).
   - Clicking a movie redirects to `player.php` with the M3U8 URL. For series, it loads a text file (e.g., `seriale/pati.txt`) with episode data.
   - The player uses Video.js with custom controls for play/pause, seeking, volume, and fullscreen.

2. **Admin Flow**:
   - Admins (role: `ADMIN` in `users.json`) access `admin.php` to manage media.
   - Add new entries (title, type, category, URL, thumbnail) via a form, stored in `f.txt`.
   - Edit or delete existing entries via AJAX (`ajax.php`).
   - Reorder entries with drag-and-drop, saved via AJAX.

3. **Data Structure**:
   - `f.txt`: Media entries in the format `title | type | category | url | thumbnail`, grouped by sections (e.g., `--- MOVIES ---`).
   - `users.json`: Stores user data (`username`, `pass` (base64-encoded), `email`, `role`).
   - Series episodes: Stored in text files (e.g., `seriale/pati.txt`) with format `episode_name | url`.

## Setup
1. **Requirements**:
   - PHP 7.4+ with `session` and `json` extensions.
   - Web server (e.g., Apache, Nginx).
   - Write permissions for `f.txt` and `users.json`.

2. **Installation**:
   ```bash
   git clone <your-repo-url>
   cd efilla
   ```
   - Copy files to your web server’s root (e.g., `/var/www/html`).
   - Ensure `f.txt` and `users.json` are writable (`chmod 664` or equivalent).
   - Create a `seriale/` directory for series episode files if needed.

3. **Configuration**:
   - Edit `users.json` to add initial users (passwords are base64-encoded, e.g., `MTIz` = `123`).
   - Update `f.txt` with your media entries. Example:
     ```
     --- MOVIES ---
     My Movie | film | Action | https://example.com/stream.m3u8 | https://example.com/thumb.jpg
     --- SERIES ---
     My Series | serial | Drama | seriale/myseries.txt | https://example.com/series_thumb.jpg
     ```
   - For series, create episode files in `seriale/` (e.g., `myseries.txt`):
     ```
     Episode 1 | https://example.com/ep1.m3u8
     Episode 2 | https://example.com/ep2.m3u8
     ```

4. **Access**:
   - Open `http://your-server/register.php` to create a user.
   - Log in via `login.php` to access the library (`index.php`).
   - Admins can manage content via `admin.php`.

## Usage
- **Users**: Browse the library, click tiles to watch movies or series. Series show an episode list in the player.
- **Admins**: Use the admin panel to add/edit/delete media, reorder entries, and manage the library structure.
- **Streaming**: Ensure M3U8 URLs are valid and accessible. The player supports HLS streaming via Video.js.

## Limitations
- **Storage**: Flat file (`f.txt`) and JSON (`users.json`) limit scalability. Consider a database for larger setups.
- **Security**: Basic auth with base64-encoded passwords. For production, use proper hashing (e.g., `password_hash`) and HTTPS.
- **No Search**: Library lacks search/filter functionality.
- **Single Format**: Only supports M3U8 streaming.

## Contributing
Feel free to fork, tweak, or extend! Submit issues or PRs for bugs, features, or improvements. Ideas:
- Add a database backend (e.g., MySQL).
- Implement search/filter in the library.
- Enhance security with proper password hashing.
- Support additional media formats.

## License
MIT License. Use it, modify it, share it.
