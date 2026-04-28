# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Corporate website for Petersen S.A. (Paraguay industrial distributor) — PHP frontend + custom SQLite CMS. Production: https://petersen.com.py

## Development Setup

**Local server:** Apache serves from `/opt/homebrew/var/www/petersen`

```bash
# Restart local Apache
sudo brew services restart httpd

# Access local site
open http://localhost/petersen

# Access local CMS
open http://localhost/petersen/cms
```

**CMS default config** (`cms/includes/config.php`):
- `ENVIRONMENT = 'development'` — shows errors, disables secure cookies
- SQLite DB: `cms/database/petersen_cms.db`
- Session timeout: 1 hour

## Deployment

The server (181.40.91.194:2250) **cannot reach GitHub** (VPN firewall blocks outbound HTTPS). The local machine acts as a bridge: local → GitHub AND local → server via rsync over SSH.

```bash
# Deploy to production (requires clean working tree)
./deploy.sh
```

`deploy.sh` pushes to GitHub, rsync's files to `/var/www/petersen/public`, and sets permissions. It uses `sshpass` with `SSHPASS` env var for auth.

Excluded from rsync: `logs/`, `deploy.sh`, `DEPLOYMENT.md`, `mockups/`, `www_petersen_com_py18-06-2025/`, `.DS_Store`, `*.log`.

**Never use `git pull` on the server** — it will time out trying to reach GitHub.

## Architecture

### Frontend (public site)
- Each page is a standalone PHP file at root level (e.g., `index.php`, `quienes-somos.php`, `division-metalurgica.php`)
- Shared layout via `includes/header.php` and `includes/footer.php`
- All pages use `<?php include 'includes/header.php'; ?>` / `<?php include 'includes/footer.php'; ?>`
- Static assets: `assets/css/styles.css`, `assets/js/main.js`, `assets/images/`
- Blog posts fetched from SQLite; individual posts via `blog-post.php`
- Form submissions handled by `includes/contact-handler.php`, `includes/form-handler.php`, etc.

### CMS (`/cms`)
- Protected by `.htaccess` + PHP session auth
- Core includes in `cms/includes/`: `config.php`, `database.php`, `auth.php`, `security.php`, `user.php`, `blog.php`, `media.php`
- WYSIWYG editor: Quill.js
- Media uploads stored in `assets/media/` (images/videos/documents/other)
- Two roles: `administrador` (full access) and `editor` (blog + media only)

### Database schema (SQLite)
- `users` — id, username, email, password (bcrypt), full_name, role, status, last_login
- `blog_posts` — id, title, slug, excerpt, content, featured_image, author_id, status (draft/published), published_at
- `media` — id, filename, filepath, file_type, mime_type, file_size, width, height, title, alt_text, uploaded_by

### Security
- CSRF tokens on all forms
- Rate limiting on login (5 attempts / 15 min)
- PDO prepared statements throughout
- Security events logged to `logs/security.log`

## Brand

- **Colors:** `#2c3e5c` (primary blue), `#f26522` (orange), `#25d366` (WhatsApp green)
- **Font:** Raleway (Google Fonts), weights 300–700
- **Breakpoints:** mobile < 768px, tablet 768–1024px, desktop > 1024px

## Production Notes

- SSL cert (SSL2BUY commercial) expires **July 2026** — must be renewed manually
- Production config requires: `ENVIRONMENT = 'production'`, `session.cookie_secure = 1`
- Apache user: `www-data`; required permissions: dirs 755, files 644, DB 600
- Backups auto-created as `/var/www/backup-YYYYMMDD-HHMMSS.tar.gz` on each deploy
- Apache logs: `/var/log/apache2/petersen_ssl_error.log`
