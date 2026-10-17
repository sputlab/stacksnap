# 🧱 StackSnap · Arcade Plus

[![CI](https://github.com/sputlab/stacksnap/actions/workflows/ci.yml/badge.svg)](.github/workflows/ci.yml)
[![Version](https://img.shields.io/badge/version-2.9.0-blue.svg)](index.html)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![PWA Ready](https://img.shields.io/badge/PWA-installable-orange.svg)](manifest.json)
[![Zero Dependencies](https://img.shields.io/badge/dependencies-0-brightgreen.svg)](#architecture--tech-stack)
[![PHP](https://img.shields.io/badge/backend-PHP%20PDO%20%7C%20MySQL-8892BF.svg)](api/)

> **StackSnap** is an addictive, fast-paced arcade browser game built with pure **HTML5 Canvas**, **Vanilla JavaScript**, and a procedural **Web Audio API** synthesizer. Stack moving blocks as high as possible, center each slice, trigger multiplier combos, collect power-up crystals, unlock achievements, and compete on the global leaderboard!

---

## ✨ Features

- **⚡ High-FPS Engine & Zero-Lag Architecture (120Hz/144Hz+ Ready)**:
  - **Zero Gaussian Blur Passes**: Replaced all heavy CPU multi-pass `ctx.shadowBlur` calls with GPU hardware-accelerated radiant halo strokes and offset shadow fills, saving thousands of offscreen blur passes every second.
  - **Precomputed Gradient Cache (Zero GC Pauses)**: Linear and radial gradients are generated once per color hue transition instead of every frame, completely eliminating garbage-collection micro-stutters.
  - **Batched Canvas Pipeline**: 85 stars rendered in a single GPU draw call; dual city skyline layers and 300+ skyscraper windows batched into unified fill paths.
  - **Compositor Queue Bypass & Hardware Layering**: Canvas initialized with `{ alpha: false, desynchronized: true }` and dedicated GPU compositing layer (`will-change: transform; transform: translateZ(0)`).
  - **Retina DPR Clamping**: Device pixel ratio clamped to `2.0` max, avoiding ~36% superfluous fill overhead on 3x screens while preserving ultra-sharp retina fidelity.
  - **Zero-Latency Unified Input**: Migrated from mouse/touch to unified `pointerdown` listener, eliminating synthetic 300ms mobile touch delays and double-taps.
  - **DOM Layout Thrashing Guard**: In-game score text updates only when the rounded integer score changes.
  - **Live HUD FPS Counter**: Real-time FPS badge with performance color coding (Green: 55+ FPS, Amber: 30-54 FPS, Red: <30 FPS), toggleable in the hamburger menu.

- **🎮 Addictive Arcade Stacking Mechanics**:
  - Horizontal block oscillation with sub-pixel precision slicing and **falling debris physics**.
  - **Perfect Alignment Bonus**: Exact placement (or precision-assisted) preserves full platform width, awards **+40 points**, and unleashes **radiant shockwave rings**.
  - **Streak Multiplier**: Score 3 Perfects in a row to unleash a **x2 multiplier** for your next 3 blocks.
  - **Golden Tower**: Every 5th block provides a **+50 bonus** with a golden aura.
  - **Perfect Zone**: A high-stakes target appears every 10 blocks granting **x3 points**.
  - **Safety Net**: Built-in rescue system automatically recovers a critically thin block once per game.
  - **Milestone Celebrations**: Every 25 blocks triggers celebratory 3D tumbling confetti ribbons, fanfare melodies, and camera shake.
  - **Pause & Resume**: Press `P`, `Esc`, or tap the screen to pause and resume at any time. Automatically pauses when opening menus or switching browser tabs.
  - **Framerate Normalization**: Delta-time (`dt`) physics ensuring consistent speed across 60Hz and 120Hz/144Hz (ProMotion) screens.

- **💎 3D Diamond Power-Up Crystals**:
  - Rotating 3D faceted gems with pulsing aura rings and orbiting starlet sparkles.
  - 🐢 **Blue (Slowmo)**: Slows sliding block speed by ~42% for 3 blocks.
  - 🎯 **Orange (Precision)**: Forgives alignment gaps up to 10% as Perfect hits.
  - 💥 **Red (Double Points)**: Doubles all points for 3 blocks.

- **🔊 100% Procedural Synthesizer Audio**:
  - Zero external `.mp3` or `.wav` files required!
  - Real-time procedural audio synthesis using the **Web Audio API** (sine, triangle, square, and sawtooth waves with dynamic envelope shaping).

- **🌌 Next-Gen Procedural Visuals & 3D Shaders**:
  - **3D Beveled Blocks**: Rounded chamfer corners, specular top gloss bar, rim lighting, drop shadows, and landing squash-and-stretch bounce.
  - **Overhang Debris Physics**: Cut pieces tumble down with rotational velocity and fade out.
  - **Cosmic Multi-Layer Background**: Altitude-based nebula glow, shooting meteors, parallax starfield with 4-point cross sparkles, and dual-layer city skyline with blinking beacons and illuminated windows.

- **📱 Full PWA (Progressive Web App) & Offline Ready**:
  - **Custom 3D App Icon Suite**: Complete icon assets across all device resolutions (`1024px`, `512px`, `192px`, maskable Android icons, `180px` Apple touch icon, `32px`/`16px` favicons, and vector SVG).
  - **Service Worker (`sw.js`)**: Offline playability, precaching of shell assets and fonts, and cache versioning.
  - **Install Prompt Integration**: Tap "📲 Install App" from the in-game menu or your browser bar to install StackSnap as a native app on iOS, Android, macOS, and Windows.
  - Pure Vanilla JavaScript and CSS glassmorphism adapting to desktop monitors, tablets, and mobile screens (e.g. iPhone 12 mini).
  - Multi-input controls: Mouse click, screen tap, or Keyboard (`Space` to drop, `P`/`Esc` to pause, `R` to restart).

- **🏆 Dual Leaderboards & Persistence**:
  - **Local Leaderboard**: Offline storage in `localStorage` for high scores.
  - **Global Leaderboard**: Shared backend powered by PHP PDO and MySQL with server-side dense ranking (tied scores share the same rank).
  - **Performance Optimized**: $O(N)$ dense rank calculation with HTTP caching (`Cache-Control: public, max-age=5, stale-while-revalidate=15`).
  - **Anti-Cheat Validation**: Mathematical score-to-blocks plausibility verification and IP cooldown rate limiting.
  - **Player Stats & 12 Achievements**: In-depth statistics tracking games played, total blocks, best streaks, play time, and unlocked badges.

---

## 🏗️ Architecture & Tech Stack

```
                          ┌──────────────────────────────────────┐
                          │         Browser Client               │
                          │                                      │
                          │  • HTML5 Canvas 2D (Dynamic DPR)     │
                          │  • Web Audio API Synthesizer         │
                          │  • PWA Manifest & Service Ready      │
                          │  • LocalStorage (Stats/Achievements) │
                          └──────────────────┬───────────────────┘
                                             │ HTTP REST (JSON)
                                             ▼
                          ┌──────────────────────────────────────┐
                          │         Backend (PHP PDO)            │
                          │                                      │
                          │  • api/get_leaderboard.php (O(N))    │
                          │  • api/submit_score.php (Anti-Cheat) │
                          │  • config.php (CORS & DB Connection) │
                          └──────────────────┬───────────────────┘
                                             │ SQL
                                             ▼
                          ┌──────────────────────────────────────┐
                          │         MySQL Database               │
                          │                                      │
                          │  • Table: stacksnap_scores           │
                          │  • Composite Index (score, created)  │
                          └──────────────────────────────────────┘
```

| Component | Technology | Description |
|---|---|---|
| **Frontend** | HTML5, CSS3, Vanilla JS | Zero-build, self-contained single-page arcade game. |
| **Graphics** | HTML5 Canvas 2D | Infinite vertical camera tracking, dynamic DPR scaling. |
| **Audio** | Web Audio API | Procedural sound synthesizer (stack, combo, crystals, fanfare). |
| **Backend API** | PHP 7.4+ / 8.x (PDO) | Lightweight JSON REST endpoints with caching and anti-cheat. |
| **Database** | MySQL 5.7+ / 8.0+ / MariaDB | Persistent global leaderboard with composite index. |

---

## 📁 Project Structure

```
stacksnap/
├── .github/
│   └── workflows/
│       └── ci.yml             # Automated GitHub Actions workflow
├── .gitattributes             # Line ending normalization
├── .gitignore                 # Protects config.php, old/, and OS artifacts
├── docker-compose.yml         # 1-command local fullstack environment
├── LICENSE                    # MIT Open Source License
├── manifest.json              # PWA Web App Manifest
├── README.md                  # Project documentation & API reference
├── index.html                 # Game client (HTML, CSS, JS engine)
├── config.example.php         # Safe database configuration template for Git
├── config.php                 # Local credentials file (ignored by Git)
├── db_setup.sql               # MySQL database schema setup script
├── api/                       # Backend REST endpoints
│   ├── get_leaderboard.php    # Endpoint: Fetch dense-ranked global scores
│   └── submit_score.php       # Endpoint: Validate and submit a new score
└── docs/
    └── LEADERBOARD_SETUP_IT.md# Italian backend setup guide
```

---

## 🚀 Quick Start

### Option 1: Frontend Only (Instant Offline Play)

StackSnap works out-of-the-box as a standalone client without any server:

1. Clone or download the repository.
2. Open `index.html` directly in any web browser.
3. Enjoy! The game tracks local highscores, player statistics, and achievements in your browser's `localStorage`.

---

### Option 2: Fullstack with Docker (One-Command Setup)

If you have [Docker](https://www.docker.com/) installed, launch the entire stack (PHP 8.2 + Apache + MariaDB) with one command:

```bash
docker compose up -d
```

Open `http://localhost:8080` in your browser. The database and tables are created automatically!

---

### Option 3: Fullstack on Shared / Traditional Hosting

To connect the game to your existing MySQL hosting:

#### 1. Setup the Database

Import `db_setup.sql` into your MySQL database:

```bash
mysql -u your_username -p your_database_name < db_setup.sql
```

*(Alternatively, paste the contents of `db_setup.sql` into the **SQL** tab of phpMyAdmin).*

#### 2. Configure Credentials

Copy the template file to `config.php`:

```bash
cp config.example.php config.php
```

Edit `config.php` with your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_mysql_username');
define('DB_PASS', 'your_mysql_password');
define('ALLOWED_ORIGIN', '*'); // Or comma-separated: 'https://yourdomain.com, http://localhost:8080'
```

> **Security Note:** `config.php` is listed in `.gitignore` so your database credentials will never be committed or pushed to GitHub.
>
> You can also supply credentials via environment variables: `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, and `ALLOWED_ORIGIN`.

#### 3. Upload & Connect

1. Upload the project files to your PHP-enabled hosting root or subfolder.
2. If hosting the API on a separate subdomain or directory, open `index.html` and update `API_BASE_URL`:
   ```javascript
   const API_BASE_URL = 'api'; // or 'https://yourdomain.com/api'
   ```
3. Test the endpoints in your browser:
   - `https://yourdomain.com/api/get_leaderboard.php`
   - `https://yourdomain.com/api/submit_score.php`

---

## 🔌 API Reference

### 1. `GET /api/get_leaderboard.php`

Returns global scores ordered from highest to lowest with $O(N)$ server-side dense ranking (tied scores share the same rank).

- **Query Parameters**:
  - `limit` *(optional, integer)*: Maximum number of rows to return (e.g. `?limit=100`).

- **Response Headers**:
  - `Cache-Control: public, max-age=5, stale-while-revalidate=15`

- **Response `(200 OK)`**:
  ```json
  [
    {
      "nickname": "Giangio",
      "score": 4520,
      "blocks": 112,
      "rank": 1
    },
    {
      "nickname": "PlayerTwo",
      "score": 3200,
      "blocks": 85,
      "rank": 2
    }
  ]
  ```

---

### 2. `POST /api/submit_score.php`

Submits a new score to the global database, applies anti-cheat validation, and returns the player's final rank.

- **Request Headers**:
  - `Content-Type: application/json`

- **Request Body**:
  ```json
  {
    "nickname": "PlayerOne",
    "score": 1280,
    "blocks": 48
  }
  ```

- **Validation Rules**:
  - `nickname`: Trimmed and HTML-stripped, maximum 16 characters (defaults to `"Anonymous"` if empty).
  - `score`: Non-negative integer $\le 999,999$.
  - `blocks`: Non-negative integer $\le 50,000$.
  - **Plausibility**: Verifies that score is mathematically possible given the number of stacked blocks.
  - **Rate Limit**: 3-second cooldown per IP to prevent automated flood scripts.

- **Response `(200 OK)`**:
  ```json
  {
    "ok": true,
    "rank": 4,
    "totalPlayers": 182
  }
  ```

---

## 🎮 Game Controls

| Control | Action |
|---|---|
| **Mouse Left Click** / **Screen Tap** | Drop sliding block onto tower (or resume when paused) — zero-latency `pointerdown` |
| **`Space` key** | Drop sliding block onto tower (or resume when paused) |
| **`P` or `Esc` key** | Pause / resume game |
| **`R` key** | Quick restart game |
| **Header `⟳` button** | Restart game |
| **Top-right Hamburger Menu** | Open Guide, Stats & Achievements, Global Leaderboard, Changelog, Audio Toggle, or FPS Meter |

---

## 📜 Version History

- **v2.9.0**:
  - **High-FPS Engine Upgrade**: Complete rendering refactoring to unlock silky-smooth 90Hz, 120Hz (ProMotion), and 144Hz+ displays.
  - **Zero CPU Blur Passes**: Replaced all heavy Gaussian `ctx.shadowBlur` passes on tower blocks, moving block, power-up crystals, and floating texts with GPU hardware-accelerated radiant halo strokes and drop shadows.
  - **Zero-GC Precomputed Gradients**: Block gradients and top glossy shaders are precomputed once per color step, preventing thousands of allocations per second and eliminating GC micro-stutters.
  - **Batched Canvas Calls**: 85 stars rendered in a single draw call; dual city skyline layers and windows batched into single-path GPU operations.
  - **Hardware Compositor Queue Bypass**: Canvas 2D context initialized with `{ alpha: false, desynchronized: true }` and dedicated GPU compositing layer (`will-change: transform; transform: translateZ(0)`).
  - **DPR Clamping**: Clamped `devicePixelRatio` to `2.0` max, cutting ~36% pixel fill overhead on high-density screens while maintaining retina crispness.
  - **Unified `pointerdown` Input**: Sub-millisecond touch/click response eliminating mobile 300ms delays and double-event processing.
  - **DOM Layout Thrashing Guard**: Score HUD only touches the DOM when integer values change.
  - **Live HUD FPS Meter**: Real-time FPS badge with performance color coding (Green: 55+ FPS, Amber: 30-54 FPS, Red: <30 FPS), toggleable in the hamburger menu and persisted in `localStorage`.
- **v2.8.0**:
  - **Full PWA Suite & Custom 3D App Icon**: Dedicated 3D glowing stack icon suite generated across all resolutions (`1024px`, `512px`, `192px`, maskable Android icons, `180px` Apple touch icon, `32px`/`16px` favicons, and vector SVG).
  - **Offline Playability**: Custom Service Worker (`sw.js`) precaching the entire game shell, icons, and fonts with automated cache versioning.
  - **Install Prompt UI**: Added native PWA "📲 Install App" prompt in the burger menu when eligible for installation.
  - **In-Game Icon Integration**: Visual placement of the app icon in the welcome splash screen, guide modal, and social Open Graph preview cards.
- **v2.7.0**:
  - **Graphic Engine Overhaul**: Falling slice debris physics when blocks are sliced off (overhang pieces tumble with angular velocity and fade out).
  - **3D Block Shaders**: Chamfered corners, specular top gloss bars, rim highlights, drop shadows, golden tower aura, and landing squash-and-stretch bounce.
  - **Radiant Shockwaves**: Expanding double-ring energy shockwaves on Perfect hits.
  - **Cosmic Multi-layer Sky**: Altitude-based cosmic nebula glow, shooting meteors, twinkling stars with 4-point cross sparkles, and dual-layer city skyline with illuminated windows and blinking beacons.
  - **3D Faceted Diamond Gems**: Power-up crystals feature rotating 3D facets, pulsing aura halos, and orbiting sparkle starlets.
  - **3D Tumbling Confetti**: Milestone celebrations burst with 3D paper ribbon ribbons and golden spark fountains.
  - **Framerate Normalization**: Delta-time (`dt`) physics ensuring consistent speed across 60Hz and 120Hz/144Hz (ProMotion) screens.
- **v2.6.0**:
  - Complete English localization (screens, modals, messages).
  - Added pause feature (`P`, `Esc`, tap to resume; auto-pauses on modal open or tab switch).
  - Added PWA mobile home screen support (`manifest.json`), SVG favicon, and Open Graph social cards.
  - Optimized global leaderboard query to $O(N)$ with HTTP caching headers.
  - Added anti-cheat score plausibility check and IP cooldown rate limiting.
  - Fixed leaderboard rank confirmation message display and double-submission protection.
  - Fixed Precision crystal bug: forgiven Perfect hits now properly preserve full platform width without shrinking.
  - Updated support donation link (`sputlab.com/donate/`).
- **v2.5.0**:
  - Slowmo crystal duration extended to 3 blocks.
  - New 🎯 Precision crystal (forgives 10% gap).
  - New 💥 Double Points crystal (2x score for 3 blocks).
  - Perfect hit value increased to 40 points.
  - Player nickname persistence in input form.
  - Welcome rules splash screen with "Don't show again" toggle.
- **v2.4.0**:
  - Global Leaderboard backend integration (PHP + MySQL).
  - End-of-run Local / Global leaderboard switch.
  - Real-time estimated global rank preview.
  - Dense ranking support.
- **v2.0.0 – v2.3.0**:
  - Visual redesign: 3D block gradient, dynamic starfield, clouds, skyline.
  - Procedural Web Audio API sound synthesis.
  - Infinite camera tracking system.
  - Achievements and persistent local stats.

---

## 🤝 Credits & Acknowledgements

- **Developer**: Stefano Soranzio ([Sputlab.com](https://www.sputlab.com))
- **Special Thanks**: **Giangio** for valuable feedback, gameplay balance ideas, and bug reports.

---

## 📄 License

This project is licensed under the [MIT License](LICENSE) — free for personal and commercial use.
