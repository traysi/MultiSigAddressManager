#!/usr/bin/php
<?php
// Copyright (c) 2019 Traysi Hylian, MinerMore.com
// Distributed under the MIT software license, see the accompanying
// file LICENSE or http://www.opensource.org/licenses/mit-license.php.

include("rpc.php");
include("config.php");

error_reporting(E_ERROR | E_WARNING | E_PARSE);

if (! $config['multisig']) { die("You must edit the config.php file first.\n"); }

$rpc = new Bitcoin($config['rpcuser'],$config['rpcpassword'],$config['rpcbind'],$config['rpcport']);

if ($argv[2]) {
  $priv_key[] = $argv[2];
} else {
  die("Usage: $argv[0] <transaction_file> <private key>\n");
}

if (file_exists($argv[1])) {
  $transactions = explode("\n", file_get_contents($argv[1]));
} else {
  die("Error: file not found $argv[1]\n");
}

if(is_array($transactions)) {
  while (list($k,$tx_hex)=each($transactions)) {
    if (! $tx_hex) continue;

    $info = $rpc->decoderawtransaction($tx_hex);
    
    if(is_array($info[vin])) {
      while (list($a,$b)=each($info[vin])) {
        $info[vin][$a]['scriptPubKey'] = $config['scriptPubKey'];
        $info[vin][$a]['redeemScript'] = $config['redeemScript'];
      }
    }

    $signed = $rpc->signrawtransaction($tx_hex,$info[vin],$priv_key);
    if (($signed[hex] != $tx_hex) || ($signed[complete])) {
      print "$signed[hex]\n"; 
    }
  }
}

