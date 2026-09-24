# SEO, performance, security, and go-live

Runbook for the Oxiron overseas site. Plugin screens live in `docs/plugin-setup.md`. This document is the production bar: what must be true before DNS points at the server, and how to keep it true.

Targets:

| Area | Bar |
| --- | --- |
| Mobile PageSpeed Insights | ≥ 85 |
| LCP | < 2.5 s |
| HTTPS | Forced; HSTS on |
| Languages | `en` default, `de` / `fr` directories, valid hreflang |
| Restore | Last UpdraftPlus backup restored on staging within the last 90 days |

---

## SEO

### Unique title and meta description on every product

Rank Math is the source of titles and descriptions. Do not leave the product CPT on the global template only.

1. **Rank Math → Titles & Meta → Products** (CPT `product`):

   - Title: `%title% | Corten Steel Manufacturer | Oxiron`
   - Description: `%excerpt%`
   - If excerpt is empty, Rank Math falls back to the first characters of the editor. Fill the excerpt on every product.

2. Per-product overrides (Rank Math meta box on the edit screen):

   | Product | Title (≤ 60 characters) | Meta description (≤ 155 characters) |
   | --- | --- | --- |
   | Corten Steel Square Planter | Corten Steel Square Planter 600 mm \| Oxiron | Weathering-steel square planter, 3 mm Corten A, folded corners and hidden drainage. MOQ 10. RFQ for EU and US delivery. |
   | Weathering Steel Enclosure | Weathering Steel Enclosure \| Custom Housing \| Oxiron | Laser-cut Corten equipment enclosure with IP54-ready seams. OEM lots, 6–8 week lead time. Specify for plant rooms and rooftops. |
   | Custom Sheet Metal Fabrication | OEM Sheet Metal Fabrication \| Laser Cut & Bend \| Oxiron | Custom brackets, panels, and housings in Corten, mild steel, aluminum, or galvanized. ±0.1 mm cutting. Drawings to crates. |

3. Homepage and archive templates are listed in `docs/plugin-setup.md` § Rank Math. Never reuse the same description on two URLs.

4. After WPML, translate Rank Math title and description per language. English keywords stay English on `/en/`; German pages use German queries (`Corten Pflanzkübel Hersteller`, `Blechgehäuse nach Maß`).

### Breadcrumbs

Enable **Rank Math → General Settings → Breadcrumbs**.

Trail for a product:

`Home > Products > Corten Steel Square Planter`

Trail after WPML:

`Startseite > Produkte > Corten-Stahl Pflanzkübel`

Print crumbs in the Elementor Single Product template with the Rank Math Breadcrumbs widget, or add `<?php if ( function_exists( 'rank_math_the_breadcrumbs' ) ) { rank_math_the_breadcrumbs(); } ?>` above the product grid in `single-product.php` / `archive-product.php`.

Schema `BreadcrumbList` is emitted by Rank Math when breadcrumbs are on. Confirm in Rich Results Test.

### XML sitemap → Google Search Console

1. Rank Math → Sitemap: include Pages, `product`, `project`, `product_category`, `application`, `material`. Exclude author archives and empty taxonomies.
2. Production index (directory URLs after WPML):

   `https://YOUR_DOMAIN/en/sitemap_index.xml`

   Without WPML directories yet:

   `https://YOUR_DOMAIN/sitemap_index.xml`

3. Google Search Console:
   - Add the **URL-prefix** property `https://YOUR_DOMAIN/` and the **Domain** property if you control DNS TXT
   - Verify by DNS TXT or HTML file (not only GA)
   - Sitemaps → submit the index URL
   - Repeat for `/de/` and `/fr/` if GSC does not pick up hreflang-linked sitemaps automatically
4. After the first index, inspect one product URL with URL Inspection → Request indexing.

### Product schema and Organization schema

**Homepage — Organization** (Rank Math → Schema → Organization):

- `@type`: Organization
- `name`: legal brand (Oxiron Fabrication until replaced)
- `url`: `https://YOUR_DOMAIN/`
- `logo`: square logo, ≥ 112 px
- `areaServed`: European Union, United States, Canada
- `contactPoint`: `sales@YOUR_DOMAIN`, contactType `sales`
- Do not attach Product schema to the homepage

**Product CPT — Product**:

- `name`, `description`, `image` (featured + gallery)
- `brand`: Organization name
- `material`: ACF `material`
- `offers`: B2B RFQ catalog — **do not invent a retail price**. Prefer no `price` or Rank Math “Offer” with URL only. Fake `$0` prices create rich-result spam.

