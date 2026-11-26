# AI Engineering Log

1. **Prompt 1 — Responsive WebApp UI**: Built a single-page responsive WebApp with Upload Photo and TV Display views, slide timer, and fullscreen mode.
2. **Prompt 2 — Returning Visitor Schema**: Normalized email/phone into a `UNIQUE` SQLite `visitors.identifier` column so two people with the same name never collide.
3. **Prompt 3 — File Validation (AI Mistake #1 Fixed)**: Replaced extension-only checks with binary magic-byte inspection (`includes/validators.php`) so renamed `.txt` files are rejected.
4. **Prompt 4 — Duplicate Photo Prevention (AI Mistake #2 Fixed)**: Added SHA-256 hashing (`UNIQUE(visitor_id, file_hash)`) and a revisit popup modal so duplicate uploads are skipped cleanly.
5. **Prompt 5 — Smooth 60fps Crossfade (AI Mistake #3 Fixed)**: Replaced single-`<img>` swapping with double-buffered full-black layers and `requestAnimationFrame` so wide/narrow photos never overlap or flicker.
6. **Prompt 6 — Database Connectivity & CRUD**: Connected the WebApp frontend to PHP + SQLite endpoints (`upload`, `slideshow`, `replace`, `delete`, `reset`) with `flock` concurrency safety.
