# 🧱 StackSnap · Arcade Plus

[![Version](https://img.shields.io/badge/version-2.6.0-blue.svg)](index.html)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Zero Dependencies](https://img.shields.io/badge/dependencies-0-brightgreen.svg)](#architecture--tech-stack)
[![PHP](https://img.shields.io/badge/backend-PHP%20PDO%20%7C%20MySQL-8892BF.svg)](api/)

> **StackSnap** is an addictive, fast-paced arcade browser game built with pure **HTML5 Canvas**, **Vanilla JavaScript**, and a procedural **Web Audio API** synthesizer. Stack moving blocks as high as possible, center each slice, trigger multiplier combos, collect power-up crystals, unlock achievements, and compete on the global leaderboard!

---

## ✨ Features

- **🎮 Addictive Arcade Stacking Mechanics**:
  - Horizontal block oscillation with sub-pixel precision slicing.
  - **Perfect Alignment Bonus**: Exact placement (or precision-assisted) preserves full platform width and awards **+40 points**.
  - **Streak Multiplier**: Score 3 Perfects in a row to unleash a **x2 multiplier** for your next 3 blocks.
  - **Golden Tower**: Every 5th block provides a **+50 bonus**.
  - **Perfect Zone**: A high-stakes target appears every 10 blocks granting **x3 points**.
  - **Safety Net**: Built-in rescue system automatically recovers a critically thin block once per game.
  - **Milestone Celebrations**: Every 25 blocks triggers celebratory confetti, fanfare melodies, and camera shake.

- **💎 Power-Up Crystals**:
  - 🐢 **Blue (Slowmo)**: Slows sliding block speed by ~42% for 3 blocks.
  - 🎯 **Orange (Precision)**: Forgives alignment gaps up to 10% as Perfect hits.
  - 💥 **Red (Double Points)**: Doubles all points for 3 blocks.

- **🔊 100% Procedural Synthesizer Audio**:
  - Zero external `.mp3` or `.wav` files required!
  - Real-time procedural audio synthesis using the **Web Audio API** (sine, triangle, square, and sawtooth waves with dynamic envelope shaping).

- **🌌 Dynamic Procedural Visuals**:
  - Parallax procedural starfield with twinkling phases.
  - Drifting clouds and procedural city skyline silhouettes.
  - Smooth day-to-night sky gradient transitioning as the tower ascends.
  - Particle sparks, floating score popups, and milestone confetti.

- **📱 Fully Responsive & Zero-Build**:
  - Pure Vanilla JavaScript and CSS glassmorphism.
  - Adapts to desktop monitors, tablets, and small mobile screens (e.g. iPhone 12 mini).
  - Multi-input controls: Mouse click, screen tap, or Keyboard (`Space` to drop, `R` to restart).

- **🏆 Dual Leaderboards & Persistence**:
  - **Local Leaderboard**: Offline storage in `localStorage` for high scores.
  - **Global Leaderboard**: Shared backend powered by PHP PDO and MySQL with server-side dense ranking (tied scores share the same rank).
  - **Player Stats & 12 Achievements**: In-depth statistics tracking games played, total blocks, best streaks, play time, and unlocked badges.

---

## 🏗️ Architecture & Tech Stack

```
                          ┌──────────────────────────────────────┐
                          │         Browser Client               │
                          │                                      │
                          │  • HTML5 Canvas 2D (Dynamic DPR)     │
                          │  • Web Audio API Synthesizer         │
                          │  • LocalStorage (Stats/Achievements) │
                          └──────────────────┬───────────────────┘
                                             │ HTTP REST (JSON)
                                             ▼
                          ┌──────────────────────────────────────┐
                          │         Backend (PHP PDO)            │
                          │                                      │
                          │  • api/get_leaderboard.php           │
                          │  • api/submit_score.php              │
                          │  • config.php (CORS & DB Connection) │
                          └──────────────────┬───────────────────┘
                                             │ SQL
                                             ▼
                          ┌──────────────────────────────────────┐
                          │         MySQL Database               │
                          │                                      │
                          │  • Table: stacksnap_scores           │
                          │  • Dense Rank Server Calculation     │
                          └──────────────────────────────────────┘
```

| Component | Technology | Description |
|---|---|---|
| **Frontend** | HTML5, CSS3, Vanilla JS | Zero-build, self-contained single-page arcade game. |
| **Graphics** | HTML5 Canvas 2D | Infinite vertical camera tracking, dynamic DPR scaling. |
| **Audio** | Web Audio API | Procedural sound synthesizer (stack, combo, crystals, fanfare). |
| **Backend API** | PHP 7.4+ / 8.x (PDO) | Lightweight JSON REST endpoints. |
| **Database** | MySQL 5.7+ / 8.0+ | Persistent global leaderboard with indexed queries. |

---

## 📁 Project Structure

```
stacksnap/
├── .gitattributes             # Line ending normalization
├── .gitignore                  # Protects credentials and ignores system files
├── LICENSE                     # MIT Open Source License
├── README.md                   # Project documentation
├── index.html                  # Standalone game entry point (HTML, CSS, JS engine)
├── config.example.php          # Database configuration template (safe for Git)
├── config.php                  # Local database configuration (ignored by Git)
├── db_setup.sql                # MySQL database schema setup script
├── api/                        # Backend REST endpoints
│   ├── get_leaderboard.php     # Endpoint: Fetch dense-ranked global scores
│   └── submit_score.php        # Endpoint: Validate and submit a new score
└── docs/
    └── LEADERBOARD_SETUP_IT.md # Original Italian setup guide
```

---

## 🚀 Quick Start

### Option 1: Frontend Only (Instant Offline Play)

StackSnap works out-of-the-box as a standalone client without any server:

1. Clone or download the repository.
2. Open `index.html` directly in any web browser.
3. Enjoy! The game will track local highscores, player statistics, and achievements in your browser's `localStorage`.

---

### Option 2: Fullstack Deployment (With Global Leaderboard)

To enable the shared Global Leaderboard:

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
define('ALLOWED_ORIGIN', '*'); // Or restrict to 'https://yourdomain.com'
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

Returns all global scores ordered from highest to lowest with dense ranking (tied scores share the same rank).

- **Query Parameters**:
  - `limit` *(optional, integer)*: Maximum number of rows to return (e.g. `?limit=100`).

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

Submits a new score to the global database and calculates the player's final rank.

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
| **Mouse Left Click** / **Screen Tap** | Drop sliding block onto tower |
| **`Space` key** | Drop sliding block onto tower |
| **`R` key** | Quick restart game |
| **Header `⟳` button** | Restart game |
| **Top-right Hamburger Menu** | Open Guide, Stats & Achievements, Global Leaderboard, Changelog, or Audio Toggle |

---

## 📜 Version History

- **v2.6.0**:
  - Complete English localization (screens, modals, messages).
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