Validate with [Google Rich Results Test](https://search.google.com/test/rich-results) on `/` and one product permalink.

### hreflang

WPML directory mode (`/en/`, `/de/`, `/fr/`) plus “Use directory for default language”. View-source on a product must contain:

```html
<link rel="alternate" hreflang="en" href="https://YOUR_DOMAIN/en/products/corten-steel-square-planter/" />
<link rel="alternate" hreflang="de" href="https://YOUR_DOMAIN/de/products/..." />
<link rel="alternate" hreflang="fr" href="https://YOUR_DOMAIN/fr/products/..." />
<link rel="alternate" hreflang="x-default" href="https://YOUR_DOMAIN/en/products/corten-steel-square-planter/" />
```

`x-default` points at English. Self-canonical in each language equals that language’s URL. Rank Math + WPML SEO must not emit a cross-language canonical.

The theme header still has static EN/DE/FR links until the WPML Language Switcher replaces them (`docs/plugin-setup.md` § WPML).

### robots.txt

Rank Math → General Settings → Edit robots.txt. Production file:

```
User-agent: *
Allow: /
Disallow: /wp-admin/
Allow: /wp-admin/admin-ajax.php

Sitemap: https://YOUR_DOMAIN/sitemap_index.xml
```

Rules:

- Allow the whole public site (products, projects, taxonomies, `/en/`, `/de/`, `/fr/`)
- Disallow `/wp-admin/` except `admin-ajax.php` (catalog AJAX and Fluent Forms)
- Do not Disallow `/wp-json/` unless you have a reason — Rank Math and WPML use REST
- Do not Disallow `/wp-content/uploads/`
- After WPML, either one sitemap line at the apex or language-specific sitemap URLs as WPML SEO documents

Confirm `https://YOUR_DOMAIN/robots.txt` is served as `text/plain` and is not the WordPress default “nolog / search”.

### Image SEO

| Rule | Practice |
| --- | --- |
| Filename | English, hyphens, no spaces: `corten-steel-planter-square-600mm.webp` |
| Alt | Describe the object and context, one sentence, not a keyword list |
| Format | ShortPixel WebP (AVIF optional). Theme sample SVGs are placeholders only |
| Dimensions | `width` and `height` on `<img>` (product cards already set 900×1125) |
| Lazy load | `loading="lazy"` on cards and below-fold images. **Do not lazy-load the hero** — it is the LCP element |
| Hero | Replace `assets/img/hero-scene.svg` with a compressed WebP/AVIF photograph; add `fetchpriority="high"` |

Alt examples:

- Good: `Corten steel 600 mm square planter with folded corners in a courtyard`
- Bad: `Corten steel planter manufacturer weathering steel planter supplier buy now`

---

## Performance

### Budgets

| Metric | Budget | How this stack helps |
| --- | --- | --- |
| Mobile PSI | ≥ 85 | Nginx static, Redis, Super Cache, WebP, limited JS |
| LCP | < 2.5 s | Hero image + `fetchpriority`, no lazy on hero, HTTP/2, cache |
| INP | < 200 ms | Vanilla `main.js`, no jQuery, Intersection Observer |
| CLS | < 0.1 | Width/height on images, header height token |

Test production URLs in PageSpeed Insights (mobile) and WebPageTest (Frankfurt + Virginia). Lab scores on `example.com` with a self-signed cert do not count.

### Gzip / Brotli

Nginx already enables **gzip** for CSS, JS, JSON, SVG, XML (`nginx/conf.d/wordpress.conf`). The official `nginx:1.27.5-alpine` image does **not** ship `ngx_brotli`.

- Production default: gzip (done)
- Optional: build a custom Nginx image with Brotli and `brotli on;` — not required to hit 85 if WebP + cache are correct

Confirm:

```powershell
curl -sI -H "Accept-Encoding: gzip" https://YOUR_DOMAIN/wp-content/themes/corten-steel/assets/css/main.css
```

Look for `Content-Encoding: gzip`.

### Images: WebP + lazy load

- ShortPixel: generate and serve WebP (`docs/plugin-setup.md` § ShortPixel)
- Product cards: `loading="lazy"` in `template-parts/product-card.php`
- Hero: eager, high priority, compressed photo, ≤ 200 KB WebP if possible
- Preload the hero in `header.php` only after the real filename is known:

```php
<link rel="preload" as="image" href="https://YOUR_DOMAIN/wp-content/uploads/.../hero.webp" fetchpriority="high">
```

### Cache-Control: max-age=31536000

Nginx serves these directly (no PHP) with `expires 365d` and `Cache-Control: public, max-age=31536000, immutable`:

- `/wp-content/uploads/` static types
- `/wp-includes/` static types
- `/wp-admin/` static types
- `/wp-content/themes/` and `/wp-content/plugins/` CSS/JS/fonts/images

WordPress cache-busts theme CSS/JS with `?ver=1.0.0` (`CORTEN_VERSION`). Bump that constant when you change `main.css` / `main.js`.

HTML pages are **not** immutable. Super Cache TTL is 3600 s (`docs/plugin-setup.md`).

### Redis object cache

- Compose service `redis:7.4.11-alpine`, AOF on, `allkeys-lru`, 256 MB cap
- `WP_REDIS_HOST=redis`, `WP_REDIS_PORT=6379` injected into `wp-config.php`
- Enable the **Redis Object Cache** plugin until the admin screen shows Connected
- Flush after deploys and after UpdraftPlus restores

```powershell
docker compose exec redis redis-cli ping
docker compose exec redis redis-cli info stats
```

### Nginx as the static file server

PHP-FPM is only used for `.php`. `try_files` in `/` serves existing files from `wp_data` (and the theme/plugin bind mounts). WordPress and Nginx both mount:

- `wp_data` → `/var/www/html`
- `./wordpress/wp-content/themes/corten-steel`
- `./wordpress/wp-content/plugins/corten-core`

If a new asset 404s, recreate Nginx so it sees the bind mount: `docker compose up -d nginx`.

### Database

MariaDB 11.4 in Compose already uses `utf8mb4`, `innodb-buffer-pool-size=256M`, `skip-name-resolve`.

On the production VPS, size the buffer pool to ~50–70% of the RAM left after PHP-FPM and Redis (256 MB is a small-site default).

Regular hygiene:

1. **WP-Optimize** or `wp db optimize` monthly (do not install overlapping cache plugins)
2. Limit post revisions: add via Compose `WORDPRESS_CONFIG_EXTRA`:

   ```php
   define( 'WP_POST_REVISIONS', 5 );
   define( 'AUTOSAVE_INTERVAL', 120 );
   define( 'EMPTY_TRASH_DAYS', 14 );
   ```

3. Delete spam comments and orphaned transients after Super Cache / Rank Math are stable
4. Indexes: `wp_posts (post_type, post_status, post_date)` is core; do not add random indexes without `EXPLAIN`
5. Object cache means Rank Math and Elementor options should not hammer `wp_options` autoload — keep autoloaded options under ~1 MB (`SELECT SUM(LENGTH(option_value)) FROM wp_options WHERE autoload='yes';`)

---

## Security

### Force HTTPS

- Port 80: ACME challenge, then `301` to HTTPS (`wordpress.conf`)
- `www` → apex on 443
- `X-Forwarded-Proto` passed to PHP; Compose sets `$_SERVER['HTTPS'] = 'on'`
- HSTS: `Strict-Transport-Security: max-age=31536000; includeSubDomains`
- WordPress Address and Site Address in **Settings → General** must be `https://YOUR_DOMAIN` (with `/en` only if WPML default-directory is on)

Issue the real certificate with `scripts/init-letsencrypt.sh` after DNS is live. Certbot renews every 12 hours in Compose; reload Nginx after a renew if the cert path changes:

```powershell
docker compose exec nginx nginx -s reload
```

### Security headers (already on the 443 server)

| Header | Value |
| --- | --- |
| X-Frame-Options | SAMEORIGIN |
| X-Content-Type-Options | nosniff |
| Referrer-Policy | strict-origin-when-cross-origin |
| Strict-Transport-Security | max-age=31536000; includeSubDomains |

Optional later: `Content-Security-Policy` once Elementor, Fluent Forms, and analytics domains are known. Do not ship an empty CSP that blocks admin.

### wp-login.php and xmlrpc.php

Nginx:

- `wp-login.php`: `limit_req zone=wp_login burst=3 nodelay` (5 requests/minute zone)
- `xmlrpc.php`: `limit_req zone=xmlrpc burst=1 nodelay` (1 r/s zone)

Wordfence adds lockout and 2FA on top. If the WordPress mobile app is unused, disable XML-RPC in Wordfence or return 403 in Nginx.

### Wordfence

- Firewall: Enabled and Protecting (learning mode ≤ 7 days)
- 2FA for every Administrator
- Daily malware scan, email to the technical inbox
- Brute-force lockout aligned with Nginx (5 failures)
- Details: `docs/plugin-setup.md` § Wordfence

### Updates

- WordPress core: minor updates on; major updates after a staging pass
- Theme `corten-steel` and plugin `corten-core`: deploy from git, do not edit inside the volume
- Commercial plugins: update monthly, Super Cache purge, PSI re-test
- Compose image pins (`wordpress:6.8.3-php8.3-fpm`, `nginx:1.27.5-alpine`, `mariadb:11.4`, `redis:7.4.11-alpine`, `certbot/certbot:v5.8.0`): bump deliberately, never `latest`

### Least privilege

| Role | Who |
| --- | --- |
| Administrator | One technical owner + backup person, 2FA |
| Editor | Content / SEO, no plugin install |
| Shop/sales | Fluent Forms entries only (or a custom role) |
| MariaDB | `wordpress` user, not `root`, from the `db` network only |
| Host SSH | Key-only, no password, firewall 80/443/SSH from known IPs |
| Redis / 3306 | Not published on the host (`backend` is `internal: true`) |

`.env` is gitignored. Placeholder passwords in `.env.example` must be replaced before production `up`.

### Backup and restore drill

1. UpdraftPlus: daily database, weekly full files, remote S3 or Drive
2. Quarterly: restore the latest backup to a staging Compose project
3. Confirm: homepage, one product, RFQ submit, Redis Connected
4. Keep volume-level dumps as a second path (`README.md`)

If the drill fails, do not ship DNS.

---

## Go-live checklist

Work top to bottom. Do not announce the URL until the last box is ticked.

### DNS and TLS

- [ ] Apex and `www` A/AAAA records point at the production VPS
- [ ] `.env` `DOMAIN` matches the apex hostname
- [ ] `docker compose up -d --force-recreate nginx` after changing `DOMAIN`
- [ ] Let’s Encrypt issued for apex + `www` (`scripts/init-letsencrypt.sh`)
- [ ] HTTP → HTTPS 301; `www` → apex 301
- [ ] Certbot container running; dry-run renew succeeds
- [ ] Browser padlock; HSTS present (add to HSTS preload list only after a stable year)

### WordPress

- [ ] Settings → General: HTTPS site URL
- [ ] Permalinks: `/%postname%/` saved twice
- [ ] Theme **Oxiron Corten Steel** active; plugin **Oxiron Corten Core** active
- [ ] Sample products replaced with real photos, MOQ, lead times, spec PDFs
- [ ] Capabilities / Contact / About / Resources pages reviewed
- [ ] Primary + footer menus assigned
- [ ] Admin username is not `admin`; local `OxironLocal!2026` password rotated

### Multilingual

- [ ] WPML: EN default, DE, FR; directories `/en/` `/de/` `/fr/`
- [ ] hreflang + x-default on home and one product
- [ ] Products, taxonomies, menus, RFQ form, Rank Math titles translated or marked “use English”
- [ ] Language switcher works on mobile

### Forms and mail

- [ ] Fluent Forms RFQ ID matches `page-contact.php` (`[fluentform id="1"]` or updated)
- [ ] Submit from production: sales notification + optional confirmation
- [ ] File upload (PDF) attached to the notification
- [ ] Spam: Turnstile/hCaptcha + honeypot
- [ ] Transactional mail: SMTP plugin (or provider API) — PHP `sendmail` is **not** in the WordPress image
- [ ] Product-page RFQ dock reaches the same form

### Mobile and performance

- [ ] iPhone and Android: nav, filters, gallery zoom, RFQ dock, language switch
- [ ] Dark/light toggle persists
- [ ] PageSpeed Insights **mobile** on `/`, `/products/`, one product ≥ 85
- [ ] LCP < 2.5 s on the homepage (real hero photo, not the SVG placeholder)
- [ ] Super Cache hit on logged-out HTML; `/contact/` not cached
- [ ] Redis Object Cache: Connected
- [ ] WebP served to Chrome; original fallback to Safari if needed

### Search and analytics

- [ ] Google Search Console verified; sitemap submitted; no `robots.txt` block
- [ ] Organization schema on home; Product schema on a product (Rich Results Test)
- [ ] Analytics: **Plausible** or **Fathom** preferred for EU B2B (cookie-light). GA4 only with a Consent Mode banner if you use advertising features
- [ ] If GA4: production Measurement ID, no localhost hits, IP filters for the office

### Backup and ops

- [ ] UpdraftPlus remote backup completed and downloaded once
- [ ] Restore drill on staging documented (date, operator, result)
- [ ] Wordfence 2FA on all administrators; learning mode ended
- [ ] Host firewall: 80, 443, SSH; no 3306/6379
- [ ] Monitoring: Uptime (HTTPS, 5 min) + disk on Docker volumes
- [ ] Runbook links: `README.md`, `docs/plugin-setup.md`, this file

### Final content pass

- [ ] No `example.com` in public copy, emails, or schema (except local hosts)
- [ ] No lorem, no SVG product shots on money pages
- [ ] Legal: privacy policy and imprint (Impressum if you target Germany/Austria)
- [ ] Open Graph image 1200×630 for home and products
- [ ] 404 page still industrial (`404.php`) and linked to Products + RFQ
