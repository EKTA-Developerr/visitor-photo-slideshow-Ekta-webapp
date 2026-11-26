# Test Plan & Verification Results

## Automated Tests (`tests/test_runner.php`)
Run with:
```bash
php tests/test_runner.php
```

| Test Case | Expected Behavior | Status |
|---|---|---|
| **Missing Privacy Consent** | Upload rejected with HTTP 400 (`Privacy consent is required`). | Pass |
| **Invalid File Type (Spoofed Extension)** | Non-image bytes renamed to `.jpg` rejected via magic-byte check. | Pass |
| **Returning Visitor Recognition** | Re-using `Ekta@gmail.com` matches existing visitor and replays prior photos. | Pass |
| **Duplicate Photo Prevention** | Uploading the exact same image hash for the same visitor is skipped (`skipped_duplicates = 1`). | Pass |

## Manual UI & Concurrency Test Plan
1. **Multi-Photo & Crossfade Check**: Upload 2+ photos of different aspect ratios (wide vs square) -> Verified clean full-black frame with smooth 60fps crossfade and zero overlap.
2. **Slide Timer & Controls**: Change duration (`3s`–`15s`), click `Pause`/`Resume`, `Next ->`, and `Fullscreen` -> Verified live countdown updates accurately.
3. **Change & Delete Photo**: Click `Change` or `Delete` on any thumbnail -> Verified SQLite `photos` table and `uploads/` directory update immediately.
4. **Concurrent Uploads**: Submit uploads from two browser tabs simultaneously -> Verified `flock` + SQLite WAL handles both without lock errors.
