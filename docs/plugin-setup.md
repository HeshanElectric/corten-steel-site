# Plugin install and configuration

Commercial and free plugins for the Oxiron weathering-steel manufacturer site. The Oxiron theme (`corten-steel`) and **Oxiron Corten Core** (`corten-core`) are already in the repo. Do not replace the `product` CPT that `corten-core` registers.

Default language: English. German and French are added with WPML directory URLs.

License keys belong in wp-admin, never in git, `.env`, or Compose files.

## Recommended install order

1. Advanced Custom Fields (free or Pro) — unlocks the Product Details field group
2. Redis Object Cache
3. WP Super Cache
4. Rank Math SEO
5. Fluent Forms
6. Elementor, then Elementor Pro
7. WPML Multilingual CMS + String Translation + WPML for ACF + WPML SEO
8. Wordfence
9. UpdraftPlus
10. ShortPixel Image Optimizer
11. DMZ B2B Product Management (map it onto the existing `product` CPT)

Install zips from **Plugins → Add New → Upload Plugin**. Activate one plugin, configure it, then continue. After WPML, set permalinks to `/%postname%/` again (**Settings → Permalinks → Save**).

---

## 0. Advanced Custom Fields (prerequisite)

`corten-core` registers the **Product Details** field group on `acf/init`. Without ACF the same values still live in post meta; with ACF they become an editor UI.

1. Install **Advanced Custom Fields** (or ACF Pro if you need the official Gallery/Repeater UI in older ACF; ACF 6.x free includes Repeater? Confirm the installed version — Pro is required for Gallery and Repeater on many licenses).
2. Activate ACF. Open any **Product** in wp-admin. You should see **Product Details** with:

| Field | Type | Values |
| --- | --- | --- |
| `gallery` | Gallery | Attachment IDs |
| `material` | Select | Corten Steel / Mild Steel / Aluminum / Galvanized |
| `dimensions` | Repeater | `label` + `value` |
| `surface_finish` | Select | Natural Rust / Powder Coated / Raw |
| `customization` | Checkbox | Size / Logo / Packaging / Color |
| `moq` | Number | Minimum order quantity |
| `lead_time` | Text | e.g. `4–6 weeks` |
| `download_spec_sheet` | File | PDF/DOC |
| `size_range` | Select | compact / standard / architectural (catalog AJAX filter) |

3. Location rule is **Post Type is equal to product**. Do not duplicate this group in the ACF UI.
4. Saving a product copies `material` onto the `material` taxonomy so Rank Math, WPML, and the archive filter stay aligned.

---

## 1. Elementor Pro

### Install and license

1. Install **Elementor**, then **Elementor Pro**.
2. **Elementor → License** → paste the Pro license → Activate.
3. **Elementor → Settings → General**: post types **Pages**, **Products**, **Projects**. Disable Posts unless you use the blog.
4. **Elementor → Settings → Features**: keep Container enabled.

### Global colors (match theme CSS variables)

**Site Settings → Global Colors** (Oxiron tokens from `style.css`):

| Elementor name | Hex | Token |
| --- | --- | --- |
| Primary | `#B85C38` | `--color-rust` |
| Secondary | `#8B6F47` | `--color-copper` |
| Accent / CTA | `#D97706` | `--color-cta` |
| Text | `#E5E5E5` | `--color-text` (dark theme) |
| Heading | `#F4EFE8` | `--color-heading` |
| Metal | `#6B7280` | `--color-metal` |
| Background | `#1A1A1A` | `--color-bg` |

Light-theme page builds should use `#F5F5F0` background and `#1F2937` text. Buttons: hover from `#B85C38` to `#D97706` (already in `main.css` for `.btn--primary`).

### Global fonts

**Site Settings → Global Fonts**:

| Role | Family | Weight | Notes |
| --- | --- | --- | --- |
| Primary headline | Space Grotesk | 700–800 | Uppercase, letter-spacing ~0.08em |
| Body | Inter | 400/500 | Line height 1.7 |
| Accent / specs | JetBrains Mono | 400/500 | Dimension tables |

