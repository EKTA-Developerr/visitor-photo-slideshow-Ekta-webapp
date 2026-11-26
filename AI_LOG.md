AI Engineering Log

Prompt 1 — Responsive WebApp UI: Built a single-page responsive WebApp with Upload Photo and TV Display views, slide timer, Loop: On/Off toggle, and fullscreen mode.

Prompt 2 — Returning Visitor Schema: Normalized email/phone into a UNIQUE MySQL visitors.identifier column so two people with the same name never collide.

Prompt 3 — File Validation (AI Mistake #1 Fixed): Replaced extension-only checks with binary magic-byte inspection (includes/validators.php) so renamed .txt files are rejected.

Prompt 4 — Duplicate Photo Prevention (AI Mistake #2 Fixed): Added SHA-256 hashing (UNIQUE KEY uniq_visitor_hash (visitor_id, file_hash)) and a revisit popup modal so duplicate uploads are skipped cleanly.

Prompt 5 — Smooth 60fps Crossfade (AI Mistake #3 Fixed): Replaced single-<img> swapping with double-buffered full-black layers and requestAnimationFrame so wide/narrow photos never overlap or flicker.

Prompt 6 — Database Connectivity & CRUD: Connected the WebApp frontend to PHP endpoints (upload, slideshow, replace, delete, reset) with flock concurrency safety.

Prompt 7 — Migration to MySQL (phpMyAdmin): Switched includes/config.php and includes/db.php from SQLite to PDO_MYSQL with automatic CREATE DATABASE IF NOT EXISTS so tables are visible and inspectable in phpMyAdmin.