***

name: "auto-deploy"
description: "Deploy website updates to Aliyun ECS via Git push auto-deployment. Invoke when user finishes modifying the website and wants to push updates to the production server."
-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

# Auto Deploy to Aliyun ECS

Push local WordPress theme changes to the production Aliyun ECS server via Git.
The server has a bare Git repo with a `post-receive` hook that automatically
checks out files into the live theme directory and restarts containers.

## Server Details

* ECS IP: `47.239.52.41`

* SSH user: `root`

* Remote repo: `/opt/git/webapp.git`

* Live theme path: `/opt/electrix/wordpress/wp-content/themes/electrix-geo/`

* Docker compose: `/opt/electrix/docker-compose.prod.yml` (project: `electrix-geo-site`)

* Site URL: `http://47.239.52.41:8080/`

## One-Time Setup (already done — skip if repo exists)

### 1. SSH Key (Windows PowerShell)

```powershell
ssh-keygen -t ed25519 -C "sales@heshanpower.com"
type $env:USERPROFILE\.ssh\id_ed25519.pub | ssh root@47.239.52.41 "cat >> ~/.ssh/authorized_keys"
```

### 2. Server-Side Bare Repo + Post-Receive Hook

```bash
ssh root@47.239.52.41
mkdir -p /opt/git && cd /opt/git
git init --bare webapp.git
```

Create `/opt/git/webapp.git/hooks/post-receive`:

```bash
#!/bin/bash
set -e
GIT_DIR=/opt/git/webapp.git
WORK_TREE=/tmp/webapp-checkout
THEME_DEST=/opt/electrix/wordpress/wp-content/themes/electrix-geo
COMPOSE_FILE=/opt/electrix/docker-compose.prod.yml
PROJECT=electrix-geo-site

rm -rf "$WORK_TREE"
mkdir -p "$WORK_TREE"
git --work-tree="$WORK_TREE" --git-dir="$GIT_DIR" checkout -f

# Sync theme files
rsync -a --delete "$WORK_TREE/wordpress/wp-content/themes/electrix-geo/" "$THEME_DEST/"

# Sync nginx config if changed
if [ -f "$WORK_TREE/nginx/conf/default.conf" ]; then
  cp "$WORK_TREE/nginx/conf/default.conf" /opt/electrix/nginx/conf/default.conf
fi

# Sync functions.php and other PHP if needed (theme is bind-mounted, live immediately)
# Restart nginx to pick up config changes
cd /opt/electrix
docker compose -f "$COMPOSE_FILE" -p "$PROJECT" restart nginx 2>/dev/null || true

echo "Deploy complete."
```

```bash
chmod +x /opt/git/webapp.git/hooks/post-receive
```

### 3. Local Git Remote (Windows)

```powershell
cd "d:\first-cc\electrix-geo-site - 副本"
git init
git remote add deploy ssh://root@47.239.52.41/opt/git/webapp.git
```

## Daily Workflow

After modifying website files (theme PHP, CSS, JS, nginx config, etc.):

```powershell
cd "d:\first-cc\electrix-geo-site - 副本"

# Stage and commit changes
git add -A
git commit -m "describe changes here"

# Push to server (triggers auto-deploy)
git push deploy master
```

The `post-receive` hook on the server will:

1. Check out files to `/tmp/webapp-checkout`
2. `rsync` theme files to the live directory (bind-mounted, takes effect immediately)
3. Copy nginx config if changed and restart nginx

## What Gets Deployed

| Path                                          | Destination                                               | Effect                                  |
| --------------------------------------------- | --------------------------------------------------------- | --------------------------------------- |
| `wordpress/wp-content/themes/electrix-geo/**` | `/opt/electrix/wordpress/wp-content/themes/electrix-geo/` | Live immediately (bind mount)           |
| `nginx/conf/default.conf`                     | `/opt/electrix/nginx/conf/default.conf`                   | Requires nginx restart (hook does this) |

## What Does NOT Get Deployed via Git

* **Database changes** (products, pages, settings) — manage via wp-admin at `http://47.239.52.41:8080/wp-admin/`

* **Uploaded media** (images in `uploads/`) — manage via wp-admin or manual `scp`

* **wp-config.php** — managed separately on the server for security

## Troubleshooting

### SSH connection refused

```powershell
ssh root@47.239.52.41 "echo ok"
```

If this fails, check Aliyun security group allows SSH (port 22).

### Push rejected (non-fast-forward)

```powershell
git push deploy master --force
```

Use `--force` only when you know the server repo has no independent commits.

### Site not updating after push

```bash
ssh root@47.239.52.41
# Check if files arrived
ls -la /opt/electrix/wordpress/wp-content/themes/electrix-geo/
# Check hook log
cat /opt/git/webapp.git/hooks/post-receive.log 2>/dev/null
# Manual restart
cd /opt/electrix
docker compose -f docker-compose.prod.yml -p electrix-geo-site restart nginx wordpress
```

### Reset admin password

```bash
ssh root@47.239.52.41
cd /opt/electrix
docker compose -f docker-compose.prod.yml -p electrix-geo-site exec wordpress \
  php -r "require 'wp-load.php'; wp_set_password('NEW_PASSWORD', get_user_by('login','heshandianqi')->ID);"
```