The theme already loads these from Bunny Fonts. In Elementor, pick the same families rather than adding a second Google Fonts request.

### Theme Builder

The theme calls `elementor_theme_do_location()` for `header`, `footer`, and `single`. Theme Builder templates **replace** the PHP header/footer/single-product layout when published.

1. **Templates → Theme Builder → Header → Add New**
   - Display condition: Entire Site
   - Include Oxiron logo, Primary menu, EN/DE/FR (WPML Language Switcher widget after WPML is on), Dark/Light is PHP-only — keep the PHP header if you need the theme toggle, or rebuild the toggle as HTML/JS in Elementor
   - Sticky on scroll is optional; the PHP header already becomes solid after 100px
2. **Theme Builder → Footer → Add New**
   - Entire Site
   - Columns: brand, catalog, company, contact + RFQ button
3. **Theme Builder → Single → Add New** (type: Product / `product` CPT)
   - Display condition: Products
   - Left: **Product Gallery** widget (Pro) bound to featured image + ACF `gallery`
   - Right: title, excerpt, ACF fields (material, surface, MOQ, lead time), JetBrains Mono spec table, RFQ button
   - Bottom: related products (Query Loop on `product`, same `material` or `application`)
4. Optional **Archive** template for `product` with **Taxonomy Filter** / Product Filter widgets pointing at `material`, `application`, and `size_range`. If you publish this archive template, it overrides `archive-product.php` (including the native AJAX filters). Prefer one or the other, not both.

### Product Gallery and Product Filter widgets

- Gallery: source = Featured Image + ACF Gallery field `gallery`. Enable zoom. Do not enable lightbox if you keep the PHP `<dialog>` zoom.
- Filter: query `product` CPT. Filter by taxonomy `material` and `application`. For size, filter by meta key `size_range` (`compact` / `standard` / `architectural`).

### Canvas vs theme

Use **Elementor Full Width** or default theme layout. Avoid Canvas on the Contact page until Fluent Forms is embedded, or the PHP fallback form disappears.

---

## 2. DMZ B2B Product Management

Install and activate the vendor zip. This plugin must **reuse** the existing `product` CPT (`rewrite` slug `products`) from `corten-core`. If the plugin offers “register a new product post type”, point it at `product` or disable its CPT registration so you do not get `/products-2/`.

### Enable modules

In the plugin settings, enable:

- Gallery
- Parameters / specifications
- Product attributes

### Field mapping

Map DMZ parameter keys onto the ACF / `corten-core` names already used by the theme:

| DMZ parameter | Oxiron field | Notes |
| --- | --- | --- |
| Gallery | `gallery` | Attachment IDs |
| Material | `material` + taxonomy `material` | Keep both in sync |
| Dimensions | `dimensions` repeater | label / value rows |
| Surface | `surface_finish` | Natural Rust / Powder Coated / Raw |
| Customization | `customization` | Size, Logo, Packaging, Color |
| MOQ | `moq` | Number |
| Lead time | `lead_time` | Text |
| Spec PDF | `download_spec_sheet` | File |
| Size class | `size_range` | compact / standard / architectural |

Applications stay on taxonomy `application` (`landscape`, `industrial`, `oem`). Categories stay on hierarchical `product_category`.

### Elementor widgets

1. Drop **Product Gallery** on the Single Product Theme Builder template.
2. Drop **Product Filter** on the products archive (or a landing page). Facets: material, application, size.
3. Confirm the filter query hits `post_type=product`. The theme REST route `GET /wp-json/corten/v1/products` remains available if you keep the PHP archive.

### Catalog hygiene

- One product = one `product` post. Do not duplicate SKUs as WooCommerce products unless you later add commerce.
- Featured image is the card image; gallery is detail zoom.
- MOQ and lead time must be filled — the RFQ dock on `single-product.php` reads them.

---

## 3. WPML

### Languages

