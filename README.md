# Corten Steel Manufacturer — Overseas WordPress Site

Industrial B2B website for a weathering-steel (Corten) sheet-metal manufacturer. Product lines: Corten planters and planter boxes, sheet-metal enclosures, and custom fabricated parts. Primary audience: landscape architects, architecture studios, premium garden retailers, and industrial equipment buyers in Europe and North America.

Default public language is English. German and French are reserved for WPML in a later stage.

This repository is a WordPress + Docker + Nginx stack for Oxiron: Compose, the `corten-steel` theme, the `corten-core` plugin, and production runbooks under `docs/`.

## Stack

| Role | Image (pinned, no `latest`) |
| --- | --- |
| CMS / PHP-FPM | `wordpress:6.8.3-php8.3-fpm` |
| Reverse proxy | `nginx:1.27.5-alpine` |
| Database | `mariadb:11.4` (LTS) |
| Object cache | `redis:7.4.11-alpine` |
| TLS | `certbot/certbot:v5.8.0` |

Apache is not used. MariaDB and Redis listen only on the internal `backend` network and are not published to the host.

```
Internet
   │
   ▼
Nginx :80 / :443     (frontend network)
   │
   ▼
WordPress PHP-FPM    (frontend + backend)
   ├── MariaDB       (backend, internal)
   └── Redis         (backend, internal)
Certbot (renewal loop, frontend, outbound HTTPS to Let's Encrypt)
```

## Directory structure

```
corten-steel-site/
├── docker-compose.yml
├── .env.example
├── .gitignore
├── README.md
├── docs/
│   ├── plugin-setup.md
│   └── seo-performance-security.md
├── nginx/
│   ├── conf.d/
│   │   └── wordpress.conf
│   ├── docker-entrypoint.d/
│   │   └── 15-prepare-nginx.sh
│   └── ssl/
│       └── .gitkeep
├── certbot/
│   ├── conf/
│   │   └── .gitkeep
│   └── www/
│       └── .gitkeep
├── wordpress/
│   ├── .gitkeep
│   └── php/
│       └── conf.d/
│           └── uploads.ini
├── db/
│   └── .gitkeep
└── scripts/
    └── init-letsencrypt.sh
```

WordPress core, uploads, and plugins installed through wp-admin live in the `wp_data` named volume. MariaDB lives in `db_data`. Redis AOF lives in `redis_data`. The `wordpress/` and `db/` directories are placeholders for later bind-mounts (custom theme, custom plugin, SQL dumps).

`${DOMAIN}` in `nginx/conf.d/wordpress.conf` is substituted from `.env` by the official Nginx image (`/etc/nginx/templates`).

## First-time deployment

Requirements: Docker Engine with Compose v2, a DNS A (and AAAA if used) record pointing at the server, and ports 80/443 reachable from the internet for Let's Encrypt HTTP-01.

If `docker compose up -d` fails with `registry-1.docker.io` timeouts or IPv6 `connectex` errors, Docker Hub is unreachable from this network. `.env.example` already points image variables at the DaoCloud Hub mirror (`docker.m.daocloud.io`). Copy those `*_IMAGE=` lines into `.env` and retry. On an overseas VPS with a clean Hub connection, delete the `*_IMAGE=` lines so Compose uses the official tags.

### 1. Copy environment variables

```powershell
cd corten-steel-site
Copy-Item .env.example .env
```

Edit `.env`:

- Set `DOMAIN` to the real hostname (not `example.com` in production).
- Set `CERTBOT_EMAIL` to a mailbox you monitor.
- Replace `MYSQL_ROOT_PASSWORD` and `MYSQL_PASSWORD` / `WORDPRESS_DB_PASSWORD` with unique secrets. The two WordPress DB password values must match.
- Do not commit `.env`.

### 2. Start the stack

```powershell
docker compose up -d
docker compose ps
```

On a fresh host, Nginx creates a **temporary self-signed certificate** so the 443 listener can start. Browsers will show a certificate warning until Let's Encrypt is issued.

### 3. Issue a Let's Encrypt certificate

DNS for `DOMAIN` and `www.DOMAIN` must already resolve to this server.

Git Bash or WSL:

```bash
sh scripts/init-letsencrypt.sh
```

PowerShell equivalent:

```powershell
# Load DOMAIN and CERTBOT_EMAIL from .env, then:
docker compose run --rm --entrypoint certbot certbot certonly `
  --webroot --webroot-path=/var/www/certbot `
  --email YOUR_EMAIL --agree-tos --no-eff-email --force-renewal `
  -d your-domain.com -d www.your-domain.com
docker compose exec nginx nginx -s reload
```

If a dummy certificate directory already exists, delete it first so Certbot can write the live lineage:

```powershell
Remove-Item -Recurse -Force ".\certbot\conf\live\your-domain.com",".\certbot\conf\archive\your-domain.com",".\certbot\conf\renewal\your-domain.com.conf" -ErrorAction SilentlyContinue
```

Certbot in Compose runs a 12-hour renew loop. After a successful renew, reload Nginx (the renew hook is not wired to a reload; reload after you confirm a new cert, or restart `nginx`):

```powershell
docker compose exec nginx nginx -s reload
```

### 4. WordPress installation wizard

Open `https://your-domain.com/wp-admin/install.php`.

