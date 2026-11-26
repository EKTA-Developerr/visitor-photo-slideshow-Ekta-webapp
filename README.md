# Visitor Photo Slideshow (WebApp)

A responsive, zero-backend **HTML/CSS/JavaScript WebApp** for event photo check-ins and live Smart TV slideshow displays.

## Features
- **Visitor Upload Form**: Name, unique email/phone ID, privacy consent checkbox, and multi-photo upload (JPG, PNG, WebP up to 5 MB).
- **Returning Visitor & Duplicate Protection**: Normalizes email/phone IDs and hashes photos with SHA-256 (`crypto.subtle.digest`) to detect revisits and skip duplicate photos with a clear popup modal.
- **TV Display & Fullscreen Mode**: Crisp double-buffered crossfade slideshow, configurable slide timer (3s–15s), Pause/Resume, and a clickable thumbnail strip of all uploaded photos.
- **Persistent Browser Storage**: Uses `localStorage` so uploaded photos and visitor records persist across page reloads.

## Setup & Run
Open `index.html` directly in any modern browser, or serve locally:
```bash
python3 -m http.server 8000
```
Then visit `http://localhost:8000`.
