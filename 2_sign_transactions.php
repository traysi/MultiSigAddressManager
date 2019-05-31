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
  fwrite(STDERR, "Usage: $argv[0] <transaction_file> <private key>" . PHP_EOL);
  die();
}

if (file_exists($argv[1])) {
  $transactions = explode("\n", file_get_contents($argv[1]));
} else {
  fwrite(STDERR, "Error: File not found $argv[1]" . PHP_EOL);
  die();
}

$vin_count = 0;
if(is_array($transactions)) {
  while (list($k,$tx_hex)=each($transactions)) {
    if (! $tx_hex) continue;

    $info = $rpc->decoderawtransaction($tx_hex);
    
    if(is_array($info[vin])) {
      while (list($a,$b)=each($info[vin])) {
        $vin_count++;
        $info['vin'][$a]['scriptPubKey'] = $config['scriptPubKey'];
        $info['vin'][$a]['redeemScript'] = $config['redeemScript'];
      }
    }

    $signed = $rpc->signrawtransaction($tx_hex,$info[vin],$priv_key);
    if ($signed['hex'] != $tx_hex) {
      print "$signed[hex]\n"; 
    } else {
      fwrite(STDERR, "FATAL: There was an error signing." . PHP_EOL);
    }
  }
} else {
  fwrite(STDERR, "FATAL: The input file $argv[1] contains no valid transactions." . PHP_EOL);
}
  

if (! $vin_count) {
  fwrite(STDERR, "FATAL: Unable to find transactions to sign." . PHP_EOL);
}
