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

$multisig = $config['multisig'];
$address[] = $multisig;
$coins = $rpc->listunspent(1,99999999,$address);
if(! is_array($coins)) die("Error: unable to connect to the coin daemon.");

$sendto = $argv[1];
$amount = $argv[2];
$dry_run = $argv[3];

if (! $amount) {
  die("Usage: $argv[0] <recipient> <amount> <dry-run>\n");
}

if (! $rpc->validateaddress($sendto)) {
  die("Error: $sendto is an invalid address.\n");
}

$vins = array();

$sum = 0;
$x = 0;
if(is_array($coins)) {
  while (list($k,$v)=each($coins)) {
    if ($v['confirmations'] < $config['confirmations']) continue;
    if ($v['amount'] < $config['consolidate_amount']) continue;

    if ($sum < $amount) {
      $sum = $sum + $v['amount'];

      $o['txid'] = $v['txid'];
      $o['vout'] = $v['vout'];
      $o['scriptPubKey'] = $config['scriptPubKey'];
      $o['redeemScript'] = $config['redeemScript'];
  
      $vins[] = $o;
      $x++;
    }
  }
}

$change = $sum - $amount - $config['tx_fee'];


if(is_array($vins)) {
  $recipient[$sendto] = $amount;
  $recipient[$multisig] = $change;
  $tx = $rpc->createrawtransaction($vins,$recipient);

  if ($dry_run) {
    $decode = $rpc->decoderawtransaction($tx);
    print_r($decode);
  } else {
    print "$tx\n";
  }
}

if ($dry_run) {
  print "Total being spent: $sum\n";
  print "Amount for $sendto: $amount\n";
  print "Change left: $change\n";
}
