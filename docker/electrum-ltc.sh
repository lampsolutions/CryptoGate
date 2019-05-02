#!/bin/bash

set -e

PUBKEY=$LTC_PUBKEY
MODE=$LTC_MODE
ELECTRUM_FORK=electrum-ltc
ELECTRUM_BINARY="/opt/$ELECTRUM_FORK/run_electrum"
ELECTRUM_DATA_DIR_CMD="/data/$ELECTRUM_FORK/"
ELECTRUM_START_OPTS=""

function render_template() {
    cat /opt/$ELECTRUM_FORK.conf.default | jq ".rpcpassword = \"${ELECTRUM_RPC_PASSWORD}\""
}

# Do not run if no pubkey is set
if [ -z "$PUBKEY" ]; then
    sleep 30
    exit 0
fi

if [ $MODE == "testnet" ]; then
    ELECTRUM_DATA_DIR="/data/$ELECTRUM_FORK/testnet"
    ELECTRUM_WALLET_DIR="$ELECTRUM_DATA_DIR/wallets"
    ELECTRUM_WALLET="$ELECTRUM_WALLET_DIR/default_wallet"
    ELECTRUM_OPTS="--dir $ELECTRUM_DATA_DIR_CMD --testnet -v -w $ELECTRUM_WALLET"
else
    ELECTRUM_DATA_DIR="/data/$ELECTRUM_FORK"
    ELECTRUM_WALLET_DIR="$ELECTRUM_DATA_DIR/wallets"
    ELECTRUM_WALLET="$ELECTRUM_WALLET_DIR/default_wallet"
    ELECTRUM_OPTS="--dir $ELECTRUM_DATA_DIR_CMD -v -w $ELECTRUM_WALLET"
fi

# Check if config dir exists and create if not
if [ ! -d $ELECTRUM_DATA_DIR ]; then
    mkdir -p $ELECTRUM_DATA_DIR
fi

# Check if default config exists
if [ ! -f $ELECTRUM_DATA_DIR/config ]; then
    render_template > $ELECTRUM_DATA_DIR/config
fi

# Check if data request storage symlink exists and create if not
if [ ! -h /app/public/$ELECTRUM_FORK-req ]; then
    ln -s /data/$ELECTRUM_FORK-req /app/public/$ELECTRUM_FORK-req
fi

# Check if data request storage exists and create if not
if [ ! -d /data/$ELECTRUM_FORK-req ]; then
    mkdir -p /data/$ELECTRUM_FORK-req
fi

# Check if data request storage exists and create if not
if [ ! -f /data/$ELECTRUM_FORK-req/index.html ]; then
    echo "" > /data/$ELECTRUM_FORK-req/index.html
fi

# Check if wallet dir exists
if [ ! -d $ELECTRUM_WALLET_DIR ]; then
    mkdir -p $ELECTRUM_WALLET_DIR
fi

# Check if wallet exists
if [ ! -f $ELECTRUM_WALLET ]; then
    $ELECTRUM_BINARY restore $PUBKEY $ELECTRUM_OPTS --offline
fi

load_wallet(){
  sleep 15
  $ELECTRUM_BINARY daemon load_wallet $ELECTRUM_OPTS
}

load_wallet &

# Start daemon
$ELECTRUM_BINARY daemon $ELECTRUM_OPTS $ELECTRUM_START_OPTS