#!/usr/bin/env bash

function render_template() {
    local template="$(cat .env.tpl)"
    eval "echo \"${template}\""
}

if [ ! -f certs/key.pem ]; then
    echo "Generating self signed test certicate"
    openssl req -nodes -newkey rsa:4096 -keyout certs/key.pem -out certs/cert.csr -subj "/C=DE/ST=Crypto/L=Crypto/O=Crypto/OU=Crypto/CN=crypto.crypto"
    openssl x509 -req -days 3650 -in certs/cert.csr -signkey certs/key.pem -out certs/cert.pem
fi

echo -n "Please enter your Bitcoin Master Public Key [ENTER]: "
read BTC_PUBKEY

if [ ! -f .envs ]; then
    echo "Generating .env file"
    APP_KEY=$(openssl rand -base64 32)
    API_TOKEN_MERCHANT=$(openssl rand -base64 32)
    API_TOKEN_ADMIN=$(openssl rand -base64 32)
    ELECTRUM_RPC_PASSWORD=$(openssl rand -base64 32)
    render_template > .env
fi