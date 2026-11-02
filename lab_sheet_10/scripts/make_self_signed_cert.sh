#!/usr/bin/env bash
# Q18: create a self-signed certificate for myproject.local (valid 1 year).
set -euo pipefail
DIR="${1:-/etc/ssl/myproject}"
sudo mkdir -p "$DIR"
sudo openssl req -x509 -nodes -newkey rsa:2048 -days 365 \
  -keyout "$DIR/myproject.local.key" \
  -out    "$DIR/myproject.local.crt" \
  -subj "/C=IN/ST=State/L=City/O=MyProject/CN=myproject.local" \
  -addext "subjectAltName=DNS:myproject.local"
sudo chmod 600 "$DIR/myproject.local.key"
echo "Created $DIR/myproject.local.crt and .key"
echo "Next: sudo a2enmod ssl rewrite headers && sudo a2ensite myproject.local && sudo systemctl restart apache2"
