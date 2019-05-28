#!/usr/bin/php
<?php
// Copyright (c) 2019 Traysi Hylian, MinerMore.com
// Distributed under the MIT software license, see the accompanying
// file LICENSE or http://www.opensource.org/licenses/mit-license.php.

include("rpc.php");
include("config.php");

error_reporting(E_ERROR | E_WARNING | E_PARSE);

$rpc = new Bitcoin($config['rpcuser'],$config['rpcpassword'],$config['rpcbind'],$config['rpcport']);

if (! $argv[1]) {
  die("Usage: $argv[0] <signed_transactions_file>\n");
}

if (file_exists($argv[1])) {
  $transactions = explode("\n", file_get_contents($argv[1]));
} else {
  die("Error: file not found $argv[1]\n");
}


if(is_array($transactions)) {
  while (list($k,$tx_hex)=each($transactions)) {
    if (! $tx_hex) continue;
    $info = $rpc->sendrawtransaction($tx_hex);
    print "TX ID: $info\n";

    // Here we will wait 1 second before proceeding. We do this to protect the chain from being flooded
    // with transactions. If you want to do the flood, just comment out the line below.
    sleep(1);
  }
}

