#!/bin/sh
# Remove the image default site and ensure TLS files exist before Nginx starts.
# Let's Encrypt certificates replace the temporary self-signed pair after issuance.
set -e

rm -f /etc/nginx/conf.d/default.conf

DOMAIN="${DOMAIN:-example.com}"
LIVE_DIR="/etc/letsencrypt/live/${DOMAIN}"

if [ -f "${LIVE_DIR}/fullchain.pem" ] && [ -f "${LIVE_DIR}/privkey.pem" ]; then
    exit 0
fi

echo "No Let's Encrypt certificate found for ${DOMAIN}. Generating a temporary self-signed certificate."
mkdir -p "${LIVE_DIR}"

if ! command -v openssl >/dev/null 2>&1; then
    if command -v apk >/dev/null 2>&1; then
        apk add --no-cache openssl >/dev/null
    else
        echo "openssl is not available. Place fullchain.pem and privkey.pem in ${LIVE_DIR}."
        exit 1
    fi
fi

openssl req -x509 -nodes -newkey rsa:2048 \
    -days 1 \
    -keyout "${LIVE_DIR}/privkey.pem" \
    -out "${LIVE_DIR}/fullchain.pem" \
    -subj "/CN=${DOMAIN}"
