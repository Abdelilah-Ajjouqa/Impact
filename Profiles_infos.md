# Impact — Demo Accounts & Profiles Reference

> **Web Application:** [http://localhost:8000](http://localhost:8000)  
> **Database:** MySQL on `localhost:3306` (`impact`)  
> **Storage:** Local Public Symlink (`storage/app/public` linked to `public/storage`)

---

## 1. Authentication Credentials Summary

All accounts use the standard password `Password123456!`.

| Role | Name | Username | Email | Password | Details |
|---|---|---|---|---|---|
| **Administrator** | Admin Impact | `admin` | `admin@impact.test` | `Password123456!` | Full administrative moderation, post review, user management |
| **Demo Creator** | Alex Rivera | `demouser` | `demo@impact.test` | `Password123456!` | Curates collections, following 3 creators, multiple liked & saved posts |
| **Creator 1** | Elena Rostova | `elena_art` | `elena@impact.test` | `Password123456!` | Fine art, minimal architecture |
| **Creator 2** | Marcus Chen | `marcus_design` | `marcus@impact.test` | `Password123456!` | Editorial typography & UI design |
| **Creator 3** | Sarah Jenkins | `sarah_clicks` | `sarah@impact.test` | `Password123456!` | High-res nature & street photography |
| **Creator 4** | Liam O'Connor | `liam_visuals` | `liam@impact.test` | `Password123456!` | Motion graphics, 3D simulations |
| **Creator 5** | Yasmine Alami | `yasmine_studio` | `yasmine@impact.test` | `Password123456!` | Ceramic glaze & physical craft studies |
| **Creator 6** | Karim Benali | `karim_motion` | `karim@impact.test` | `Password123456!` | Kinetic typography & visual pacing |
| **Creator 7** | Sofia Rossi | `sofia_lens` | `sofia@impact.test` | `Password123456!` | Brutalist architecture & shadows |
| **Creator 8** | David Miller | `david_craft` | `david@impact.test` | `Password123456!` | Woodworking & Scandinavian interiors |

---

## 2. Seeded Content & Media Highlights

- **36 Rich Media Posts**: Ingested directly from your local uploads (24 high-resolution photography cards and 12 `.mp4` video cards with auto-play previews).
- **5 Curated Public Albums**:
  1. *Minimal Architecture & Forms*
  2. *Golden Hour & Street Vibes*
  3. *Editorial Typography & Print*
  4. *Abstract Motion & 3D Loops*
  5. *Nordic Living & Warm Spaces*
- **Social Graph**: Active follower graph, 30 design-focused comments, and 18 bookmark likes.

---

## 3. Re-running the Seed
To re-seed the database at any time:
```bash
docker exec impact_app php artisan db:seed
```
