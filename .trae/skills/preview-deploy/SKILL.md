---
name: "preview-deploy"
description: "Website modification workflow: modify → preview in local browser → user confirms → deploy to server. Invoke when user wants to modify the website and review before deploying."
---

# Preview-Deploy Workflow

This skill defines the standard workflow for website modifications: **Modify → Preview → Confirm → Deploy**.

## Workflow Steps

### Step 1: Modify (改进)

1. Read and understand the target files (PHP templates, CSS, JS).
2. Make code changes in the `/deploy` directory.
3. Bump `ELECTRIX_VERSION` in `functions.php` to force cache refresh.
4. Summarize the changes for the user (what was modified, design highlights).

### Step 2: Preview (请求预览)

Since the website runs on WordPress (PHP + MySQL), theme files cannot be rendered locally without a server. Create a **standalone HTML preview file** instead:

1. Create a file named `preview-<section>.html` in the project root (e.g., `preview-about.html`).
2. The preview file should:
   - Include all relevant CSS (inline in `<style>`) from the changes — copy the exact CSS rules from `main.css` and/or `critical.css`.
   - Include mock HTML structure that mirrors the actual PHP template output (same classes, same element hierarchy).
   - Use placeholder content that matches the real content (titles, paragraphs, list items).
   - For images, use the text-to-image API: `https://trae-api-cn.mchost.guru/api/ide/v1/text_to_image?prompt={prompt}&image_size=landscape_16_9` (URL-encode the prompt).
   - Include a simple mock header/nav to provide visual context.
   - Be fully responsive — include the same `@media` breakpoints as the real CSS.
3. Open the preview file in the user's default browser:
   ```powershell
   Start-Process "path\to\preview-file.html"
   ```
4. Ask the user to review the preview and confirm whether to deploy.

### Step 3: Confirm (同意)

Wait for user confirmation:
- If the user **approves**: proceed to Step 4 (Deploy).
- If the user requests **adjustments**: go back to Step 1, modify the code, regenerate the preview, and ask again.
- If the user says deploy without asking for preview: skip Step 2 and go directly to Step 4.

### Step 4: Deploy (部署)

Deploy changed files to the Aliyun ECS server:

1. **SCP transfer** — upload modified files to the server:
   ```powershell
   scp -i "C:\Users\Administrator\.ssh\id_ed25519" -o StrictHostKeyChecking=no "<local-path>" root@47.239.52.41:/opt/electrix/wordpress/wp-content/themes/electrix-geo/<remote-path>
   ```
   Common files to deploy:
   - `assets/css/main.css`
   - `assets/css/critical.css`
   - `assets/js/main.js`
   - `functions.php`
   - Template files (`front-page.php`, `header.php`, `footer.php`, `page-about.php`, `page-contact.php`, etc.)

2. **Restart WordPress container**:
   ```powershell
   ssh -i "C:\Users\Administrator\.ssh\id_ed25519" -o StrictHostKeyChecking=no root@47.239.52.41 "cd /opt/electrix && docker compose -f docker-compose.prod.yml -p electrix-geo-site restart wordpress"
   ```

3. **Verify deployment** — wait ~8 seconds, then check the version number:
   ```powershell
   $r = Invoke-WebRequest -Uri "http://47.239.52.41:8080/" -UseBasicParsing -TimeoutSec 30
   # Look for ver=X.X.X in the HTML source
   ```

4. **Report to user** — confirm the version is live, remind them to use Ctrl+F5 to refresh.

## Key Rules

- **Always create a preview before deploying** unless the user explicitly says to deploy directly.
- **The preview HTML must use the exact same CSS** as the actual changes — do not simplify or approximate styles.
- **Clean up preview files** after deployment is confirmed (delete the `preview-*.html` file).
- **Version bump is mandatory** — always increment `ELECTRIX_VERSION` to bypass browser caching.
- **Deploy all modified files** — don't forget to SCP every file that was changed (CSS, JS, PHP templates).