- Site title: weathering-steel manufacturer brand name.
- Language: English.
- Admin username: do **not** use `admin`.
- Admin password: a long random password stored in a password manager.
- Search-engine visibility: leave unchecked until content and Rank Math are ready.

WordPress Site URL should be `https://your-domain.com` (HTTPS, no `www`; Nginx redirects `www` to apex).

## Backup and restore

### Database backup

```powershell
docker compose exec db sh -c 'mariadb-dump -u root -p"$MYSQL_ROOT_PASSWORD" --single-transaction --routines --databases wordpress' > "db\wordpress-$(Get-Date -Format yyyyMMdd-HHmmss).sql"
```

### Database restore

```powershell
Get-Content .\db\wordpress-YYYYMMDD-HHMMSS.sql | docker compose exec -T db sh -c 'mariadb -u root -p"$MYSQL_ROOT_PASSWORD"'
```

### WordPress files (uploads, plugins, themes)

```powershell
docker run --rm -v corten-steel_wp_data:/data -v ${PWD}:/backup alpine:3.21 tar czf /backup/wp-data.tgz -C /data .
```

Restore:

```powershell
docker run --rm -v corten-steel_wp_data:/data -v ${PWD}:/backup alpine:3.21 tar xzf /backup/wp-data.tgz -C /data
```

The Compose project name is `corten-steel`, so named volumes are typically `corten-steel_wp_data`, `corten-steel_db_data`, and `corten-steel_redis_data`. Confirm with `docker volume ls`.

After Phase 4, UpdraftPlus handles scheduled off-site backups (Google Drive or S3). Keep these volume dumps as a second restore path.

## Common commands

```powershell
docker compose up -d
docker compose ps
docker compose logs -f nginx wordpress db
docker compose exec wordpress php -v
docker compose exec db mariadb -u wordpress -p"$MYSQL_PASSWORD" wordpress
docker compose exec redis redis-cli ping
docker compose exec nginx nginx -t
docker compose exec nginx nginx -s reload
docker compose run --rm --entrypoint certbot certbot renew --dry-run
docker compose down
```

Named volumes are kept on `docker compose down`. They are removed only with `docker compose down -v` (destructive).

## Production notes

- Never use `latest` image tags. Bump pins in `docker-compose.yml` after testing.
- Never publish MariaDB (`3306`) or Redis (`6379`) to the host. They are on the internal `backend` network.
- Never hard-code production passwords in Compose, Nginx, or the theme. Read them from `.env`.
- Rotate the placeholder values from `.env.example` before the first production `up`.
- Keep `WORDPRESS_DB_PASSWORD` identical to `MYSQL_PASSWORD`.
- PHP upload limit matches Nginx: 256 MB (`wordpress/php/conf.d/uploads.ini` and `client_max_body_size`).
- After changing `DOMAIN`, recreate Nginx so envsubst and certificate paths update: `docker compose up -d --force-recreate nginx`.
- Put the real hostname in WordPress **Settings → General** (WordPress Address and Site Address).
- Enable a host firewall: allow 80/443 only; SSH from known IPs.
- Plan WPML directory URLs (`/en/`, `/de/`, `/fr/`) before publishing permalinks.
- Commercial plugins (Elementor Pro, WPML, DMZ B2B, ShortPixel, Wordfence, UpdraftPlus, Rank Math, Fluent Forms) are installed in later phases; they are not bundled here.
- Redis Object Cache will use `WP_REDIS_HOST` / `WP_REDIS_PORT` already injected into `wp-config.php` via `WORDPRESS_CONFIG_EXTRA`.

## Screenshot for the theme (Phase 2)

The Oxiron theme lives at `wordpress/wp-content/themes/corten-steel/` and is bind-mounted into both Nginx and WordPress.

After WordPress is installed, activate it under **Appearance → Themes**. Then assign menus to **Primary** and **Footer**, and create pages using the **Capabilities** and **Contact** templates.

Activate **Oxiron Corten Core** under **Plugins**. Activation registers `product` and `project` CPTs, taxonomies, REST filters, and imports three products plus two projects. Re-run from **Tools → Oxiron Sample Data**.

Commercial plugin licenses, Elementor Theme Builder, WPML directories, Rank Math, Fluent Forms RFQ, Redis + Super Cache, ShortPixel, Wordfence, and UpdraftPlus are documented in `docs/plugin-setup.md`. SEO titles, PageSpeed budgets, security headers, and the go-live checklist are in `docs/seo-performance-security.md`.

To show a thumbnail in the theme picker, add a 1200×900 PNG at:

`wordpress/wp-content/themes/corten-steel/screenshot.png`

WordPress shows that file on **Appearance → Themes**. Dark/light toggle, product AJAX filters, and demo catalog cards work before the corten-core plugin is installed.