1. Install **WPML Multilingual CMS**, **WPML String Translation**, **WPML Media**, plus **ACF Multilingual** and **SEO for WPML** (Rank Math).
2. Wizard:
   - Default language: **English**
   - Additional: **German**, **French**
   - URL format: **Different languages in directories**
   - Enable **Use directory for default language** so URLs are `/en/`, `/de/`, `/fr/`
   - Directory names: `en`, `de`, `fr`
3. After the wizard, **Settings → Permalinks → Save** twice.

### hreflang

WPML emits `hreflang` when directory mode is on. Confirm in page source:

```html
<link rel="alternate" hreflang="en" href="https://example.com/en/..." />
<link rel="alternate" hreflang="de" href="https://example.com/de/..." />
<link rel="alternate" hreflang="fr" href="https://example.com/fr/..." />
<link rel="alternate" hreflang="x-default" href="https://example.com/en/..." />
```

`x-default` should point at the English directory.

Replace the static EN/DE/FR links in `header.php` with the **WPML Language Switcher** (or keep the markup and hide it when the switcher is present). Theme Builder headers should use the WPML Language Switcher widget.

### What to translate

| Object | WPML screen | Notes |
| --- | --- | --- |
| Pages | WPML → Translation Management | Home, Capabilities, Contact, About, Resources |
| `product` | Make `product` translatable | Translate title, editor, excerpt, ACF fields |
| `project` | Make `project` translatable | Case studies |
| Taxonomies | `product_category`, `application`, `material` | Translate term names, keep slugs language-specific |
| Menus | WPML → Menu sync | One Primary menu per language |
| Fluent Forms | WPML + Fluent Forms multilingual | Duplicate the RFQ form per language or use form translation |
| Theme strings | String Translation | Text domain `corten-steel` |
| Plugin strings | String Translation | Text domain `corten-core` |
| Rank Math titles | WPML SEO | Per-language title and description |

### Compatibility switches

- **WPML → Settings → Custom XML Configuration**: ACF group `group_corten_product_details` should be translatable; file fields copy, gallery copy-or-translate.
- **Elementor**: WPML → Settings → Custom XML includes Elementor. Translate Theme Builder templates or use “use the same layout, translate strings”.
- **Rank Math**: enable WPML SEO so sitemaps split by language (`/en/sitemap_index.xml` pattern per WPML docs).
- **corten-core REST**: filters use slugs. Translated `material` terms get language-specific slugs; the archive filter labels must come from the current language.

### Language switcher in the industrial header

Style the switcher like `.lang-switch` in `main.css` (JetBrains Mono, rust color on the current language). Do not add flags — B2B EU/US sites use `EN DE FR` codes.

---

## 4. Rank Math SEO

1. Install **Rank Math SEO**. Connect Google Search Console when the production domain and SSL are live.
2. Setup wizard:
   - Site type: **Business / Organization**
   - Business name: Oxiron Fabrication (replace with the legal name)
   - Logo: square PNG/SVG on a dark background
   - Person vs Organization: **Organization**
3. **Rank Math → General Settings → Links**: force trailing slash to match permalinks. Enable breadcrumbs.

### Organization schema (homepage)

**Rank Math → Schema → Organization**:

- Name, legal name, URL `https://example.com/`
- Logo, image
- Address / area served: EU + North America (as you operate)
- `sameAs`: LinkedIn, Houzz, Architonic if they exist
- Contact: `sales@example.com`, `customer support` / `sales`

Homepage schema type: Organization. Do not mark the homepage as Product.

### Product schema (each product)

On every `product` post, Rank Math → Schema → Product:

- Name: post title
- Description: excerpt or Rank Math description
- SKU: optional internal code
- Brand: Oxiron
- Material: ACF `material`
- Images: featured + gallery
- Offers: do **not** invent a public price. Use `Offer` with `availability` = InStock / PreOrder and `url` = product permalink, or use a custom schema snippet for RFQ-only B2B (no price). If Rank Math requires a price, set `0` and hide it, or switch to a custom JSON-LD block that omits `price`. Prefer **no fake prices**.

