Visitor Photo Slideshow (WebApp + MySQL Database)

A responsive HTML/Tailwind/JS WebApp backed by PHP 8+ & MySQL (phpMyAdmin) for multi-user event photo check-ins and live Smart TV slideshow displays.

Features

Visitor Upload Form: Name, unique email/phone ID, privacy consent checkbox, and multi-photo upload (JPG, PNG, WebP up to 5 MB).

Returning Visitor & Duplicate Protection: Normalizes email/phone IDs and checks SHA-256 photo hashes in MySQL to detect revisits and skip duplicate photos with a popup modal.

TV Display & Fullscreen Mode: Crisp 60fps double-buffered crossfade slideshow, configurable slide timer (3s–15s), Loop: On/Off toggle, Pause/Replay, Change Photo, Delete Photo, and a clickable thumbnail strip.

Setup & Run (XAMPP / phpMyAdmin)

Start Apache and MySQL in the XAMPP Control Panel.

Place the project folder inside C:\xampp\htdocs\visitor-photo-slideshow-webapp.

(Optional) Copy .env.example to .env if using custom MySQL credentials (defaults: 127.0.0.1, user root, empty password, database visitor_slideshow).

Open http://localhost/visitor-photo-slideshow-Ekta%20webapp in your browser.

View the auto-created visitor_slideshow database (visitors and photos tables) in http://localhost/phpmyadmin.

Running Tests

php tests/test_runner.php