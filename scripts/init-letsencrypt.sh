#!/bin/sh
# Issue a Let's Encrypt certificate via the Certbot webroot authenticator.
# Usage (from the project root, Git Bash or WSL):
#   cp .env.example .env   # then edit DOMAIN, CERTBOT_EMAIL, and passwords
#   docker compose up -d
#   sh scripts/init-letsencrypt.sh
set -eu

if [ ! -f .env ]; then
    echo "Missing .env. Copy .env.example to .env and set DOMAIN plus CERTBOT_EMAIL."
    exit 1
fi

# shellcheck disable=SC1091
. ./.env

if [ -z "${DOMAIN:-}" ] || [ -z "${CERTBOT_EMAIL:-}" ]; then
    echo "DOMAIN and CERTBOT_EMAIL must be set in .env."
    exit 1
fi

if [ "${DOMAIN}" = "example.com" ]; then
    echo "Refusing to request a certificate for example.com. Set DOMAIN to a real hostname you control."
    exit 1
fi

echo "Removing temporary self-signed files for ${DOMAIN} (if present)..."
rm -rf "./certbot/conf/live/${DOMAIN}" \
       "./certbot/conf/archive/${DOMAIN}" \
       "./certbot/conf/renewal/${DOMAIN}.conf"

echo "Requesting Let's Encrypt certificate for ${DOMAIN} and www.${DOMAIN}..."
docker compose run --rm --entrypoint certbot certbot certonly \
    --webroot \
    --webroot-path=/var/www/certbot \
    --email "${CERTBOT_EMAIL}" \
    --agree-tos \
    --no-eff-email \
    --force-renewal \
    -d "${DOMAIN}" \
    -d "www.${DOMAIN}"

echo "Reloading Nginx..."
docker compose exec nginx nginx -s reload

echo "Certificate issued. Visit https://${DOMAIN}/"