Breadcrumb: Home → Products → Product title. Enable **Rank Math → General Settings → Breadcrumbs** and print `rank_math_the_breadcrumbs()` in Elementor or leave the PHP templates without crumbs until Theme Builder includes them.

### XML sitemap

**Rank Math → Sitemap Settings**:

- Include: Pages, `product`, `project`, `product_category`, `application`, `material`
- Exclude: `wp-admin`, author archives (unless you want them), empty taxonomies
- Images: include
- After WPML: language sitemaps per WPML SEO

Submit `https://example.com/sitemap_index.xml` (or `/en/sitemap_index.xml` with directories) in Google Search Console.

### Title and meta templates

**Rank Math → Titles & Meta**:

| Type | Title | Description |
| --- | --- | --- |
| Homepage | `Corten Steel Planters & Custom Sheet Metal \| Oxiron` | `Weathering-steel planters, equipment enclosures, and OEM sheet metal for landscape architects, studios, and industrial buyers in Europe and North America.` |
| Product | `%title% \| Corten Steel Manufacturer \| Oxiron` | `%excerpt%` (fallback: `Specify %title% in weathering steel. MOQ and lead time on the product page. RFQ for EU and US delivery.`) |
| Product archive | `Corten Steel Products \| Planters, Enclosures, OEM \| Oxiron` | `Filter planters, enclosures, and custom fabrication by material, application, and size.` |
| Project | `%title% \| Project \| Oxiron` | `%excerpt%` |
| Contact | `Request a Quote \| Oxiron Fabrication` | `Send drawings for Corten planters, enclosures, or custom sheet metal. EU dispatch and North American staging.` |

### Focus keywords (suggested)

Assign one primary keyword per URL. Do not stuff.

| URL | Primary keyword |
| --- | --- |
| Home | Corten steel planter manufacturer |
| Planter product | weathering steel planter supplier |
| Enclosure product | custom sheet metal enclosure |
| Custom fabrication product | OEM sheet metal fabrication |
| Capabilities | laser cutting bending welding |
| Products archive | Corten steel planter manufacturer |

Secondary terms to weave into copy (not as extra title tags): weathering steel planter, Corten flower box, architectural planter OEM, outdoor equipment housing.

robots.txt is owned by Rank Math when “Edit robots.txt” is enabled. Keep:

```
User-agent: *
Allow: /
Disallow: /wp-admin/
Allow: /wp-admin/admin-ajax.php
```

---

## 5. Fluent Forms

1. Install **Fluent Forms** (Pro if you need more than the free field set; File Upload exists on free for many versions — confirm).
2. **Fluent Forms → New Form** named `RFQ`.
3. The Contact template prints `[fluentform id="1"]` when the shortcode exists. Create this as the **first** form so the ID is `1`. If the ID is not `1`, change `page-contact.php` to the real ID.

### Fields (all required except file and target date)

| Label | Type | Name / admin key | Notes |
| --- | --- | --- | --- |
| Name | Text | `name` | Required |
| Company | Text | `company` | Required |
| Email | Email | `email` | Required, unique |
| Country | Select or Country | `country` | Required; default list EU + US/CA/UK |
| Product interest | Select | `product_interest` | Corten planters / Sheet-metal enclosures / Custom fabrication |
| Quantity | Number | `quantity` | Min 1 |
| Target date | Date | `target_date` | Optional |
| Message | Textarea | `message` | Required |
| File upload | File | `drawing` | Optional; `.pdf,.dwg,.step,.stp,.zip,.jpg,.png`; max size ≤ 32 MB (PHP and Nginx allow 256 MB) |

Submit label: **Send RFQ**. Success: “We received the drawings. Sales will reply with feasibility and lead time.”

### Notifications

**Settings → Email Notifications**:

- To: `sales@example.com` (replace)
- Subject: `[RFQ] {inputs.company} — {inputs.product_interest}`
- Body: all fields + attachment
- Reply-To: submitter email

