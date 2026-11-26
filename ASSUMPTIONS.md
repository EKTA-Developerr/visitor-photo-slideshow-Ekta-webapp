# Design Assumptions & Engineering Decisions

| Gap / Question | Decision Made | Reason |
|---|---|---|
| **Returning Visitor Identification** | Normalize `email` or `phone` (lowercase/trimmed) as a unique visitor key. | Prevents collisions when two visitors share the same name. |
| **Duplicate Photo Uploads** | Compute SHA-256 hash of each image using Web Crypto API (`crypto.subtle.digest`). | Prevents the same visitor from accidentally queuing the same photo twice and triggers a revisit popup. |
| **Slide Duration** | Default 8 seconds, configurable from 3s to 15s in the TV Display bar. | Balances readability with queue rotation speed. |
| **Max File Size & Formats** | 5 MB per photo; JPEG, PNG, and WebP only. | Keeps browser memory and `localStorage` responsive. |
| **Privacy & Consent** | Mandatory consent checkbox; TV display shows only the visitor's name. | Protects visitor email/phone PII on a public screen. |
