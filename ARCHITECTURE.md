# System Architecture (WebApp)

## Tech Stack
- **Frontend**: Responsive HTML5, Tailwind CSS, and Vanilla JavaScript
- **Hashing & State**: Web Crypto API (`SHA-256`) + `localStorage`

## System Diagram
```mermaid
flowchart LR
    User[Visitor Browser] -->|Upload Form + Consent| Val[File Type & 5MB Validator]
    Val -->|SHA-256 Hash| Dup[Revisit & Duplicate Check]
    Dup -->|Save| Store[(localStorage State)]
    Store -->|Double-Buffered Crossfade| TV[TV Display & Fullscreen Stage]
```