**Confirmation email** (optional):

- To: `{inputs.email}`
- Subject: `Oxiron received your RFQ`
- No pricing promises

### Anti-spam

- Enable **hCaptcha or Cloudflare Turnstile** (not only reCAPTCHA if you sell into the EU)
- Enable Fluent Forms honeypot
- Akismet if you already run it
- Limit file types as above

### Embed

- Contact page: already `[fluentform id="1"]` in `page-contact.php`
- Product detail: in Elementor Single Product, add a Fluent Forms widget **or** keep the sticky RFQ dock linking to `/contact/?product={post_title}` and map that query argument into Product interest with Fluent Forms “URL parameter” default
- Shortcode anywhere: `[corten_rfq_button text="Get a Quote"]` still jumps to `/contact/`

WPML: translate the form with Fluent Forms multilingual, or duplicate DE/FR forms and assign them on translated Contact pages.

---

## 6. WP Super Cache + Redis Object Cache

### Redis Object Cache

`WP_REDIS_HOST` and `WP_REDIS_PORT` are already written into `wp-config.php` from Compose (`redis` / `6379`). Redis is on the internal `backend` network and is not published to the host.

1. Install **Redis Object Cache**.
2. **Settings → Redis**: Enable Object Cache.
3. Status must show **Connected**. Diagnostics: `PONG` via:

```powershell
docker compose exec redis redis-cli ping
```

4. Do not define a Redis password unless you also set `requirepass` in Compose. If you add a password later, set `WP_REDIS_PASSWORD` through Compose `WORDPRESS_CONFIG_EXTRA`, never hard-code it.

Optional constants (add via Compose extra, not a committed `wp-config.php`):

```php
define( 'WP_REDIS_DATABASE', 0 );
define( 'WP_CACHE_KEY_SALT', 'oxiron:' );
```

### WP Super Cache

1. Install **WP Super Cache**.
2. **Settings → WP Super Cache → Easy**: Caching On.
3. **Advanced**:
   - Cache Delivery: **Simple** (mod_rewrite is Apache-only; this stack is Nginx)
   - Cache timeout: 3600 seconds; scheduler: 600
   - Compress pages: on (Nginx also gzips)
   - 304 support: on
   - Cache HTTP headers: on
   - Late init: off unless a plugin requires it
4. **Preload**: optional, off until the catalog is stable.

### Exclusions

**WP Super Cache → Advanced → Rejected URL strings** / **Rejected User Agents**:

- `wp-admin`
- `wp-login.php`
- `xmlrpc.php`
- `contact` (RFQ page — never cache POSTs or nonce-bearing forms)
- `fluentform` / `admin-ajax.php` (already uncached for POST)
- Logged-in users: **Don’t cache pages for known users** = on

Accepted filenames: leave default. Do not cache `wp-json` GET catalog if you rely on live filters; the archive AJAX uses `admin-ajax.php` which is not page-cached.

After enabling, load `/` logged out twice and confirm a cache hit in the response headers (`WP-Super-Cache: Served supercache file` or equivalent). Purge all caches after theme/plugin deploys:

```powershell
docker compose exec wordpress php -r "require '/var/www/html/wp-load.php'; if (function_exists('wp_cache_clear_cache')) { wp_cache_clear_cache(); echo 'page-cache-cleared'; }"
```

---

## 7. ShortPixel

1. Install **ShortPixel Image Optimizer**.
2. Paste the API key (**Settings → ShortPixel**).
3. Compression: **Glossy** or **Lossy** for product photos; **Lossless** for logos and line diagrams.
4. **WebP**: Create WebP images = on. Serve WebP via ShortPixel (or Nginx `map` later). AVIF optional.
5. Resize huge uploads: max width 2560 px.
6. Backup originals: on, until you confirm quality.
7. **Bulk ShortPixel**: run on Media Library after the first product photos land. Do not bulk-compress the SVG sample illustrations unless you replace them with photos.

### File naming

