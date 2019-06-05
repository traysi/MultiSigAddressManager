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
$spend_amount = $argv[2];
$dry_run = $argv[3];

if (! $spend_amount) {
  die("Usage: $argv[0] <recipient> <amount> <dry-run>\n");
}

if (! $rpc->validateaddress($sendto)) {
  die("Error: $sendto is an invalid address.\n");
}

$vins = array();

if(is_array($coins)) {
  while (list($k,$v)=each($coins)) {
    if ($v['amount'] <= $config['consolidate_amount']) continue;
    if ($v['confirmations'] < $config['confirmations']) continue;

    $txid = $v['txid'];
    $vout = $v['vout'];
    $newcoins[$txid][$vout] = $v['amount'];
  }
}

$sum = 0;
$x = 0;

if(is_array($newcoins)) {
  arsort($newcoins); // Sort by value
  while (list($txid,$v)=each($newcoins)) {
    if(is_array($v)) {
      while (list($vout,$amount)=each($v)) {
        if ($sum < $spend_amount) {
          $sum = $sum + $amount;
          $sum = sprintf('%.8f', round($sum, 8, PHP_ROUND_HALF_DOWN));
    
          $o['txid'] = $txid;
          $o['vout'] = $vout;
          $o['scriptPubKey'] = $config['scriptPubKey'];
          $o['redeemScript'] = $config['redeemScript'];

          $vins[] = $o;
          $x++;
        }
      }
    }
  }
}

$change = $sum - $spend_amount - $config['tx_fee'];
if ($change > 0) {
  if(is_array($vins)) {
    $recipient[$sendto] = sprintf('%.8f', round($spend_amount, 8, PHP_ROUND_HALF_DOWN));
    $recipient[$multisig] = sprintf('%.8f', round($change, 8, PHP_ROUND_HALF_DOWN));

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
    print "Amount to $sendto: $spend_amount\n";
    print "Change left: $change\n";
  }
} else {
  print "Fatal error: There aren't enough confirmed coins in this wallet to send $amount to $sendto.\n";
}
