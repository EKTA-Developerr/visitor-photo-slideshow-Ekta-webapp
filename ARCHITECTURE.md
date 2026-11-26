System Architecture

Tech Stack

Frontend: Responsive HTML5, Tailwind CSS, Vanilla JavaScript (index.html)

Backend: PHP 8+ REST endpoints (api/upload.php, api/slideshow.php, api/replace.php, api/delete.php, api/reset.php)

Database: MySQL (PDO_MYSQL, InnoDB engine) with flock concurrency locking

Data Model

visitors(id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(150), identifier VARCHAR(255) UNIQUE, consent_given TINYINT(1), created_at TIMESTAMP)

photos(id INT AUTO_INCREMENT PRIMARY KEY, visitor_id INT FK, filename VARCHAR(255) UNIQUE, file_hash CHAR(64), created_at TIMESTAMP) with UNIQUE KEY uniq_visitor_hash (visitor_id, file_hash)

System Diagram

flowchart LR
    Browser[index.html WebApp] -->|POST api/upload.php| API[PHP Upload & Validation]
    API -->|Magic Bytes + SHA-256 + flock| DB[(MySQL visitor_slideshow)]
    API -->|Save Image| Disk[(uploads/)]
    Browser -->|Poll api/slideshow.php| API
    Browser -->|POST api/replace.php / delete.php| API