Before upload, rename on disk (English, hyphen, no spaces):

```
corten-steel-planter-square-600mm.webp
weathering-steel-enclosure-1200mm.webp
oem-sheet-metal-bracket-custom.webp
```

Pattern: `{material-or-process}-{product}-{salient-dimension}.webp`

### Alt text

- Descriptive, not keyword dumps
- Example: `Corten steel square planter 600 mm with folded corners in a courtyard`
- Do not start every alt with “Corten steel planter manufacturer”
- Decorative SVG textures in the theme already use empty `alt=""` — leave them

---

## 8. Wordfence

1. Install **Wordfence Security**. Complete the wizard (free is enough to start; Premium for country blocking if you need it).
2. **Wordfence → Login Security**:
   - Enable 2FA for Administrators (TOTP). Do this before exposing the site.
   - Strong password enforcement
   - CAPTCHA on login (Turnstile/hCaptcha preferred)
3. **Brute Force**:
   - Lock out after 5 failures / 4 hours (Nginx already rate-limits `wp-login.php` at 5r/m)
   - Immediate lockout on invalid username
4. **Firewall**:
   - Protection level: **Enabled and Protecting**
   - Learning mode: 7 days on a new site, then turn off
   - Block: known malicious IPs, PHP execution in uploads (theme/nginx already deny `.php` under uploads)
5. **Scan**:
   - Schedule daily
   - Scan for: core changes, malware signatures, vulnerable plugins
   - Email results to the technical inbox
6. Disable XML-RPC if you do not need the app. Nginx also rate-limits `xmlrpc.php`.
7. Hide WordPress version is optional; `server_tokens off` is already set in Nginx.

Never whitelist `/wp-admin/` from the firewall. Do not email yourself the recovery code into a public ticket.

---

## 9. UpdraftPlus

1. Install **UpdraftPlus**.
2. **Settings → UpdraftPlus Backups → Settings**:
   - Files: **Weekly** (full site: plugins, themes, uploads)
   - Database: **Daily**
   - Retention: 14 database copies, 8 full-file copies (adjust to storage budget)
3. Remote storage: **Google Drive** or **Amazon S3** (S3 preferred for EU data residency if you need a Frankfurt/Paris bucket). Authenticate the remote. Test with “Send a backup now” → Database only.
4. Encrypt database backups if the remote is a shared Drive.
5. Exclude: `wp-content/cache`, `wp-content/updraft` (self), `node_modules` if any.

### Restore

1. **UpdraftPlus → Backup / Restore**
2. Restore database first, then plugins, themes, uploads
3. If the domain changed, run a search-replace with a dedicated tool (do not hand-edit serialized PHP)
4. **Settings → Permalinks → Save**
5. Redis: **Settings → Redis → Flush**
6. WP Super Cache: Delete Cache
7. Visit `/contact/` and send a test RFQ

Also keep the Compose volume dumps documented in `README.md` as a second restore path (`corten-steel_wp_data`, `corten-steel_db_data`).

---

## Post-config checklist

- [ ] ACF Product Details visible on a product
- [ ] Elementor globals use rust / copper / CTA hex values
- [ ] Theme Builder Header/Footer/Single Product published or PHP templates still used
- [ ] DMZ (if licensed) mapped to CPT `product`, no second archive slug
- [ ] WPML `/en/` `/de/` `/fr/` and hreflang in view-source
- [ ] Rank Math Organization on home, Product schema on a product, sitemap submitted
- [ ] Fluent Forms RFQ ID matches `page-contact.php`
- [ ] Test RFQ email received; confirmation received
- [ ] Redis Object Cache: Connected
- [ ] Super Cache: logged-out homepage is cached; `/contact/` is not
- [ ] ShortPixel WebP on a real product photo
- [ ] Wordfence 2FA on administrator
- [ ] UpdraftPlus: one successful remote database backup restored to a staging copy

Continue with `docs/seo-performance-security.md` for SEO, performance targets, security, and the go-live checklist.
